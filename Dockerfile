FROM php:8.1.2-apache

RUN apt-get update

# 1. development packages
RUN apt-get install -y libpq-dev libzip-dev libxml2-dev libcurl4-openssl-dev curl


# 2. apache configs + document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

ENV PORT 8080

RUN sed -i "/^\s*Listen 80/c\Listen $PORT" /etc/apache2/*.conf
RUN sed -i "/^\s*<VirtualHost \*:80>/c\<VirtualHost 0.0.0.0:$PORT>" /etc/apache2/sites-available/*.conf

# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers


# 4. start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install mysqli pdo pdo_mysql zip xml curl

# 5. composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN composer install

# 6. npm
ARG NODE_VERSION=16.16.0
ARG NODE_PACKAGE=node-v$NODE_VERSION-linux-x64
ARG NODE_HOME=/opt/$NODE_PACKAGE

ENV NODE_PATH $NODE_HOME/lib/node_modules
ENV PATH $NODE_HOME/bin:$PATH

RUN curl https://nodejs.org/dist/v$NODE_VERSION/$NODE_PACKAGE.tar.gz | tar -xzC /opt/

RUN npm install

RUN mkdir /var/www/html/public/build && chmod 777 /var/www/html/public/build

RUN npm run build
