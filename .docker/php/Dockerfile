# Utilisation de l'image PHP officielle basée sur Alpine Linux
FROM php:8.2-fpm-alpine

# Se placer dans le répertoire de travail
WORKDIR /var/www

# Installer les dépendances nécessaires
RUN apk update && apk add --no-cache \
	build-base \
	libpng-dev \
	libjpeg-turbo-dev \
	freetype-dev \
	libzip \
	jpegoptim \
	optipng \
	pngquant \
	gifsicle \
	vim \
	unzip \
	curl \
	postgresql-dev \
	git

# Nettoyer le cache
RUN rm -rf /var/cache/apk/*

# Installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_pgsql pgsql gd

# Exposer le port 9000
EXPOSE 9000