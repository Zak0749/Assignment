FROM node:18.16.0 as builder

WORKDIR /usr/public/app

COPY package.json .

RUN npm install

COPY ./public .

RUN npx tailwindcss build styles.css -o dist.css

FROM php:8.2.6

WORKDIR /usr/public/app

COPY --from=builder /usr/public/app/dist.css ./

COPY ./public .

EXPOSE 8000

CMD php -S 0.0.0.0:8000 -t .