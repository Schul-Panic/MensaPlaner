<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = self::connectToPostgres() ?? self::createMockConnection();
        }

        return self::$connection;
    }

    private static function connectToPostgres(): ?PDO
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT') ?: '5432';
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        if (!$host || !$dbname) {
            return null;
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};connect_timeout=3";

        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $exception) {
            error_log('MensaPlaner: PostgreSQL nicht erreichbar, verwende Mock-Daten (' . $exception->getMessage() . ')');

            return null;
        }
    }

    private static function createMockConnection(): PDO
    {
        // Datei statt :memory:, damit die Mock-Daten (z.B. neue Bestellungen) über
        // mehrere Requests hinweg erhalten bleiben. Löschen setzt den Stand zurück.
        $path = sys_get_temp_dir() . '/mensaplaner_mock.sqlite';
        $isNew = !file_exists($path);

        $pdo = new PDO("sqlite:{$path}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Postgres-Funktion NOW(), die u.a. Vote::cast() nutzt, unter SQLite nachbilden.
        @$pdo->sqliteCreateFunction('NOW', fn () => date('Y-m-d H:i:s'));

        // SQLite ignoriert Fremdschlüssel (inkl. ON DELETE CASCADE) ohne dieses Pragma.
        $pdo->exec('PRAGMA foreign_keys = ON');

        if ($isNew) {
            self::createMockSchema($pdo);
            self::seedMockData($pdo);
        }

        return $pdo;
    }

    private static function createMockSchema(PDO $pdo): void
    {
        $pdo->exec('
            CREATE TABLE accounts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT \'student\',
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $pdo->exec('
            CREATE TABLE orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                account_id INTEGER NOT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $pdo->exec('
            CREATE TABLE order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                dish_name TEXT NOT NULL,
                price TEXT NOT NULL,
                quantity INTEGER NOT NULL
            )
        ');

        $pdo->exec('
            CREATE TABLE votes (
                dish_id INTEGER NOT NULL,
                account_id INTEGER NOT NULL,
                direction TEXT NOT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE (dish_id, account_id)
            )
        ');

        $pdo->exec('
            CREATE TABLE dishes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category TEXT NOT NULL,
                variant TEXT NOT NULL,
                name TEXT NOT NULL,
                price TEXT NOT NULL
            )
        ');

        $pdo->exec('
            CREATE TABLE menu_slots (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                week TEXT NOT NULL,
                day TEXT NOT NULL,
                category TEXT NOT NULL,
                variant TEXT NOT NULL,
                dish_id INTEGER NOT NULL,
                UNIQUE (week, day, category, variant),
                FOREIGN KEY (dish_id) REFERENCES dishes (id) ON DELETE CASCADE
            )
        ');

        $pdo->exec('
            CREATE TABLE allergens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE
            )
        ');

        $pdo->exec('
            CREATE TABLE dish_allergens (
                dish_id INTEGER NOT NULL,
                allergen_id INTEGER NOT NULL,
                UNIQUE (dish_id, allergen_id),
                FOREIGN KEY (dish_id) REFERENCES dishes (id) ON DELETE CASCADE,
                FOREIGN KEY (allergen_id) REFERENCES allergens (id) ON DELETE CASCADE
            )
        ');
    }

    private static function seedMockData(PDO $pdo): void
    {
        self::seedDishes($pdo);

        $passwordHash = password_hash('test1234', PASSWORD_DEFAULT);

        $accountStatement = $pdo->prepare(
            'INSERT INTO accounts (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)'
        );
        $accounts = [
            ['name' => 'Anna Admin', 'email' => 'admin@mensa.local', 'role' => 'admin'],
            ['name' => 'Malik Mitarbeiter', 'email' => 'mitarbeiter@mensa.local', 'role' => 'mitarbeiter'],
            ['name' => 'Tobias Lehrer', 'email' => 'teacher@mensa.local', 'role' => 'teacher'],
            ['name' => 'Sina Schuster', 'email' => 'student1@mensa.local', 'role' => 'student'],
            ['name' => 'Paul Peters', 'email' => 'student2@mensa.local', 'role' => 'student'],
            ['name' => 'Gundula Gast', 'email' => 'gast@mensa.local', 'role' => 'gast'],
        ];

        foreach ($accounts as $account) {
            $accountStatement->execute([
                'name' => $account['name'],
                'email' => $account['email'],
                'password_hash' => $passwordHash,
                'role' => $account['role'],
            ]);
        }

        $studentId = (int) $pdo->query("SELECT id FROM accounts WHERE email = 'student1@mensa.local'")->fetchColumn();
        $secondStudentId = (int) $pdo->query("SELECT id FROM accounts WHERE email = 'student2@mensa.local'")->fetchColumn();
        $mitarbeiterId = (int) $pdo->query("SELECT id FROM accounts WHERE email = 'mitarbeiter@mensa.local'")->fetchColumn();

        $orderStatement = $pdo->prepare('INSERT INTO orders (account_id) VALUES (:account_id)');
        $itemStatement = $pdo->prepare(
            'INSERT INTO order_items (order_id, dish_name, price, quantity) VALUES (:order_id, :dish_name, :price, :quantity)'
        );

        $mockOrders = [
            [
                'account_id' => $studentId,
                'items' => [
                    ['name' => 'Schweineschnitzel mit Bratkartoffeln', 'price' => '4,10 €', 'quantity' => 2],
                    ['name' => 'Bratkartoffeln', 'price' => '1,50 €', 'quantity' => 1],
                    ['name' => 'Fruchtjoghurt', 'price' => '1,20 €', 'quantity' => 2],
                ],
            ],
            [
                'account_id' => $secondStudentId,
                'items' => [
                    ['name' => 'Kichererbsen-Curry mit Basmatireis', 'price' => '3,30 €', 'quantity' => 1],
                    ['name' => 'Gulasch mit Spätzle', 'price' => '4,00 €', 'quantity' => 3],
                    ['name' => 'Karamellpudding', 'price' => '1,20 €', 'quantity' => 3],
                ],
            ],
            [
                'account_id' => $mitarbeiterId,
                'items' => [
                    ['name' => 'Gulasch mit Spätzle', 'price' => '4,00 €', 'quantity' => 1],
                    ['name' => 'Spätzle', 'price' => '1,40 €', 'quantity' => 1],
                    ['name' => 'Hähnchen-Curry mit Reis', 'price' => '3,90 €', 'quantity' => 2],
                    ['name' => 'Zitronenmousse', 'price' => '1,40 €', 'quantity' => 2],
                ],
            ],
            [
                'account_id' => $studentId,
                'items' => [
                    ['name' => 'Hähnchen-Curry mit Reis', 'price' => '3,90 €', 'quantity' => 1],
                    ['name' => 'Reis', 'price' => '1,20 €', 'quantity' => 1],
                ],
            ],
        ];

        foreach ($mockOrders as $order) {
            $orderStatement->execute(['account_id' => $order['account_id']]);
            $orderId = (int) $pdo->lastInsertId();

            foreach ($order['items'] as $item) {
                $itemStatement->execute([
                    'order_id' => $orderId,
                    'dish_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        $voteStatement = $pdo->prepare(
            'INSERT INTO votes (dish_id, account_id, direction) VALUES (:dish_id, :account_id, :direction)'
        );
        $mockVotes = [
            ['dish_id' => 1, 'account_id' => $studentId, 'direction' => 'up'],
            ['dish_id' => 1, 'account_id' => $secondStudentId, 'direction' => 'up'],
            ['dish_id' => 2, 'account_id' => $studentId, 'direction' => 'down'],
        ];

        foreach ($mockVotes as $vote) {
            $voteStatement->execute($vote);
        }
    }

    private static function seedDishes(PDO $pdo): void
    {
        $dishStatement = $pdo->prepare(
            'INSERT INTO dishes (category, variant, name, price) VALUES (:category, :variant, :name, :price)'
        );
        $slotStatement = $pdo->prepare(
            'INSERT INTO menu_slots (week, day, category, variant, dish_id) VALUES (:week, :day, :category, :variant, :dish_id)'
        );
        $allergenStatement = $pdo->prepare(
            'INSERT OR IGNORE INTO allergens (name) VALUES (:name)'
        );
        $allergenLookupStatement = $pdo->prepare('SELECT id FROM allergens WHERE name = :name');
        $linkStatement = $pdo->prepare(
            'INSERT INTO dish_allergens (dish_id, allergen_id) VALUES (:dish_id, :allergen_id)'
        );

        $weeks = [
            'current' => [
                'Montag' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Currywurst mit Pommes', '3,50 €', 'Schwein,Gluten'],
                        'ohne_fleisch' => ['Gemüsecurry mit Reis', '3,20 €', 'Vegan'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Kartoffelsalat', '1,50 €', 'Eier']],
                    'nachtisch' => ['ohne_fleisch' => ['Vanillepudding', '1,20 €', 'Milch,Eier']],
                ],
                'Dienstag' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Rinderroulade mit Rotkohl und Klößen', '4,20 €', 'Rind,Gluten'],
                        'ohne_fleisch' => ['Kartoffel-Lauch-Suppe mit Baguette', '2,80 €', 'Milch,Gluten'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Rotkohl', '1,40 €', 'Vegan']],
                    'nachtisch' => ['ohne_fleisch' => ['Schokopudding', '1,20 €', 'Milch']],
                ],
                'Mittwoch' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Hähnchengeschnetzeltes mit Basmatireis', '3,90 €', 'Hähnchen'],
                        'ohne_fleisch' => ['Linsen-Curry mit Reis', '3,10 €', 'Vegan'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Basmatireis', '1,30 €', 'Vegan']],
                    'nachtisch' => ['ohne_fleisch' => ['Grießbrei mit Kirschen', '1,30 €', 'Milch,Gluten']],
                ],
                'Donnerstag' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Putengeschnetzeltes mit Nudeln', '3,90 €', 'Pute,Gluten,Eier'],
                        'ohne_fleisch' => ['Linsen-Dal mit Naan-Brot', '3,10 €', 'Gluten'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Salzkartoffeln', '1,30 €', 'Vegan']],
                    'nachtisch' => ['ohne_fleisch' => ['Milchreis mit Zimt-Zucker', '1,30 €', 'Milch']],
                ],
                'Freitag' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Pizza Salami', '3,00 €', 'Schwein,Gluten,Milch'],
                        'ohne_fleisch' => ['Pizza Margherita (vegan)', '3,00 €', 'Vegan,Gluten'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Rohkostsalat', '1,60 €', 'Vegan']],
                    'nachtisch' => ['ohne_fleisch' => ['Rote Grütze mit Vanillesoße', '1,30 €', 'Milch']],
                ],
            ],
            'next' => [
                'Montag' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Schweineschnitzel mit Bratkartoffeln', '4,10 €', 'Schwein,Gluten,Eier'],
                        'ohne_fleisch' => ['Kichererbsen-Curry mit Basmatireis', '3,30 €', 'Vegan'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Bratkartoffeln', '1,50 €', 'Vegan']],
                    'nachtisch' => ['ohne_fleisch' => ['Fruchtjoghurt', '1,20 €', 'Milch']],
                ],
                'Dienstag' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Gulasch mit Spätzle', '4,00 €', 'Rind,Gluten,Eier'],
                        'ohne_fleisch' => ['Ofengemüse mit Couscous', '3,10 €', 'Vegan,Gluten'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Spätzle', '1,40 €', 'Gluten,Eier']],
                    'nachtisch' => ['ohne_fleisch' => ['Karamellpudding', '1,20 €', 'Milch']],
                ],
                'Mittwoch' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Hähnchen-Curry mit Reis', '3,90 €', 'Hähnchen'],
                        'ohne_fleisch' => ['Veganes Erbsen-Risotto', '3,20 €', 'Vegan'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Reis', '1,20 €', 'Vegan']],
                    'nachtisch' => ['ohne_fleisch' => ['Zitronenmousse', '1,40 €', 'Milch,Eier']],
                ],
                'Donnerstag' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Bratwurst mit Sauerkraut', '3,60 €', 'Schwein'],
                        'ohne_fleisch' => ['Süßkartoffel-Bowl', '3,50 €', 'Vegan'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Sauerkraut', '1,20 €', 'Vegan']],
                    'nachtisch' => ['ohne_fleisch' => ['Waffeln mit Apfelmus', '1,60 €', 'Milch,Eier,Gluten']],
                ],
                'Freitag' => [
                    'hauptgericht' => [
                        'mit_fleisch' => ['Fish & Chips', '3,80 €', 'Fisch,Gluten'],
                        'ohne_fleisch' => ['Veganer Burger mit Pommes', '3,60 €', 'Vegan,Gluten'],
                    ],
                    'beilage' => ['ohne_fleisch' => ['Kartoffelwedges', '1,50 €', 'Vegan']],
                    'nachtisch' => ['ohne_fleisch' => ['Zitronen-Sorbet', '1,40 €', 'Vegan']],
                ],
            ],
        ];

        foreach ($weeks as $week => $days) {
            foreach ($days as $day => $categories) {
                foreach ($categories as $category => $variants) {
                    foreach ($variants as $variant => [$name, $price, $labels]) {
                        $dishStatement->execute([
                            'category' => $category,
                            'variant' => $variant,
                            'name' => $name,
                            'price' => $price,
                        ]);
                        $dishId = (int) $pdo->lastInsertId();

                        $slotStatement->execute([
                            'week' => $week,
                            'day' => $day,
                            'category' => $category,
                            'variant' => $variant,
                            'dish_id' => $dishId,
                        ]);

                        foreach (explode(',', $labels) as $allergenName) {
                            $allergenName = trim($allergenName);

                            if ($allergenName === '') {
                                continue;
                            }

                            $allergenStatement->execute(['name' => $allergenName]);
                            $allergenLookupStatement->execute(['name' => $allergenName]);
                            $allergenId = (int) $allergenLookupStatement->fetchColumn();

                            $linkStatement->execute([
                                'dish_id' => $dishId,
                                'allergen_id' => $allergenId,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
