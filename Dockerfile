FROM ubuntu:trusty as builder

RUN sudo apt-get -y update

RUN sudo apt-get -y upgrade

RUN sudo apt-get install -y sqlite3 libsqlite3-dev

COPY ./database/setup.sql .

RUN sqlite3 'db.sqlite' < 'setup.sql'

FROM php:apache

WORKDIR /var/www

COPY --from=builder db.sqlite ./database/db.sqlite

COPY public/ html

COPY src/ src

EXPOSE 80
