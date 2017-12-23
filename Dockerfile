FROM richarvey/nginx-php-fpm:latest

ENV APP_END=prod \
    WWW_HOME=/var/www/html \
    ERRORS=0 \
    PHP_ERRORS_STDERR=1

VOLUME /var/run/docker.sock

COPY . $WWW_HOME
