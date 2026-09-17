<?php

namespace App\Model;

use App\Database;
use PDO;

class Dish
{
    public const CATEGORY_LABELS = [
        "hauptgerichte" => "Hauptgerichte",
        "beilagen" => "Beilagen",
        "nachtische" => "Nachtische",
    ];

    public const ROW_LABELS = [
        "hauptgericht" => "Hauptgericht",
        "beilage" => "Beilage",
        "nachtisch" => "Nachtisch",
    ];

    public const COLUMN_LABELS = [
        "mit_fleisch" => "Mit Fleisch",
        "ohne_fleisch" => "Ohne Fleisch",
    ];

    public const DAYS = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag"];

    public function getWeeklyMatrix(): array
    {
        return $this->buildMatrix('current');
    }

    public function getNextWeekMatrix(): array
    {
        return $this->buildMatrix('next');
    }

    public function all(): array
    {
        $statement = Database::connection()->query(
            'SELECT id, category, variant, name, price FROM dishes'
        );
        $dishes = $statement->fetchAll();

        $categoryOrder = array_flip(array_keys(self::ROW_LABELS));

        usort($dishes, function ($a, $b) use ($categoryOrder) {
            return [$categoryOrder[$a['category']] ?? 99, $a['variant'], $a['name']]
                <=> [$categoryOrder[$b['category']] ?? 99, $b['variant'], $b['name']];
        });

        $allergensByDish = $this->allergensByDishId(array_column($dishes, 'id'));

        foreach ($dishes as &$dish) {
            $dish['allergens'] = $allergensByDish[$dish['id']] ?? [];
        }
        unset($dish);

        return $dishes;
    }

    public function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, category, variant, name, price FROM dishes WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $dish = $statement->fetch();

        if (!$dish) {
            return null;
        }

        $dish['allergens'] = $this->allergensByDishId([$id])[$id] ?? [];

        return $dish;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO dishes (category, variant, name, price)
             VALUES (:category, :variant, :name, :price)'
        );
        $statement->execute($data);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = Database::connection()->prepare(
            'UPDATE dishes SET category = :category, variant = :variant,
             name = :name, price = :price WHERE id = :id'
        );
        $statement->execute($data);
    }

    public function delete(int $id): void
    {
        $statement = Database::connection()->prepare('DELETE FROM dishes WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function allergens(): array
    {
        $statement = Database::connection()->query('SELECT name FROM allergens ORDER BY name');

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function resolveOrCreateAllergenIds(array $names): array
    {
        $db = Database::connection();
        $insertStatement = $db->prepare('INSERT INTO allergens (name) VALUES (:name)');
        $lookupStatement = $db->prepare('SELECT id FROM allergens WHERE name = :name');
        $ids = [];

        foreach (array_unique($names) as $name) {
            $name = trim($name);

            if ($name === '') {
                continue;
            }

            $lookupStatement->execute(['name' => $name]);
            $id = $lookupStatement->fetchColumn();

            if ($id === false) {
                $insertStatement->execute(['name' => $name]);
                $id = (int) $db->lastInsertId();
            }

            $ids[] = (int) $id;
        }

        return $ids;
    }

    public function syncAllergens(int $dishId, array $allergenIds): void
    {
        $db = Database::connection();
        $db->prepare('DELETE FROM dish_allergens WHERE dish_id = :dish_id')->execute(['dish_id' => $dishId]);

        $linkStatement = $db->prepare(
            'INSERT INTO dish_allergens (dish_id, allergen_id) VALUES (:dish_id, :allergen_id)'
        );

        foreach ($allergenIds as $allergenId) {
            $linkStatement->execute(['dish_id' => $dishId, 'allergen_id' => $allergenId]);
        }
    }

    private function allergensByDishId(array $dishIds): array
    {
        $dishIds = array_unique(array_map('intval', $dishIds));

        if (!$dishIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($dishIds), '?'));
        $statement = Database::connection()->prepare(
            "SELECT dish_allergens.dish_id, allergens.name
             FROM dish_allergens
             JOIN allergens ON allergens.id = dish_allergens.allergen_id
             WHERE dish_allergens.dish_id IN ($placeholders)
             ORDER BY allergens.name"
        );
        $statement->execute($dishIds);

        $result = [];
        foreach ($statement->fetchAll() as $row) {
            $result[(int) $row['dish_id']][] = $row['name'];
        }

        return $result;
    }

    private function buildMatrix(string $week): array
    {
        $matrix = [];

        foreach (self::DAYS as $day) {
            foreach (array_keys(self::ROW_LABELS) as $row) {
                foreach (array_keys(self::COLUMN_LABELS) as $column) {
                    $matrix[$day][$row][$column] = null;
                }
            }
        }

        $statement = Database::connection()->prepare(
            'SELECT menu_slots.day, menu_slots.category, menu_slots.variant,
                    dishes.id, dishes.name, dishes.price
             FROM menu_slots
             JOIN dishes ON dishes.id = menu_slots.dish_id
             WHERE menu_slots.week = :week'
        );
        $statement->execute(['week' => $week]);
        $rows = $statement->fetchAll();

        $allergensByDish = $this->allergensByDishId(array_column($rows, 'id'));

        foreach ($rows as $row) {
            if (!isset($matrix[$row['day']][$row['category']])) {
                continue;
            }

            $matrix[$row['day']][$row['category']][$row['variant']] = [
                'id' => (int) $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'labels' => $allergensByDish[(int) $row['id']] ?? [],
            ];
        }

        return $matrix;
    }

    public function getWeeklyMenu()
    {
        $menu = [
            "Montag" => [
                "hauptgerichte" => [
                    ["name" => "Currywurst mit Pommes", "price" => "3,50 €", "vegan" => false, "labels" => ["Schwein", "Gluten"]],
                    ["name" => "Hähnchenschnitzel mit Kartoffelsalat", "price" => "4,00 €", "vegan" => false, "labels" => ["Hähnchen", "Gluten", "Eier"]],
                    ["name" => "Gemüsecurry mit Reis", "price" => "3,20 €", "vegan" => true, "labels" => ["Vegan"]],
                    ["name" => "Falafel-Bowl mit Hummus", "price" => "3,40 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
                "beilagen" => [
                    ["name" => "Kartoffelsalat", "price" => "1,50 €", "vegan" => false, "labels" => ["Eier"]],
                    ["name" => "Pommes frites", "price" => "1,50 €", "vegan" => true, "labels" => ["Vegan"]],
                    ["name" => "Gemischter Salat", "price" => "1,80 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
                "nachtische" => [
                    ["name" => "Vanillepudding", "price" => "1,20 €", "vegan" => false, "labels" => ["Milch", "Eier"]],
                    ["name" => "Obstsalat", "price" => "1,50 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
            ],
            "Dienstag" => [
                "hauptgerichte" => [
                    ["name" => "Rinderroulade mit Rotkohl und Klößen", "price" => "4,20 €", "vegan" => false, "labels" => ["Rind", "Gluten"]],
                    ["name" => "Schweinebraten mit Semmelknödel", "price" => "4,10 €", "vegan" => false, "labels" => ["Schwein", "Gluten", "Eier"]],
                    ["name" => "Kartoffel-Lauch-Suppe mit Baguette", "price" => "2,80 €", "vegan" => true, "labels" => ["Vegan", "Gluten"]],
                    ["name" => "Veganes Chili sin Carne", "price" => "3,30 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
                "beilagen" => [
                    ["name" => "Semmelknödel", "price" => "1,40 €", "vegan" => false, "labels" => ["Gluten", "Eier"]],
                    ["name" => "Rotkohl", "price" => "1,40 €", "vegan" => true, "labels" => ["Vegan"]],
                    ["name" => "Baguette", "price" => "1,00 €", "vegan" => true, "labels" => ["Vegan", "Gluten"]],
                ],
                "nachtische" => [
                    ["name" => "Schokopudding", "price" => "1,20 €", "vegan" => false, "labels" => ["Milch"]],
                    ["name" => "Apfelmus", "price" => "1,00 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
            ],
            "Mittwoch" => [
                "hauptgerichte" => [
                    ["name" => "Hähnchengeschnetzeltes mit Basmatireis", "price" => "3,90 €", "vegan" => false, "labels" => ["Hähnchen"]],
                    ["name" => "Fischfrikadellen mit Remoulade", "price" => "3,80 €", "vegan" => false, "labels" => ["Fisch", "Eier"]],
                    ["name" => "Linsen-Curry mit Reis", "price" => "3,10 €", "vegan" => true, "labels" => ["Vegan"]],
                    ["name" => "Gebackener Tofu mit Gemüse", "price" => "3,40 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
                "beilagen" => [
                    ["name" => "Basmatireis", "price" => "1,30 €", "vegan" => true, "labels" => ["Vegan"]],
                    ["name" => "Gemüsepfanne", "price" => "1,60 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
                "nachtische" => [
                    ["name" => "Grießbrei mit Kirschen", "price" => "1,30 €", "vegan" => false, "labels" => ["Milch", "Gluten"]],
                    ["name" => "Sojajoghurt mit Beeren", "price" => "1,40 €", "vegan" => true, "labels" => ["Vegan", "Soja"]],
                ],
            ],
            "Donnerstag" => [
                "hauptgerichte" => [
                    ["name" => "Fischfilet mit Remoulade und Kartoffeln", "price" => "3,70 €", "vegan" => false, "labels" => ["Fisch", "Eier"]],
                    ["name" => "Putengeschnetzeltes mit Nudeln", "price" => "3,90 €", "vegan" => false, "labels" => ["Pute", "Gluten", "Eier"]],
                    ["name" => "Linsen-Dal mit Naan-Brot", "price" => "3,10 €", "vegan" => true, "labels" => ["Vegan", "Gluten"]],
                    ["name" => "Vegane Bolognese mit Spaghetti", "price" => "3,30 €", "vegan" => true, "labels" => ["Vegan", "Gluten"]],
                ],
                "beilagen" => [
                    ["name" => "Salzkartoffeln", "price" => "1,30 €", "vegan" => true, "labels" => ["Vegan"]],
                    ["name" => "Naan-Brot", "price" => "1,20 €", "vegan" => true, "labels" => ["Vegan", "Gluten"]],
                ],
                "nachtische" => [
                    ["name" => "Milchreis mit Zimt-Zucker", "price" => "1,30 €", "vegan" => false, "labels" => ["Milch"]],
                    ["name" => "Erdbeer-Sorbet", "price" => "1,50 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
            ],
            "Freitag" => [
                "hauptgerichte" => [
                    ["name" => "Pizza Salami", "price" => "3,00 €", "vegan" => false, "labels" => ["Schwein", "Gluten", "Milch"]],
                    ["name" => "Backfisch mit Remoulade", "price" => "3,60 €", "vegan" => false, "labels" => ["Fisch", "Gluten", "Eier"]],
                    ["name" => "Pizza Margherita (vegan)", "price" => "3,00 €", "vegan" => true, "labels" => ["Vegan", "Gluten"]],
                    ["name" => "Gemüse-Wok mit Tofu", "price" => "3,20 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
                "beilagen" => [
                    ["name" => "Pommes frites", "price" => "1,50 €", "vegan" => true, "labels" => ["Vegan"]],
                    ["name" => "Rohkostsalat", "price" => "1,60 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
                "nachtische" => [
                    ["name" => "Rote Grütze mit Vanillesoße", "price" => "1,30 €", "vegan" => false, "labels" => ["Milch"]],
                    ["name" => "Obstsalat", "price" => "1,50 €", "vegan" => true, "labels" => ["Vegan"]],
                ],
            ],
        ];

        foreach ($menu as &$categories) {
            foreach ($categories as &$items) {
                usort($items, fn ($a, $b) => $a['vegan'] <=> $b['vegan']);
            }
        }

        return $menu;
    }

    public function getDishCategoryMap(): array
    {
        $map = [];

        foreach ([$this->getWeeklyMatrix(), $this->getNextWeekMatrix()] as $matrix) {
            foreach ($matrix as $rows) {
                foreach ($rows as $row => $columns) {
                    foreach ($columns as $dish) {
                        if ($dish !== null) {
                            $map[$dish['name']] = $row;
                        }
                    }
                }
            }
        }

        return $map;
    }

    public function getFlattenedDishes()
    {
        $dishes = [];
        $id = 1;

        foreach ($this->getWeeklyMenu() as $day => $categories) {
            foreach ($categories as $category => $items) {
                foreach ($items as $item) {
                    $item["id"] = $id++;
                    $item["day"] = $day;
                    $item["category"] = $category;
                    $dishes[] = $item;
                }
            }
        }

        return $dishes;
    }
}
