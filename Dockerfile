FROM php:8.1-fpm

# Установка всех необходимых зависимостей
RUN apt-get update && \
    apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    unzip \
    git \
    curl

# Установка расширений PHP
RUN docker-php-ext-install pdo_mysql zip gd pgsql pdo_pgsql

# Установка Xdebug для отладки
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копирование файлов проекта и установка зависимостей
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-autoloader --no-scripts --no-progress --no-interaction

COPY . .

# Генерация автозагрузчика
RUN composer dump-autoload

# Копирование всей папки vendor
COPY ./vendor ./vendor


