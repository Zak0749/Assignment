FROM ubuntu:trusty as builder

RUN sudo apt-get -y update

RUN sudo apt-get -y upgrade

RUN sudo apt-get install -y sqlite3 libsqlite3-dev

WORKDIR /usr/public/app

COPY ./database/setup.sql ./database/setup.sql

RUN sqlite3 'database/db.sqlite' < 'database/setup.sql'

FROM php:8.2.6

WORKDIR /usr/public/app

COPY --from=builder /usr/public/app/database/db.sqlite ./database/db.sqlite

COPY ./public ./public

EXPOSE 8000

CMD php -S 0.0.0.0:8000 -t ./public