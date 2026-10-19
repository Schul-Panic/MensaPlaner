#!/bin/bash
# Vorlage für den lokalen Start. Kopieren nach start-local.sh (in .gitignore)
# und dort die Zugangsdaten eintragen:  cp start-local.example.sh start-local.sh

# "sqlite" = lokale Mock-Datenbank, "pgsql" = Online-PostgreSQL
export DB_CONNECTION="sqlite"

# --- nur für DB_CONNECTION=pgsql ---
# Entweder die komplette URL (z.B. "External Database URL" bei Render) ...
export DATABASE_URL=""
# ... oder einzeln (wird nur genutzt, wenn DATABASE_URL leer ist):
# export DB_HOST=""
# export DB_PORT="5432"
# export DB_NAME=""
# export DB_USER=""
# export DB_PASSWORD=""
# export DB_SSLMODE="require"

# --- nur für DB_CONNECTION=sqlite ---
# Standard ist eine Datei im Temp-Verzeichnis; löschen setzt die Mock-Daten zurück.
# export DB_SQLITE_PATH="$PWD/mensaplaner.sqlite"

# --- Login mit Google (leer = Button zeigt "nicht eingerichtet") ---
# Rücksprung-Adresse in der Google Console: http://localhost:8000/auth/google/callback
# export GOOGLE_CLIENT_ID=""
# export GOOGLE_CLIENT_SECRET=""
# export APP_URL="https://…"   # nur nötig, falls die Rücksprung-Adresse falsch erkannt wird

php -S localhost:8000 router.php
