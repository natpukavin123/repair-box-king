# ─────────────────────────────────────────────────────────────────────────────
# Stage 1: Node — build frontend assets (Tailwind + Vite)
# Cache bust: 2026-03-15-v1
# ─────────────────────────────────────────────────────────────────────────────
FROM node:20-alpine AS node-build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --frozen-lockfile

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/

ENV VITE_APP_NAME="RepairBox"
RUN npm run build

# ─────────────────────────────────────────────────────────────────────────────
# Stage 2: PHP dependencies (Composer — production only, no dev)
# ─────────────────────────────────────────────────────────────────────────────
FROM composer:2.7 AS composer-build

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs \
    --no-interaction

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ─────────────────────────────────────────────────────────────────────────────
# Stage 3: Final production image — PHP 8.2 FPM + Nginx
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS production

# ── System packages ──────────────────────────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    bash \
    netcat-openbsd \
    mysql-client \
    # PHP extension build dependencies
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev

# ── PHP Extensions ────────────────────────────────────────────────────────────
# IMPORTANT: tokenizer, xml, mbstring, json, ctype are BUNDLED in php:8.2-fpm-alpine.
# Do NOT re-install them — it causes "No rule to make target" build errors in Alpine.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    bcmath \
    zip \
    gd \
    intl \
    pcntl \
    opcache

# ── PHP config ─────────────────────────────────────────────────────────────
COPY docker/php/php.ini    /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf

# ── Nginx config ─────────────────────────────────────────────────────────────
# __PORT__ is replaced at container startup by start.sh using Railway's $PORT
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# ── Supervisor config ───────────────────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── App files ─────────────────────────────────────────────────────────────────
WORKDIR /var/www/html

COPY --from=composer-build /app .
COPY --from=node-build /app/public/build ./public/build

# ── Permissions ──────────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage \
 && chmod -R 755 /var/www/html/bootstrap/cache

# ── Startup entrypoint ────────────────────────────────────────────────────────
COPY docker/start.sh /start.sh
COPY docker/init.sh  /init.sh
RUN chmod +x /start.sh /init.sh

# Railway dynamically assigns a port via the PORT env var.
# Nginx is configured at runtime to listen on $PORT (not hardcoded 80).
EXPOSE 80

ENTRYPOINT ["/start.sh"]
