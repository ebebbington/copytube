#!/bin/sh

# Create required directories
mkdir -p /var/www/rte_voicemails
mkdir -p /var/www/voicemail
mkdir -p /var/www/callrecordings
mkdir -p /var/www/rte_data

# start php-fpm
php-fpm
