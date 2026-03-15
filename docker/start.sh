#!/bin/bash
set -e

echo "🚀 RepairBox — Container startup"
echo "================================="

cd /var/www/html

# ── 1. Write .env from Railway environment variables ──────────────────────────
echo "→ Writing .env from environment..."
cat > .env << EOF
APP_NAME="${APP_NAME:-RepairBox}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"
APP_TIMEZONE="${APP_TIMEZONE:-UTC}"

LOG_CHANNEL=stderr
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

CACHE_STORE=${CACHE_STORE:-database}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}

FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_HOST=${MAIL_HOST:-127.0.0.1}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME:-null}
MAIL_PASSWORD=${MAIL_PASSWORD:-null}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-null}
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-hello@example.com}"
MAIL_FROM_NAME="${APP_NAME:-RepairBox}"
EOF

echo ".env written ✔"

# ── 2. Ensure APP_KEY is set ──────────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "→ Generating APP_KEY (no APP_KEY set in environment)..."
    php artisan key:generate --force
fi

# ── 3. Clear and cache config for production ─────────────────────────────────
echo "→ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 4. Storage link ───────────────────────────────────────────────────────────
echo "→ Creating storage symlink..."
php artisan storage:link --no-interaction 2>/dev/null || true

# ── 5. Wait for DB to be ready ────────────────────────────────────────────────
echo "→ Waiting for database connection..."
MAX_TRIES=30
TRIES=0
until php artisan db:show --no-interaction > /dev/null 2>&1; do
    TRIES=$((TRIES+1))
    if [ $TRIES -ge $MAX_TRIES ]; then
        echo "✗ Database not reachable after ${MAX_TRIES} attempts. Aborting."
        exit 1
    fi
    echo "  … waiting for DB (attempt $TRIES/$MAX_TRIES)"
    sleep 2
done
echo "  DB ready ✔"

# ── 6. Run migrations ─────────────────────────────────────────────────────────
echo "→ Running migrations..."
php artisan migrate --force --no-interaction

# ── 7. Run initial seed if not installed ─────────────────────────────────────
if [ ! -f storage/installed ]; then
    echo "→ First run detected — seeding initial data..."
    php artisan db:seed --class=InitialDataSeeder --force --no-interaction
    echo "→ Creating install lock file..."
    echo "$(date -u +"%Y-%m-%d %H:%M:%S UTC")" > storage/installed
fi

# ── 8. Fix permissions ────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "================================="
echo "✅ Startup complete — launching services"
echo ""

# ── 9. Start Supervisor (manages Nginx + PHP-FPM) ─────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
