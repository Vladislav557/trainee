FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    wget \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    librdkafka-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    apt-transport-https \
    lsb-release \
    ca-certificates \
    gnupg 

RUN docker-php-ext-install pdo pdo_pgsql intl gd zip opcache sockets curl 

RUN pecl install apcu \
    && docker-php-ext-enable apcu


RUN pecl install rdkafka 
RUN docker-php-ext-enable rdkafka

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /usr/local/share/ca-certificates/Yandex && \
    wget "https://storage.yandexcloud.net/cloud-certs/CA.pem" -O /usr/local/share/ca-certificates/Yandex/YandexCA.crt

WORKDIR /var/www/symfony

COPY . /var/www/symfony