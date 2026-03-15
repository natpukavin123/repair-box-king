#!/bin/bash
# init.sh — runs via supervisord AFTER nginx + php-fpm are up.
# Handles: package discovery, config caching, DB wait, migrations, first-install seed.
# Runs ONCE (autorestart=false in supervisor). Failures are logged, not fatal to app.

set -e

echo "[init] ── Starting initialisation ──────────────────────────────────────"

cd /var/www/html

# ── 1. Wait for PHP-FPM to accept connections (up to 30s) ────────────────────
echo "[init] Waiting for PHP-FPM..."
for i in $(seq 1 30); do
    if php-fpm -t 2>/dev/null; then
        break
    fi
    sleep 1
done

# Actually wait for FPM to be listening on 9000
for i in $(seq 1 30); do
    if nc -z 127.0.0.1 9000 2>/dev/null; then
        echo "[init] PHP-FPM ready ✔"
        break
    fi
    sleep 1
done

# ── 2. Generate APP_KEY if not set ───────────────────────────────────────────
if grep -q "^APP_KEY=$" .env 2>/dev/null || ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "[init] Generating APP_KEY..."
    php artisan key:generate --force --no-interaction 2>/dev/null || true
fi

# ── 3. Discover packages + cache for production ───────────────────────────────
echo "[init] Caching config/routes/views..."
php artisan package:discover --ansi --no-interaction 2>/dev/null || true
php artisan config:cache  --no-interaction 2>/dev/null || true
php artisan route:cache   --no-interaction 2>/dev/null || true
php artisan view:cache    --no-interaction 2>/dev/null || true
php artisan storage:link  --no-interaction 2>/dev/null || true

# ── 4. Wait for MySQL ─────────────────────────────────────────────────────────
DB_HOST_V=$(grep "^DB_HOST=" .env | cut -d= -f2)
DB_PORT_V=$(grep "^DB_PORT=" .env | cut -d= -f2)
DB_USER_V=$(grep "^DB_USERNAME=" .env | cut -d= -f2)
DB_PASS_V=$(grep "^DB_PASSWORD=" .env | cut -d= -f2)

echo "[init] Waiting for MySQL at $DB_HOST_V:$DB_PORT_V..."
MAX=40
for i in $(seq 1 $MAX); do
    if mysqladmin ping -h"$DB_HOST_V" -P"$DB_PORT_V" \
        -u"$DB_USER_V" -p"$DB_PASS_V" --silent 2>/dev/null; then
        echo "[init] MySQL ready ✔"
        break
    fi
    if [ "$i" -ge "$MAX" ]; then
        echo "[init] ✗ MySQL not reachable after $MAX attempts. Check DB_HOST/DB_PASSWORD vars."
        exit 1
    fi
    echo "[init]   ... attempt $i/$MAX"
    sleep 3
done

# ── 5. Run migrations ─────────────────────────────────────────────────────────
echo "[init] Running migrations..."
php artisan migrate --force --no-interaction
echo "[init] Migrations done ✔"

# ── 6. First-install check (DB-based, not filesystem — survives Railway redeploys) ─
echo "[init] Checking first-install status..."
SUPER_COUNT=$(mysql -h"$DB_HOST_V" -P"$DB_PORT_V" -u"$DB_USER_V" -p"$DB_PASS_V" \
    --skip-column-names --silent -e \
    "SELECT COUNT(*) FROM \`${DB_DATABASE:-repair_box}\`.\`users\` WHERE is_super_admin=1;" \
    2>/dev/null || echo "0")
SUPER_COUNT=$(echo "$SUPER_COUNT" | tr -d '[:space:]')

if [ "$SUPER_COUNT" = "0" ] || [ -z "$SUPER_COUNT" ]; then
    echo "[init] First install — seeding initial data..."
    php artisan db:seed --class=InitialDataSeeder --force --no-interaction
    echo "[init] Initial data seeded ✔"

    # Auto-create admin from env vars if provided
    ADMIN_EMAIL_V=$(grep "^ADMIN_EMAIL=" .env | cut -d= -f2)
    ADMIN_PASS_V=$(grep "^ADMIN_PASSWORD=" .env | cut -d= -f2)
    ADMIN_NAME_V=$(grep "^ADMIN_NAME=" .env | cut -d= -f2-)

    if [ -n "$ADMIN_EMAIL_V" ] && [ -n "$ADMIN_PASS_V" ]; then
        echo "[init] Creating admin: $ADMIN_EMAIL_V"
        php artisan tinker --execute="
\$role = \App\Models\Role::where('name','Admin')->first();
if (\$role) {
    \App\Models\User::updateOrCreate(
        ['email' => '$ADMIN_EMAIL_V'],
        ['name'=>'$ADMIN_NAME_V','password'=>Hash::make('$ADMIN_PASS_V'),
         'role_id'=>\$role->id,'status'=>'active','is_super_admin'=>true]
    );
    echo 'Admin created';
}
" --no-interaction 2>/dev/null || true
        echo "[init] Admin created ✔"
    else
        echo "[init] ⚠ No ADMIN_EMAIL/ADMIN_PASSWORD set — use /setup wizard on first visit."
    fi
else
    echo "[init] Already installed ($SUPER_COUNT super admin found) ✔"
fi

echo "[init] ── Initialisation complete ───────────────────────────────────────"
