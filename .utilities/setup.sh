#!/bin/sh
if command -v docker; then
  docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs
  if [ ! -e .env ]; then 
    cp .env.example .env
    docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest php artisan key:generate
  fi
elif command -v php; then
  composer install --ignore-platform-reqs
  if [ ! -e .env ]; then 
    cp .env.example .env
    php artisan key:generate
  fi
else
  echo "Docker or PHP is not installed"
  exit 1
fi

git config --local core.hooksPath .githooks/
