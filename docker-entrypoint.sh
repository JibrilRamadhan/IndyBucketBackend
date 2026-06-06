#!/bin/sh
set -e

# Pindahkan environment variables dari container ke file .env agar terbaca oleh PHP/Apache
env > /var/www/html/.env

# Ubah kepemilikan file .env agar bisa dibaca oleh Apache (www-data)
chown www-data:www-data /var/www/html/.env

# Jalankan perintah CMD default (misal: apache2-foreground)
exec "$@"
