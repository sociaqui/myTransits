FROM php:7.0-fpm-alpine
ARG TIMEZONE

# install xdebug
RUN apk add --no-cache $PHPIZE_DEPS && pecl install xdebug-2.5.0 && docker-php-ext-enable xdebug

# install mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

# Add alias for symfony ease of usage
RUN echo 'alias sf="php bin/console"' >> ~/.bashrc

WORKDIR /var/www/symfony