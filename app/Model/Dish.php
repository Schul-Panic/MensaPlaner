<?php

namespace App\Model;

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

    public function getWeeklyMatrix()
    {
        return [
            "Montag" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Currywurst mit Pommes", "price" => "3,50 €", "labels" => ["Schwein", "Gluten"]],
                    "ohne_fleisch" => ["name" => "Gemüsecurry mit Reis", "price" => "3,20 €", "labels" => ["Vegan"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Kartoffelsalat", "price" => "1,50 €", "labels" => ["Eier"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Vanillepudding", "price" => "1,20 €", "labels" => ["Milch", "Eier"]],
                ],
            ],
            "Dienstag" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Rinderroulade mit Rotkohl und Klößen", "price" => "4,20 €", "labels" => ["Rind", "Gluten"]],
                    "ohne_fleisch" => ["name" => "Kartoffel-Lauch-Suppe mit Baguette", "price" => "2,80 €", "labels" => ["Milch", "Gluten"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Rotkohl", "price" => "1,40 €", "labels" => ["Vegan"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Schokopudding", "price" => "1,20 €", "labels" => ["Milch"]],
                ],
            ],
            "Mittwoch" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Hähnchengeschnetzeltes mit Basmatireis", "price" => "3,90 €", "labels" => ["Hähnchen"]],
                    "ohne_fleisch" => ["name" => "Linsen-Curry mit Reis", "price" => "3,10 €", "labels" => ["Vegan"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Basmatireis", "price" => "1,30 €", "labels" => ["Vegan"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Grießbrei mit Kirschen", "price" => "1,30 €", "labels" => ["Milch", "Gluten"]],
                ],
            ],
            "Donnerstag" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Putengeschnetzeltes mit Nudeln", "price" => "3,90 €", "labels" => ["Pute", "Gluten", "Eier"]],
                    "ohne_fleisch" => ["name" => "Linsen-Dal mit Naan-Brot", "price" => "3,10 €", "labels" => ["Gluten"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Salzkartoffeln", "price" => "1,30 €", "labels" => ["Vegan"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Milchreis mit Zimt-Zucker", "price" => "1,30 €", "labels" => ["Milch"]],
                ],
            ],
            "Freitag" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Pizza Salami", "price" => "3,00 €", "labels" => ["Schwein", "Gluten", "Milch"]],
                    "ohne_fleisch" => ["name" => "Pizza Margherita (vegan)", "price" => "3,00 €", "labels" => ["Vegan", "Gluten"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Rohkostsalat", "price" => "1,60 €", "labels" => ["Vegan"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Rote Grütze mit Vanillesoße", "price" => "1,30 €", "labels" => ["Milch"]],
                ],
            ],
        ];
    }

    public function getNextWeekMatrix()
    {
        return [
            "Montag" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Schweineschnitzel mit Bratkartoffeln", "price" => "4,10 €", "labels" => ["Schwein", "Gluten", "Eier"]],
                    "ohne_fleisch" => ["name" => "Kichererbsen-Curry mit Basmatireis", "price" => "3,30 €", "labels" => ["Vegan"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Bratkartoffeln", "price" => "1,50 €", "labels" => ["Vegan"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Fruchtjoghurt", "price" => "1,20 €", "labels" => ["Milch"]],
                ],
            ],
            "Dienstag" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Gulasch mit Spätzle", "price" => "4,00 €", "labels" => ["Rind", "Gluten", "Eier"]],
                    "ohne_fleisch" => ["name" => "Ofengemüse mit Couscous", "price" => "3,10 €", "labels" => ["Vegan", "Gluten"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Spätzle", "price" => "1,40 €", "labels" => ["Gluten", "Eier"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Karamellpudding", "price" => "1,20 €", "labels" => ["Milch"]],
                ],
            ],
            "Mittwoch" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Hähnchen-Curry mit Reis", "price" => "3,90 €", "labels" => ["Hähnchen"]],
                    "ohne_fleisch" => ["name" => "Veganes Erbsen-Risotto", "price" => "3,20 €", "labels" => ["Vegan"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Reis", "price" => "1,20 €", "labels" => ["Vegan"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Zitronenmousse", "price" => "1,40 €", "labels" => ["Milch", "Eier"]],
                ],
            ],
            "Donnerstag" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Bratwurst mit Sauerkraut", "price" => "3,60 €", "labels" => ["Schwein"]],
                    "ohne_fleisch" => ["name" => "Süßkartoffel-Bowl", "price" => "3,50 €", "labels" => ["Vegan"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Sauerkraut", "price" => "1,20 €", "labels" => ["Vegan"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Waffeln mit Apfelmus", "price" => "1,60 €", "labels" => ["Milch", "Eier", "Gluten"]],
                ],
            ],
            "Freitag" => [
                "hauptgericht" => [
                    "mit_fleisch" => ["name" => "Fish & Chips", "price" => "3,80 €", "labels" => ["Fisch", "Gluten"]],
                    "ohne_fleisch" => ["name" => "Veganer Burger mit Pommes", "price" => "3,60 €", "labels" => ["Vegan", "Gluten"]],
                ],
                "beilage" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Kartoffelwedges", "price" => "1,50 €", "labels" => ["Vegan"]],
                ],
                "nachtisch" => [
                    "mit_fleisch" => null,
                    "ohne_fleisch" => ["name" => "Zitronen-Sorbet", "price" => "1,40 €", "labels" => ["Vegan"]],
                ],
            ],
        ];
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
