FROM php:7.2-apache

LABEL maintainer="miguel.gutierrez@correounivalle.edu.co"

RUN rm /etc/apt/preferences.d/no-debian-php

RUN apt-get update && apt-get install -y \
        mariadb-client \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libaio1 \
        libmcrypt-dev \
        libxml2-dev \
        zlib1g-dev \
        php-soap \
        iputils-ping \
        vim \
    && docker-php-ext-install -j$(nproc) iconv gettext mbstring \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install \
            opcache soap \
            mysqli pdo pdo_mysql

#install zip extension
RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-configure zip --with-libzip \
  && docker-php-ext-install zip

#Install postgresql-client
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

RUN a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/apache2.conf \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_11.x | bash - \
        && apt install -y nodejs

# Install yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
        && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
        && apt update \
        && apt install -y yarn

RUN useradd miguel
RUN mkdir /home/miguel
RUN chown miguel /home/miguel

WORKDIR /var/www
