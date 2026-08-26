FROM dunglas/frankenphp:php8.4

RUN install-php-extensions \
    pdo_mysql \
    mongodb \
    redis \
    zip \
    intl \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-scripts --no-autoloader --no-interaction

COPY . .

RUN composer dump-autoload --optimize
