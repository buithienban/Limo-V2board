#!/bin/bash

rm -rf composer.phar
wget https://github.com/composer/composer/releases/latest/download/composer.phar -O composer.phar
php composer.phar install -vvv

php_main_version = $(php -v | head -n 1 | cut -d ' ' -f 2 | cut -d '.' -f 1)
if [ $major_version -ge 8 ]; then
    php composer.phar require joanhey/adapterman
    php composer.phar require cedar2025/http-foundation:5.4.x-dev
fi

php artisan v2board:install

if [ -f "/etc/init.d/bt" ]; then
  chown -R www $(pwd);
fi
