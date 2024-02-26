# FROM ubuntu:trusty as builder

# RUN sudo apt-get -y update

# RUN sudo apt-get -y upgrade

# RUN sudo apt-get install -y sqlite3 libsqlite3-dev

# COPY ./database/setup.sql .

# RUN sqlite3 'db.sqlite' < 'setup.sql'

FROM php:apache

RUN docker-php-ext-install -j "$(nproc)" opcache


WORKDIR /var/www

RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod cache
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql



# COPY --from=builder db.sqlite ./database/db.sqlite

COPY public/ html

COPY src/ src
COPY php.ini .
COPY .htaccess .



# RUN chmod 777 ./database/db.sqlite
# RUN chmod 777 ./database

# Use the PORT environment variable in Apache configuration files.
# https://cloud.google.com/run/docs/reference/container-contract#port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

