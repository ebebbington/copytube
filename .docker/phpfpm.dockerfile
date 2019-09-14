FROM php:7.3-fpm

# Update and install required packages and dependencies
RUN apt-get update -y && apt-get install apt-transport-https -y
RUN apt-get install apt-utils -y
RUN apt-get install libc-client-dev -y
RUN apt-get install libzip-dev -y
RUN apt-get install -y libxml2-dev libxslt-dev python-dev --no-install-recommends
RUN apt-get install -y libldb-dev libldap2-dev
RUN apt-get install -y libpng-dev

# Avilable extensions by default when using docker-php-ext-install
# bcmath bz2 calendar ctype curl dba dom enchant exif fileinfo filter ftp gd gettext gmp hash iconv imap interbase intl json ldap mbstring mysqli oci8 odbc opcache pcntl pdo pdo_dblib pdo_firebird pdo_mysql pdo_oci pdo_odbc pdo_pgsql pdo_sqlite pgsql phar posix pspell readline recode reflection session shmop simplexml snmp soap sockets sodium spl standard sysvmsg sysvsem sysvshm tidy tokenizer wddx xml xmlreader xmlrpc xmlwriter xsl zend_test zip
RUN docker-php-ext-install pdo pdo_mysql mysqli xml json ldap mbstring soap gd xsl zip sockets
# Can add mysqli

# Configure php.ini
COPY ./.docker/config/phpfpm/php.ini /etc/php.ini

RUN apt-get install vim -y

# Copy entry point script
COPY ./.docker/config/phpfpm/entry-point.sh /etc/entry-point.sh

# Install composer in /usr/lib folder
WORKDIR /usr/lib
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');"

# Install PHPMailer
RUN php /usr/lib/composer.phar require phpmailer/phpmailer @stable
