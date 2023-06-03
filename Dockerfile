FROM node:18.16.0 as css_builder

WORKDIR /usr/public/app

COPY package.json .

RUN npm install

COPY ./public .

RUN npx tailwindcss build styles.css -o dist.css

FROM ubuntu:trusty as db_builder

RUN sudo apt-get -y update

RUN sudo apt-get -y upgrade

RUN sudo apt-get install -y sqlite3 libsqlite3-dev

WORKDIR /usr/public/app

COPY ./database/setup.sql ./database/setup.sql

RUN sqlite3 'database/db.sqlite' < 'database/setup.sql'

FROM php:8.2.6

WORKDIR /usr/public/app

COPY --from=css_builder /usr/public/app/dist.css ./

COPY --from=db_builder /usr/public/app/database/db.sqlite ./database/db.sqlite


COPY ./public ./public

EXPOSE 8000

CMD php -S 0.0.0.0:8000 -t ./public