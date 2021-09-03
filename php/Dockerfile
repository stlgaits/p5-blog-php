FROM php:8-fpm-alpine3.13

RUN apk update; \
    apk upgrade;

# Install git
RUN apk add bash git

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Install pdo extension
RUN docker-php-ext-install pdo_mysql

# Install Composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php && php -r "unlink('composer-setup.php');" && mv composer.phar /usr/local/bin/composer


