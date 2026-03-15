#!/bin/bash
set -e

echo "🚀 RepairBox — Container startup"
echo "================================="

cd /var/www/html

# ── 1. Resolve PORT (Railway injects $PORT, default 80) ───────────────────────
APP_PORT="${PORT:-80}"
echo "→ App will listen on port: $APP_PORT"

# Patch nginx config with the actual port
sed -i "s/__PORT__/$APP_PORT/" /etc/nginx/http.d/default.conf

# ── 2. Resolve DB credentials ─────────────────────────────────────────────────
# Railway MySQL plugin provides MYSQLHOST, MYSQLUSER, etc. (no underscore)
# Also support standard DB_HOST, DB_USER etc. for manual config.
RESOLVED_DB_HOST="${DB_HOST:-${MYSQLHOST:-127.0.0.1}}"
RESOLVED_DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
RESOLVED_DB_NAME="${DB_DATABASE:-${MYSQLDATABASE:-repair_box}}"
RESOLVED_DB_USER="${DB_USERNAME:-${MYSQLUSER:-root}}"
RESOLVED_DB_PASS="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"

# ── 3. Write .env from environment variables ──────────────────────────────────
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
DB_HOST=${RESOLVED_DB_HOST}
DB_PORT=${RESOLVED_DB_PORT}
DB_DATABASE=${RESOLVED_DB_NAME}
DB_USERNAME=${RESOLVED_DB_USER}
DB_PASSWORD=${RESOLVED_DB_PASS}

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
ENVEOF

echo ".env written ✔"

# ── 4. Ensure APP_KEY is set ──────────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "→ Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── 5. Discover packages (needed since composer ran with --no-scripts) ────────
echo "→ Discovering packages..."
php artisan package:discover --ansi 2>/dev/null || true

# ── 6. Cache config/routes/views for production ───────────────────────────────
echo "→ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 7. Storage symlink ────────────────────────────────────────────────────────
echo "→ Creating storage symlink..."
php artisan storage:link --no-interaction 2>/dev/null || true

# ── 8. Wait for MySQL to be ready ─────────────────────────────────────────────
echo "→ Waiting for database on ${RESOLVED_DB_HOST}:${RESOLVED_DB_PORT}..."
MAX_TRIES=30
TRIES=0
until mysqladmin ping -h"${RESOLVED_DB_HOST}" -P"${RESOLVED_DB_PORT}" \
    -u"${RESOLVED_DB_USER}" -p"${RESOLVED_DB_PASS}" \
    --silent 2>/dev/null; do
    TRIES=$((TRIES+1))
    if [ "$TRIES" -ge "$MAX_TRIES" ]; then
        echo "✗ DB not reachable after ${MAX_TRIES} attempts. Aborting."
        exit 1
    fi
    echo "  … attempt $TRIES/$MAX_TRIES (retrying in 3s)"
    sleep 3
done
echo "  DB ready ✔"

# ── 9. Run migrations ─────────────────────────────────────────────────────────
echo "→ Running migrations..."
php artisan migrate --force --no-interaction
echo "  Migrations done ✔"

# ── 10. First-install seed check (uses DB, not filesystem) ───────────────────
# Check if a super admin exists — Railway filesystem is ephemeral so we
# cannot rely on storage/installed file surviving container restarts.
echo "→ Checking first-install status..."
SUPER_ADMIN_COUNT=$(php artisan tinker --execute="echo \App\Models\User::where('is_super_admin', true)->count();" 2>/dev/null | tail -1 | tr -d '[:space:]')

if [ "$SUPER_ADMIN_COUNT" = "0" ] || [ -z "$SUPER_ADMIN_COUNT" ]; then
    echo "→ First install — seeding initial system data..."
    php artisan db:seed --class=InitialDataSeeder --force --no-interaction
    echo "  Initial data seeded ✔"

    # Create admin from env vars if provided
    if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ]; then
        echo "→ Creating admin from ADMIN_EMAIL/ADMIN_PASSWORD env vars..."
        php artisan tinker --execute="
use App\Models\User;
use App\Models\Role;
\$role = Role::where('name', 'Admin')->first();
if (\$role) {
    User::updateOrCreate(
        ['email' => '${ADMIN_EMAIL}'],
        [
            'name'           => '${ADMIN_NAME:-Administrator}',
            'password'       => Hash::make('${ADMIN_PASSWORD}'),
            'role_id'        => \$role->id,
            'status'         => 'active',
            'is_super_admin' => true,
        ]
    );
    echo 'Admin created: ${ADMIN_EMAIL}';
}
" 2>/dev/null || true
        echo "  Admin account created ✔"
    else
        echo "  ⚠  No ADMIN_EMAIL/ADMIN_PASSWORD set — use the /setup wizard on first visit."
    fi
else
    echo "  Already installed (found ${SUPER_ADMIN_COUNT} super admin) ✔"
fi

# ── 11. Fix permissions ───────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "================================="
echo "✅ Startup complete — launching on port $APP_PORT"
echo ""

# ── 12. Start Supervisor (manages Nginx + PHP-FPM) ────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
