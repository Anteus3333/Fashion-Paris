#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
  echo "[docker] composer install…"
  composer install --no-interaction --prefer-dist --ignore-platform-req=ext-mongodb
fi

mkdir -p logs
chown -R www-data:www-data logs 2>/dev/null || true

exec apache2-foreground
