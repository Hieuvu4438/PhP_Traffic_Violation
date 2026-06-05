#!/bin/bash
set -e

# Generate config.php from example if it doesn't exist
if [ ! -f /var/www/html/config/config.php ]; then
    cp /var/www/html/config/config.example.php /var/www/html/config/config.php
    sed -i "s/'db_host' => 'localhost'/'db_host' => 'db'/" /var/www/html/config/config.php
    echo "[Docker] Created config/config.php with db_host=db"
fi

# Ensure uploads directory is writable
mkdir -p /var/www/html/public/assets/uploads
chown -R www-data:www-data /var/www/html/public/assets/uploads

exec apache2-foreground
