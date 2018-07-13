#!/usr/bin/env sh

php /var/www/html/composer.phar install
php-fpm -D && nginx -g "daemon off;"
