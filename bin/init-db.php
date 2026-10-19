<?php

// Richtet eine leere PostgreSQL-Datenbank ein: Schema, Speiseplan und ein Admin-Account.
// Mehrfaches Ausführen ist unkritisch, vorhandene Daten bleiben erhalten.
//
//   DATABASE_URL="postgres://user:pass@host:5432/db" \
//   ADMIN_EMAIL="admin@schule.de" ADMIN_PASSWORD="..." \
//   php bin/init-db.php

require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Model\Account;

// Ohne explizites pgsql würde Database bei Fehlern still auf SQLite ausweichen.
if (Database::driver() === '') {
    putenv('DB_CONNECTION=pgsql');
}

$db = Database::connection();

if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
    fwrite(STDERR, "Nur für PostgreSQL gedacht. Die SQLite-Mock-DB richtet sich beim ersten Request selbst ein.\n");
    exit(1);
}

$migrations = __DIR__ . '/../migrations';

$db->exec(file_get_contents("{$migrations}/base-schema.sql"));
echo "Basis-Tabellen ok\n";

// dishes-migration.sql löscht die Gerichte-Tabellen vorab, daher nur auf leerer DB ausführen.
if ($db->query("SELECT to_regclass('public.dishes')")->fetchColumn() === null) {
    $db->exec(file_get_contents("{$migrations}/dishes-migration.sql"));
    echo "Gerichte & Speiseplan angelegt\n";
} else {
    echo "Gerichte-Tabellen existieren bereits, übersprungen\n";
}

$hasAdmin = (bool) $db->query("SELECT 1 FROM accounts WHERE role = 'admin' LIMIT 1")->fetchColumn();
$adminEmail = getenv('ADMIN_EMAIL') ?: 'admin@mensa.local';
$adminPassword = getenv('ADMIN_PASSWORD');

if ($hasAdmin) {
    echo "Admin-Account vorhanden\n";
} elseif ($adminPassword) {
    (new Account())->create('Admin', $adminEmail, $adminPassword, 'admin');
    echo "Admin-Account {$adminEmail} angelegt\n";
} else {
    echo "Kein Admin vorhanden: mit ADMIN_PASSWORD (optional ADMIN_EMAIL) erneut ausführen\n";
}
