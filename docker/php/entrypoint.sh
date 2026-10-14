#!/bin/sh
set -e

cd /var/www/html

echo "Waiting for MySQL..."
until php -r "
  \$h = getenv('DB_HOST') ?: 'db';
  \$p = getenv('DB_PORT') ?: '3306';
  \$u = getenv('DB_USERNAME') ?: 'ichat';
  \$w = getenv('DB_PASSWORD') ?: 'ichat_secret';
  new PDO(\"mysql:host=\$h;port=\$p\", \$u, \$w);
" 2>/dev/null; do
  sleep 2
done
echo "MySQL is ready."

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
mkdir -p bootstrap/cache
mkdir -p public/uploads/videoupload public/uploads/imageupload public/uploads/fileupload

chown -R www-data:www-data storage bootstrap/cache public/uploads 2>/dev/null || true
chmod -R 775 storage bootstrap/cache public/uploads 2>/dev/null || true

if [ ! -f vendor/autoload.php ]; then
  echo "Installing Composer dependencies (first start may take a few minutes)..."
  composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs --no-scripts
  php artisan package:discover --no-interaction 2>/dev/null || true
fi

php artisan storage:link 2>/dev/null || true

exec docker-php-entrypoint "$@"
