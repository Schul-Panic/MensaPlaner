<?php

namespace App\Model;

class Dish
{
    public const CATEGORY_LABELS = [
        "hauptgerichte" => "Hauptgerichte",
        "beilagen" => "Beilagen",
        "nachtische" => "Nachtische",
    ];

    public function getWeeklyMenu()
    {
        $menu = [
            "Montag" => [
                "hauptgerichte" => [
                    ["name" => "Currywurst mit Pommes", "price" => "3,50 €", "vegan" => false],
                    ["name" => "Hähnchenschnitzel mit Kartoffelsalat", "price" => "4,00 €", "vegan" => false],
                    ["name" => "Gemüsecurry mit Reis", "price" => "3,20 €", "vegan" => true],
                    ["name" => "Falafel-Bowl mit Hummus", "price" => "3,40 €", "vegan" => true],
                ],
                "beilagen" => [
                    ["name" => "Kartoffelsalat", "price" => "1,50 €", "vegan" => false],
                    ["name" => "Pommes frites", "price" => "1,50 €", "vegan" => true],
                    ["name" => "Gemischter Salat", "price" => "1,80 €", "vegan" => true],
                ],
                "nachtische" => [
                    ["name" => "Vanillepudding", "price" => "1,20 €", "vegan" => false],
                    ["name" => "Obstsalat", "price" => "1,50 €", "vegan" => true],
                ],
            ],
            "Dienstag" => [
                "hauptgerichte" => [
                    ["name" => "Rinderroulade mit Rotkohl und Klößen", "price" => "4,20 €", "vegan" => false],
                    ["name" => "Schweinebraten mit Semmelknödel", "price" => "4,10 €", "vegan" => false],
                    ["name" => "Kartoffel-Lauch-Suppe mit Baguette", "price" => "2,80 €", "vegan" => true],
                    ["name" => "Veganes Chili sin Carne", "price" => "3,30 €", "vegan" => true],
                ],
                "beilagen" => [
                    ["name" => "Semmelknödel", "price" => "1,40 €", "vegan" => false],
                    ["name" => "Rotkohl", "price" => "1,40 €", "vegan" => true],
                    ["name" => "Baguette", "price" => "1,00 €", "vegan" => true],
                ],
                "nachtische" => [
                    ["name" => "Schokopudding", "price" => "1,20 €", "vegan" => false],
                    ["name" => "Apfelmus", "price" => "1,00 €", "vegan" => true],
                ],
            ],
            "Mittwoch" => [
                "hauptgerichte" => [
                    ["name" => "Hähnchengeschnetzeltes mit Basmatireis", "price" => "3,90 €", "vegan" => false],
                    ["name" => "Fischfrikadellen mit Remoulade", "price" => "3,80 €", "vegan" => false],
                    ["name" => "Linsen-Curry mit Reis", "price" => "3,10 €", "vegan" => true],
                    ["name" => "Gebackener Tofu mit Gemüse", "price" => "3,40 €", "vegan" => true],
                ],
                "beilagen" => [
                    ["name" => "Basmatireis", "price" => "1,30 €", "vegan" => true],
                    ["name" => "Gemüsepfanne", "price" => "1,60 €", "vegan" => true],
                ],
                "nachtische" => [
                    ["name" => "Grießbrei mit Kirschen", "price" => "1,30 €", "vegan" => false],
                    ["name" => "Sojajoghurt mit Beeren", "price" => "1,40 €", "vegan" => true],
                ],
            ],
            "Donnerstag" => [
                "hauptgerichte" => [
                    ["name" => "Fischfilet mit Remoulade und Kartoffeln", "price" => "3,70 €", "vegan" => false],
                    ["name" => "Putengeschnetzeltes mit Nudeln", "price" => "3,90 €", "vegan" => false],
                    ["name" => "Linsen-Dal mit Naan-Brot", "price" => "3,10 €", "vegan" => true],
                    ["name" => "Vegane Bolognese mit Spaghetti", "price" => "3,30 €", "vegan" => true],
                ],
                "beilagen" => [
                    ["name" => "Salzkartoffeln", "price" => "1,30 €", "vegan" => true],
                    ["name" => "Naan-Brot", "price" => "1,20 €", "vegan" => true],
                ],
                "nachtische" => [
                    ["name" => "Milchreis mit Zimt-Zucker", "price" => "1,30 €", "vegan" => false],
                    ["name" => "Erdbeer-Sorbet", "price" => "1,50 €", "vegan" => true],
                ],
            ],
            "Freitag" => [
                "hauptgerichte" => [
                    ["name" => "Pizza Salami", "price" => "3,00 €", "vegan" => false],
                    ["name" => "Backfisch mit Remoulade", "price" => "3,60 €", "vegan" => false],
                    ["name" => "Pizza Margherita (vegan)", "price" => "3,00 €", "vegan" => true],
                    ["name" => "Gemüse-Wok mit Tofu", "price" => "3,20 €", "vegan" => true],
                ],
                "beilagen" => [
                    ["name" => "Pommes frites", "price" => "1,50 €", "vegan" => true],
                    ["name" => "Rohkostsalat", "price" => "1,60 €", "vegan" => true],
                ],
                "nachtische" => [
                    ["name" => "Rote Grütze mit Vanillesoße", "price" => "1,30 €", "vegan" => false],
                    ["name" => "Obstsalat", "price" => "1,50 €", "vegan" => true],
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
