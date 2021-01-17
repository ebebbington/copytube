FROM php:7.4-fpm

ARG HOST_IP

# Update and install required packages and dependencies
RUN apt-get update -y
RUN apt-get install -y --no-install-recommends libxml2-dev libxslt-dev python-dev
RUN apt-get install -y \
  apt-transport-https apt-utils libc-client-dev libzip-dev libldb-dev libldap2-dev libpng-dev libonig-dev zip unzip curl
RUN apt-get install -y libjpeg62-turbo-dev libfreetype6-dev libmcrypt-dev libjpeg-dev

# Avilable extensions by default when using docker-php-ext-install
# bcmath bz2 calendar ctype curl dba dom enchant exif fileinfo filter ftp gd gettext gmp hash iconv imap interbase intl json ldap mbstring mysqli oci8 odbc opcache pcntl pdo pdo_dblib pdo_firebird pdo_mysql pdo_oci pdo_odbc pdo_pgsql pdo_sqlite pgsql phar posix pspell readline recode reflection session shmop simplexml snmp soap sockets sodium spl standard sysvmsg sysvsem sysvshm tidy tokenizer wddx xml xmlreader xmlrpc xmlwriter xsl zend_test zip
RUN docker-php-ext-install pdo pdo_mysql mysqli xml json ldap mbstring soap gd xsl zip sockets intl opcache
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
# Can add mysqli

# Install Xdebug
RUN yes | pecl install xdebug \
    && echo "[Xdebug]" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey=VSCode" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/xdebug.ini

# Configure php.ini
COPY ./.docker/config/php.ini /etc/php.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHPMailer
# RUN php /usr/lib/composer.phar require phpmailer/phpmailer @stable