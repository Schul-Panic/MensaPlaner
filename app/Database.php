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
    }

    private static function seedMockData(PDO $pdo): void
    {
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
}
