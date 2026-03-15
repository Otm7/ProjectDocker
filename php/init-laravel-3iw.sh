#!/bin/bash

set -e

cd /var/www

if [ -f .env.example ] && [ ! -f .env ]; then
  cp .env.example .env
fi

# Only install dependencies if they are missing (speeds up rebuilds)
if [ ! -d vendor ] || [ ! -f composer.lock ]; then
  composer install --no-interaction --prefer-dist
else
  echo "Composer dependencies already installed, skipping."
fi

if [ ! -d node_modules ]; then
  npm install
  npm run build
else
  echo "Node dependencies already installed, skipping build."
fi

php artisan key:generate --ansi

php artisan migrate:fresh --seed --force

php-fpm -F
