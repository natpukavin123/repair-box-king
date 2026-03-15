#!/bin/bash
# init.sh — runs via supervisord AFTER nginx + php-fpm are up.
# Uses exported env vars from start.sh directly (no grep from .env).

set -e

echo "[init] ── Starting initialisation ──────────────────────────────────────"

cd /var/www/html

# ── 1. Wait for PHP-FPM on port 9000 ─────────────────────────────────────────
echo "[init] Waiting for PHP-FPM on port 9000..."
for i in $(seq 1 30); do
    if nc -z 127.0.0.1 9000 2>/dev/null; then
        echo "[init] PHP-FPM ready ✔"
        break
    fi
    sleep 1
done

# ── 2. Generate APP_KEY if missing ────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "[init] Generating APP_KEY..."
    php artisan key:generate --force --no-interaction 2>/dev/null || true
fi

# ── 3. Cache config / routes / views ─────────────────────────────────────────
echo "[init] Caching config/routes/views..."
php artisan package:discover --ansi  --no-interaction 2>/dev/null || true
php artisan config:cache             --no-interaction 2>/dev/null || true
php artisan route:cache              --no-interaction 2>/dev/null || true
php artisan view:cache               --no-interaction 2>/dev/null || true
php artisan storage:link             --no-interaction 2>/dev/null || true

# ── 4. Wait for MySQL using env vars directly (not grep from .env) ────────────
# start.sh exports: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
echo "[init] Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."

MAX=40
for i in $(seq 1 $MAX); do
    # Use MYSQL_PWD env var to avoid password-on-cmdline warning
    if MYSQL_PWD="$DB_PASSWORD" mysqladmin ping \
        -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" \
        --connect-timeout=5 --silent 2>/dev/null; then
        echo "[init] MySQL ready ✔"
        break
    fi
    if [ "$i" -ge "$MAX" ]; then
        echo "[init] ✗ MySQL not reachable after $MAX attempts."
        echo "[init]   DB_HOST=$DB_HOST  DB_PORT=$DB_PORT  DB_USERNAME=$DB_USERNAME"
        echo "[init]   Check your Railway MySQL variables (DB_HOST / MYSQLHOST)."
        exit 1
    fi
    echo "[init]   ... attempt $i/$MAX"
    sleep 3
done

# ── 5. Run migrations ─────────────────────────────────────────────────────────
echo "[init] Running migrations..."
php artisan migrate --force --no-interaction
echo "[init] Migrations done ✔"

# ── 6. First-install check (DB-based — survives ephemeral Railway filesystem) ─
echo "[init] Checking first-install..."
SUPER_COUNT=$(MYSQL_PWD="$DB_PASSWORD" mysql \
    -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" \
    --skip-column-names --silent \
    -e "SELECT COUNT(*) FROM \`${DB_DATABASE}\`.\`users\` WHERE is_super_admin=1;" \
    2>/dev/null || echo "0")
SUPER_COUNT=$(echo "$SUPER_COUNT" | tr -d '[:space:]')

if [ "$SUPER_COUNT" = "0" ] || [ -z "$SUPER_COUNT" ]; then
    echo "[init] First install — seeding initial data..."
    php artisan db:seed --class=InitialDataSeeder --force --no-interaction
    echo "[init] Initial data seeded ✔"

    # Auto-create admin from env vars if provided
    if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ]; then
        echo "[init] Creating admin: $ADMIN_EMAIL"
        MYSQL_PWD="$DB_PASSWORD" mysql \
            -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" "$DB_DATABASE" \
            -e "
INSERT INTO users (name, email, password, role_id, status, is_super_admin, created_at, updated_at)
SELECT
    '${ADMIN_NAME:-Administrator}',
    '${ADMIN_EMAIL}',
    '$(php -r \"echo password_hash('${ADMIN_PASSWORD}', PASSWORD_BCRYPT, ['cost'=>12]);\")',
    (SELECT id FROM roles WHERE name='Admin' LIMIT 1),
    'active',
    1,
    NOW(), NOW()
ON DUPLICATE KEY UPDATE
    name   = VALUES(name),
    password = VALUES(password),
    is_super_admin = 1,
    status = 'active';
" 2>/dev/null || true
        echo "[init] Admin created ✔"
    else
        echo "[init] ⚠ No ADMIN_EMAIL/ADMIN_PASSWORD — use /setup wizard on first visit."
    fi
else
    echo "[init] Already installed ($SUPER_COUNT super admin found) ✔"
fi

echo "[init] ── Initialisation complete ───────────────────────────────────────"
