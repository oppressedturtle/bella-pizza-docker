FROM php:8.2-apache

RUN a2enmod rewrite ssl


RUN docker-php-ext-install mysqli pdo pdo_mysql


RUN mkdir -p /etc/apache2/ssl


COPY certs/selfsigned.crt /etc/apache2/ssl/selfsigned.crt
COPY certs/selfsigned.key /etc/apache2/ssl/selfsigned.key


RUN a2ensite default-ssl


