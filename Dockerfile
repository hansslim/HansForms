FROM node:16-alpine as frontend

ARG MIX_API_BASE_URL
ENV MIX_API_BASE_URL $MIX_API_BASE_URL

WORKDIR /app
COPY package-lock.json package.json ./
RUN npm install

COPY . .
RUN npm run prod && rm -rf node_modules


FROM ubuntu:22.04

ARG DEBIAN_FRONTEND=noninteractive

WORKDIR /app

RUN apt update \
	&& apt upgrade -y \
	&& apt install lsb-release ca-certificates apt-transport-https software-properties-common -y \
	&& add-apt-repository ppa:ondrej/php --yes \
	&& apt install php8.0-common php8.0-dom php8.0-bcmath openssl php8.0-mbstring php8.0-cli php8.0-gd php8.0-zip php8.0-curl php8.0-pgsql -y \
	&& rm -rf /var/lib/apt/lists/*

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
	&& php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
	&& php composer-setup.php \
	&& php -r "unlink('composer-setup.php');"

COPY --from=frontend /app/ .

RUN php composer.phar install

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
