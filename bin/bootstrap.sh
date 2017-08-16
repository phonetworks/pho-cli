#!/usr/bin/env bash
sudo apt-get update

# Install php7.1 repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get remove php7.0 -y
sudo apt-get install -y curl php7.1 php7.1-fpm php7.1-cli php7.1-common php7.1-mbstring php7.1-gd php7.1-intl php7.1-xml php7.1-mysql php7.1-mcrypt php7.1-zip
sudo dpkg -i /opt/pho-cli/bin/ubuntu-16.04/libgraphqlparser_0.6.0-0ubuntu1_amd64.deb
sudo dpkg -i /opt/pho-cli/bin/ubuntu-16.04/php-graphql_0.6.0-0ubuntu1_amd64.deb
cd ~
sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
cd /opt/pho-cli
composer install
