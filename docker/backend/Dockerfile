FROM php:8.5-apache
COPY --from=composer:2.7.2 /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y git zip unzip vim task-japanese locales locales-all \
    && locale-gen ja_JP.UTF-8 \
    && echo "export LANG=ja_JP.UTF-8" >> ~/.bashrc
RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/html/
ENV APACHE_DOCUMENT_ROOT /var/www/html/sample_test/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf