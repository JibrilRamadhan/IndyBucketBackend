#!/bin/sh
set -e

# Pindahkan environment variables dari container ke file .env secara aman agar terbaca oleh PHP/Apache
php -r '
$content = "";
foreach (getenv() as $key => $value) {
    if (preg_match("/^(?:PHP_|APACHE_|GPG_|PHPIZE_|LDFLAGS|CFLAGS|CPPFLAGS|HOME|HOSTNAME|SHLVL|TERM|PWD|PATH)/", $key)) {
        continue;
    }
    $escaped = str_replace(["\\", "\x27"], ["\\\\", "\\\x27"], $value);
    $content .= "{$key}=\x27{$escaped}\x27\n";
}
file_put_contents("/var/www/html/.env", $content);
'

# Ubah kepemilikan file .env agar bisa dibaca oleh Apache (www-data)
chown www-data:www-data /var/www/html/.env

# Jalankan perintah CMD default (misal: apache2-foreground)
exec "$@"
