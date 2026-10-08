FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libsqlite3-dev \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# สร้างไฟล์ฐานข้อมูล รัน Migration และใส่ข้อมูล Seeder อัตโนมัติ
RUN touch database/database.sqlite
RUN php artisan migrate --force
RUN php artisan db:seed --force

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=8080