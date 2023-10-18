FROM ubuntu:trusty as builder

RUN sudo apt-get -y update

RUN sudo apt-get -y upgrade

RUN sudo apt-get install -y sqlite3 libsqlite3-dev

COPY ./database/setup.sql .

RUN sqlite3 'db.sqlite' < 'setup.sql'

FROM php:apache

RUN docker-php-ext-install -j "$(nproc)" opcache
RUN set -ex; \
  { \
    echo "; Cloud Run enforces memory & timeouts"; \
    echo "memory_limit = -1"; \
    echo "max_execution_time = 0"; \
    echo "; File upload at Cloud Run network limit"; \
    echo "upload_max_filesize = 32M"; \
    echo "post_max_size = 32M"; \
    echo "; Configure Opcache for Containers"; \
    echo "opcache.enable = On"; \
    echo "opcache.validate_timestamps = Off"; \
    echo "; Configure Opcache Memory (Application-specific)"; \
    echo "opcache.memory_consumption = 32"; \
  } > "$PHP_INI_DIR/conf.d/cloud-run.ini"

WORKDIR /var/www

RUN a2enmod rewrite

COPY --from=builder db.sqlite ./database/db.sqlite

COPY public/ html

COPY src/ src
COPY php.ini .
COPY .htaccess .


RUN chmod 777 ./database/db.sqlite
RUN chmod 777 ./database


# Use the PORT environment variable in Apache configuration files.
# https://cloud.google.com/run/docs/reference/container-contract#port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

