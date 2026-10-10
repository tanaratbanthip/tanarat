FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libsqlite3-dev \
    nodejs \
    npm

# ติดตั้ง pdo_mysql ร่วมกับ zip และ sqlite
RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite zip

# ปรับค่าขนาดไฟล์อัปโหลดให้รองรับรูปภาพขนาดใหญ่
RUN echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 25M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# สร้างโฟลเดอร์ storage ให้ครบและเปิดสิทธิ์การเขียนไฟล์
RUN mkdir -p storage/app/public/posts storage/framework/sessions storage/framework/views storage/framework/cache
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-c", "php artisan storage:link && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080"]
