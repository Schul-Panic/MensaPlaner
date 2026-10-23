-- Basis-Tabellen (Accounts, Bestellungen, Votes) für PostgreSQL.
-- Idempotent: kann gefahrlos mehrfach ausgeführt werden.
-- Gerichte/Speiseplan kommen aus dishes-migration.sql.

CREATE TABLE IF NOT EXISTS accounts (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    account_id INTEGER NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    dish_name TEXT NOT NULL,
    price VARCHAR(20) NOT NULL,
    quantity INTEGER NOT NULL
);

-- Freitext-Wünsche von der Abstimmungsseite
CREATE TABLE IF NOT EXISTS wishes (
    id SERIAL PRIMARY KEY,
    account_id INTEGER NOT NULL,
    text TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- dish_id verweist auf die IDs aus Dish::getFlattenedDishes(), nicht auf dishes.id.
CREATE TABLE IF NOT EXISTS votes (
    dish_id INTEGER NOT NULL,
    account_id INTEGER NOT NULL,
    direction VARCHAR(4) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE (dish_id, account_id)
);
