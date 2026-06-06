# Ubah versi ini menjadi 8.4
FROM php:8.4-apache

# Install ekstensi sistem yang dibutuhkan
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libpq-dev

# Install ekstensi PHP (pdo_pgsql sangat penting untuk Supabase!)
RUN docker-php-ext-install pdo pdo_pgsql zip

# Aktifkan mod_rewrite Apache untuk URL Laravel
RUN a2enmod rewrite

# Atur folder public Laravel sebagai akar web
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Salin semua file proyek ke dalam container
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Beri izin akses folder storage agar Laravel tidak error permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Gunakan script entrypoint untuk memindahkan env ke file .env saat startup
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]