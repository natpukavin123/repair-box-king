#!/bin/bash
set -e

echo "🚀 RepairBox — Boot"
echo "==================="

cd /var/www/html

# ── 1. Resolve PORT ───────────────────────────────────────────────────────────
APP_PORT="${PORT:-80}"
echo "→ Port: $APP_PORT"
sed -i "s/__PORT__/$APP_PORT/g" /etc/nginx/http.d/default.conf

# ── 2. Resolve DB credentials (Railway uses MYSQLHOST / standard uses DB_HOST) ─
export DB_HOST="${DB_HOST:-${MYSQLHOST:-127.0.0.1}}"
export DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
export DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-repair_box}}"
export DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-root}}"
export DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"

# ── 3. Write .env quickly (no artisan yet) ────────────────────────────────────
echo "→ Writing .env..."
cat > .env << ENVEOF
APP_NAME="${APP_NAME:-RepairBox}"
APP_ENV="${APP_ENV:-production}"
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
APP_TIMEZONE=${APP_TIMEZONE:-UTC}

LOG_CHANNEL=stderr
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=

CACHE_STORE=${CACHE_STORE:-database}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}

FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
BROADCAST_CONNECTION=log

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_HOST=${MAIL_HOST:-127.0.0.1}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME:-null}
MAIL_PASSWORD=${MAIL_PASSWORD:-null}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-null}
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-hello@example.com}"
MAIL_FROM_NAME="${APP_NAME:-RepairBox}"

ADMIN_EMAIL=${ADMIN_EMAIL:-}
ADMIN_PASSWORD=${ADMIN_PASSWORD:-}
ADMIN_NAME=${ADMIN_NAME:-Administrator}
ENVEOF
echo ".env written ✔"

# ── 4. Fix permissions so www-data can write ──────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==================="
echo "→ Starting services (Nginx + PHP-FPM + Init)..."

# ── 5. Hand off to supervisor — Nginx + PHP-FPM start immediately ─────────────
# The 'init' program inside supervisord runs migrations after FPM is ready.
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
