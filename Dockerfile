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
    echo "; Fil