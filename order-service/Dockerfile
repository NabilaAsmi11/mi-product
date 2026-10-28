FROM php:8.2-cli

# Install dependencies sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory di dalam container
WORKDIR /app

# Salin seluruh file proyek ke dalam container
COPY . .

# Install vendor dependencies tanpa paket development
RUN composer install --no-dev --optimize-autoloader

# Expose port aplikasi
EXPOSE 8000

# Jalankan server Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]