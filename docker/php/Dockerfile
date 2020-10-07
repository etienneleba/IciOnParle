FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
      wget \
      git \
      fish \
      unzip

RUN apt-get update && apt-get install -y libzip-dev libicu-dev && docker-php-ext-install pdo zip intl

# Support de MySQL
RUN docker-php-ext-install mysqli pdo_mysql

# Xdebug
# RUN pecl install xdebug-2.8.1 && docker-php-ext-enable xdebug

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# ADD xdebug.ini /usr/local/etc/php/conf.d/

ARG USER_ID
ARG GROUP_ID

RUN groupadd -f --gid $GROUP_ID user
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID user
USER user

WORKDIR /usr/src/

COPY ./ /usr/src/