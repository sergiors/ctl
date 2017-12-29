FROM richarvey/nginx-php-fpm:latest

ENV APP_ENV=prod \
    WWW_HOME=/var/www/html \
    ERRORS=0 \
    PHP_ERRORS_STDERR=1

ENV LOGLEVEL=ERROR \
    LOGFILE=$WWW_HOME/cache/error.log

VOLUME /var/run/docker.sock

RUN mkdir -p $WWW_HOME/cache \
    && chmod 777 $WWW_HOME/cache

COPY . $WWW_HOME
