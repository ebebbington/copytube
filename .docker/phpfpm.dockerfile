FROM php:8.2.11-fpm

# Update and install required packages and dependencies
#RUN apt-get install -y --no-install-recommends libxslt-dev

RUN apt-get update -y \
  && apt-get install -y libpng-dev unzip curl libjpeg-dev libzip-dev libjpeg62-turbo-dev libfreetype6-dev npm
# or libc-client-dev, libonig-dev, apt-transport-https, apt-utils, libmcrypt-dev

RUN npm i npm@latest -g

# Avilable extensions by default when using docker-php-ext-install
# bcmath bz2 calendar ctype curl dba dom enchant exif fileinfo filter ftp gd gettext gmp hash iconv imap interbase intl json ldap mbstring mysqli oci8 odbc opcache pcntl pdo pdo_dblib pdo_firebird pdo_mysql pdo_oci pdo_odbc pdo_pgsql pdo_sqlite pgsql phar posix pspell readline recode reflection session shmop simplexml snmp soap sockets sodium spl standard sysvmsg sysvsem sysvshm tidy tokenizer wddx xml xmlreader xmlrpc xmlwriter xsl zend_test zip
RUN docker-php-ext-install pdo_mysql zip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd

ARG XDEBUG
ARG XDEBUG_MODE

RUN if [ $XDEBUG ]; then \
  pecl install xdebug \
    && echo "[Xdebug]" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=$XDEBUG_MODE" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey=VSCode" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=true" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9001" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && docker-php-ext-enable xdebug; \
fi

# Configure php.ini
COPY ./.docker/config/php.ini /etc/php.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/copytube

COPY src/copytube/composer.json src/copytube/composer.lock ./
RUN composer install --no-scripts --no-autoloader

COPY src/copytube/resources resources
COPY src/copytube/package.json src/copytube/package-lock.json src/copytube/webpack.mix.js src/copytube/tsconfig.json ./
COPY src/copytube/public public

RUN npm ci --prefer-offline --no-audit --progress=false && npm run prod

COPY src/copytube/. .

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/copytube/storage
