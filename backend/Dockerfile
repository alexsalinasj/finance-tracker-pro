FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
        git \
        libonig-dev \
        libpng-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-install bcmath gd mbstring pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php-fpm/zz-finance.conf /usr/local/etc/php-fpm.d/zz-finance.conf
COPY docker/php/conf.d/zz-finance.ini /usr/local/etc/php/conf.d/zz-finance.ini

WORKDIR /var/www/html

COPY . .

RUN chmod +x docker-entrypoint.sh \
    && composer install --no-interaction --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["./docker-entrypoint.sh"]
CMD ["php-fpm"]
