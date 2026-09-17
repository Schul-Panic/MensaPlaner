DROP TABLE IF EXISTS dish_allergens;
DROP TABLE IF EXISTS allergens;
DROP TABLE IF EXISTS menu_slots;
DROP TABLE IF EXISTS dishes;

CREATE TABLE dishes (
    id SERIAL PRIMARY KEY,
    category VARCHAR(20) NOT NULL,
    variant VARCHAR(20) NOT NULL,
    name TEXT NOT NULL,
    price VARCHAR(20) NOT NULL
);

CREATE TABLE menu_slots (
    id SERIAL PRIMARY KEY,
    week VARCHAR(10) NOT NULL,
    day VARCHAR(20) NOT NULL,
    category VARCHAR(20) NOT NULL,
    variant VARCHAR(20) NOT NULL,
    dish_id INTEGER NOT NULL REFERENCES dishes(id) ON DELETE CASCADE,
    UNIQUE (week, day, category, variant)
);

CREATE TABLE allergens (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE dish_allergens (
    dish_id INTEGER NOT NULL REFERENCES dishes(id) ON DELETE CASCADE,
    allergen_id INTEGER NOT NULL REFERENCES allergens(id) ON DELETE CASCADE,
    UNIQUE (dish_id, allergen_id)
);

INSERT INTO dishes (category, variant, name, price) VALUES
('hauptgericht','mit_fleisch','Currywurst mit Pommes','3,50 €'),
('hauptgericht','ohne_fleisch','Gemüsecurry mit Reis','3,20 €'),
('beilage','ohne_fleisch','Kartoffelsalat','1,50 €'),
('nachtisch','ohne_fleisch','Vanillepudding','1,20 €'),
('hauptgericht','mit_fleisch','Rinderroulade mit Rotkohl und Klößen','4,20 €'),
('hauptgericht','ohne_fleisch','Kartoffel-Lauch-Suppe mit Baguette','2,80 €'),
('beilage','ohne_fleisch','Rotkohl','1,40 €'),
('nachtisch','ohne_fleisch','Schokopudding','1,20 €'),
('hauptgericht','mit_fleisch','Hähnchengeschnetzeltes mit Basmatireis','3,90 €'),
('hauptgericht','ohne_fleisch','Linsen-Curry mit Reis','3,10 €'),
('beilage','ohne_fleisch','Basmatireis','1,30 €'),
('nachtisch','ohne_fleisch','Grießbrei mit Kirschen','1,30 €'),
('hauptgericht','mit_fleisch','Putengeschnetzeltes mit Nudeln','3,90 €'),
('hauptgericht','ohne_fleisch','Linsen-Dal mit Naan-Brot','3,10 €'),
('beilage','ohne_fleisch','Salzkartoffeln','1,30 €'),
('nachtisch','ohne_fleisch','Milchreis mit Zimt-Zucker','1,30 €'),
('hauptgericht','mit_fleisch','Pizza Salami','3,00 €'),
('hauptgericht','ohne_fleisch','Pizza Margherita (vegan)','3,00 €'),
('beilage','ohne_fleisch','Rohkostsalat','1,60 €'),
('nachtisch','ohne_fleisch','Rote Grütze mit Vanillesoße','1,30 €'),
('hauptgericht','mit_fleisch','Schweineschnitzel mit Bratkartoffeln','4,10 €'),
('hauptgericht','ohne_fleisch','Kichererbsen-Curry mit Basmatireis','3,30 €'),
('beilage','ohne_fleisch','Bratkartoffeln','1,50 €'),
('nachtisch','ohne_fleisch','Fruchtjoghurt','1,20 €'),
('hauptgericht','mit_fleisch','Gulasch mit Spätzle','4,00 €'),
('hauptgericht','ohne_fleisch','Ofengemüse mit Couscous','3,10 €'),
('beilage','ohne_fleisch','Spätzle','1,40 €'),
('nachtisch','ohne_fleisch','Karamellpudding','1,20 €'),
('hauptgericht','mit_fleisch','Hähnchen-Curry mit Reis','3,90 €'),
('hauptgericht','ohne_fleisch','Veganes Erbsen-Risotto','3,20 €'),
('beilage','ohne_fleisch','Reis','1,20 €'),
('nachtisch','ohne_fleisch','Zitronenmousse','1,40 €'),
('hauptgericht','mit_fleisch','Bratwurst mit Sauerkraut','3,60 €'),
('hauptgericht','ohne_fleisch','Süßkartoffel-Bowl','3,50 €'),
('beilage','ohne_fleisch','Sauerkraut','1,20 €'),
('nachtisch','ohne_fleisch','Waffeln mit Apfelmus','1,60 €'),
('hauptgericht','mit_fleisch','Fish & Chips','3,80 €'),
('hauptgericht','ohne_fleisch','Veganer Burger mit Pommes','3,60 €'),
('beilage','ohne_fleisch','Kartoffelwedges','1,50 €'),
('nachtisch','ohne_fleisch','Zitronen-Sorbet','1,40 €');

INSERT INTO menu_slots (week, day, category, variant, dish_id) VALUES
('current','Montag','hauptgericht','mit_fleisch',1),
('current','Montag','hauptgericht','ohne_fleisch',2),
('current','Montag','beilage','ohne_fleisch',3),
('current','Montag','nachtisch','ohne_fleisch',4),
('current','Dienstag','hauptgericht','mit_fleisch',5),
('current','Dienstag','hauptgericht','ohne_fleisch',6),
('current','Dienstag','beilage','ohne_fleisch',7),
('current','Dienstag','nachtisch','ohne_fleisch',8),
('current','Mittwoch','hauptgericht','mit_fleisch',9),
('current','Mittwoch','hauptgericht','ohne_fleisch',10),
('current','Mittwoch','beilage','ohne_fleisch',11),
('current','Mittwoch','nachtisch','ohne_fleisch',12),
('current','Donnerstag','hauptgericht','mit_fleisch',13),
('current','Donnerstag','hauptgericht','ohne_fleisch',14),
('current','Donnerstag','beilage','ohne_fleisch',15),
('current','Donnerstag','nachtisch','ohne_fleisch',16),
('current','Freitag','hauptgericht','mit_fleisch',17),
('current','Freitag','hauptgericht','ohne_fleisch',18),
('current','Freitag','beilage','ohne_fleisch',19),
('current','Freitag','nachtisch','ohne_fleisch',20),
('next','Montag','hauptgericht','mit_fleisch',21),
('next','Montag','hauptgericht','ohne_fleisch',22),
('next','Montag','beilage','ohne_fleisch',23),
('next','Montag','nachtisch','ohne_fleisch',24),
('next','Dienstag','hauptgericht','mit_fleisch',25),
('next','Dienstag','hauptgericht','ohne_fleisch',26),
('next','Dienstag','beilage','ohne_fleisch',27),
('next','Dienstag','nachtisch','ohne_fleisch',28),
('next','Mittwoch','hauptgericht','mit_fleisch',29),
('next','Mittwoch','hauptgericht','ohne_fleisch',30),
('next','Mittwoch','beilage','ohne_fleisch',31),
('next','Mittwoch','nachtisch','ohne_fleisch',32),
('next','Donnerstag','hauptgericht','mit_fleisch',33),
('next','Donnerstag','hauptgericht','ohne_fleisch',34),
('next','Donnerstag','beilage','ohne_fleisch',35),
('next','Donnerstag','nachtisch','ohne_fleisch',36),
('next','Freitag','hauptgericht','mit_fleisch',37),
('next','Freitag','hauptgericht','ohne_fleisch',38),
('next','Freitag','beilage','ohne_fleisch',39),
('next','Freitag','nachtisch','ohne_fleisch',40);

INSERT INTO allergens (name) VALUES
('Schwein'),('Gluten'),('Vegan'),('Eier'),('Rind'),('Milch'),
('Hähnchen'),('Pute'),('Fisch'),('Soja');

INSERT INTO dish_allergens (dish_id, allergen_id)
SELECT d.id, a.id FROM dishes d, allergens a WHERE (d.name, a.name) IN (
    ('Currywurst mit Pommes','Schwein'), ('Currywurst mit Pommes','Gluten'),
    ('Gemüsecurry mit Reis','Vegan'),
    ('Kartoffelsalat','Eier'),
    ('Vanillepudding','Milch'), ('Vanillepudding','Eier'),
    ('Rinderroulade mit Rotkohl und Klößen','Rind'), ('Rinderroulade mit Rotkohl und Klößen','Gluten'),
    ('Kartoffel-Lauch-Suppe mit Baguette','Milch'), ('Kartoffel-Lauch-Suppe mit Baguette','Gluten'),
    ('Rotkohl','Vegan'),
    ('Schokopudding','Milch'),
    ('Hähnchengeschnetzeltes mit Basmatireis','Hähnchen'),
    ('Linsen-Curry mit Reis','Vegan'),
    ('Basmatireis','Vegan'),
    ('Grießbrei mit Kirschen','Milch'), ('Grießbrei mit Kirschen','Gluten'),
    ('Putengeschnetzeltes mit Nudeln','Pute'), ('Putengeschnetzeltes mit Nudeln','Gluten'), ('Putengeschnetzeltes mit Nudeln','Eier'),
    ('Linsen-Dal mit Naan-Brot','Gluten'),
    ('Salzkartoffeln','Vegan'),
    ('Milchreis mit Zimt-Zucker','Milch'),
    ('Pizza Salami','Schwein'), ('Pizza Salami','Gluten'), ('Pizza Salami','Milch'),
    ('Pizza Margherita (vegan)','Vegan'), ('Pizza Margherita (vegan)','Gluten'),
    ('Rohkostsalat','Vegan'),
    ('Rote Grütze mit Vanillesoße','Milch'),
    ('Schweineschnitzel mit Bratkartoffeln','Schwein'), ('Schweineschnitzel mit Bratkartoffeln','Gluten'), ('Schweineschnitzel mit Bratkartoffeln','Eier'),
    ('Kichererbsen-Curry mit Basmatireis','Vegan'),
    ('Bratkartoffeln','Vegan'),
    ('Fruchtjoghurt','Milch'),
    ('Gulasch mit Spätzle','Rind'), ('Gulasch mit Spätzle','Gluten'), ('Gulasch mit Spätzle','Eier'),
    ('Ofengemüse mit Couscous','Vegan'), ('Ofengemüse mit Couscous','Gluten'),
    ('Spätzle','Gluten'), ('Spätzle','Eier'),
    ('Karamellpudding','Milch'),
    ('Hähnchen-Curry mit Reis','Hähnchen'),
    ('Veganes Erbsen-Risotto','Vegan'),
    ('Reis','Vegan'),
    ('Zitronenmousse','Milch'), ('Zitronenmousse','Eier'),
    ('Bratwurst mit Sauerkraut','Schwein'),
    ('Süßkartoffel-Bowl','Vegan'),
    ('Sauerkraut','Vegan'),
    ('Waffeln mit Apfelmus','Milch'), ('Waffeln mit Apfelmus','Eier'), ('Waffeln mit Apfelmus','Gluten'),
    ('Fish & Chips','Fisch'), ('Fish & Chips','Gluten'),
    ('Veganer Burger mit Pommes','Vegan'), ('Veganer Burger mit Pommes','Gluten'),
    ('Kartoffelwedges','Vegan'),
    ('Zitronen-Sorbet','Vegan')
);
