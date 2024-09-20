#!/bin/sh

php artisan optimize

php-fpm -D &&  nginx -g "daemon off;"
