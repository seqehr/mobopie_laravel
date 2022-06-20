FROM php:8.0.2-fpm

RUN docker-php-ext-install pdo pdo_mysql

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && apt-get update \ 
    && apt-get install supervisor

Add  supervisord.conf /etc/supervisord.conf

CMD ["supervisord", "-c", "/etc/supervisord.conf"]