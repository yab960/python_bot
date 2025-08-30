FROM php:8.1-cli

# Install dependencies and composer
RUN apt-get update && apt-get install -y unzip git libzip-dev libssl-dev && docker-php-ext-install zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080

CMD ["php", "random_server.php"]
