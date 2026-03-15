# ─────────────────────────────────────────────────────────────────────────────
# Stage 1: Node — build frontend assets (Tailwind + Vite)
# ─────────────────────────────────────────────────────────────────────────────
FROM node:20-alpine AS node-build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --frozen-lockfile

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/

# Inject a placeholder APP_URL so Vite doesn't complain
ENV VITE_APP_NAME="RepairBox"
RUN npm run build

# ─────────────────────────────────────────────────────────────────────────────
# Stage 2: PHP dependencies (Composer — no dev)
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
    unzip \
    git \
    bash \
    mysql-client \
    # PHP extension dependencies
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev

# ── PHP Extensions ────────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    bcmath \
    mbstring \
    tokenizer \
    xml \
    zip \
    gd \
    intl \
    pcntl \
    opcache

# ── PHP config ─────────────────────────────────────────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf

# ── Nginx config ────────────────────────────────────────────────────────────
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# ── Supervisor config ───────────────────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── App files ────────────────────────────────────────────────────────────────
WORKDIR /var/www/html

COPY --from=composer-build /app .
COPY --from=node-build /app/public/build ./public/build

# ── Permissions ────────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage \
 && chmod -R 755 /var/www/html/bootstrap/cache

# ── Startup entrypoint ──────────────────────────────────────────────────────
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

ENTRYPOINT ["/start.sh"]
