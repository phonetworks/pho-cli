#!/usr/bin/env bash
sudo apt-get update

sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get remove php7.0 -y
sudo apt-get install -y redis-server gcc 
sudo apt-get install -y curl php7.1 php7.1-fpm php7.1-cli php7.1-common php7.1-mbstring php7.1-gd php7.1-intl php7.1-xml php7.1-mysql php7.1-mcrypt php7.1-zip php7.1-dev
sudo dpkg -i /opt/pho-cli/bin/ubuntu-16.04/libgraphqlparser_0.6.0-0ubuntu1_amd64.deb
sudo dpkg -i /opt/pho-cli/bin/ubuntu-16.04/php-graphql_0.6.0-0ubuntu1_amd64.deb
cd /tmp
sudo apt-get install -y composer
sudo mv /etc/php/7.0/mods-available/z_graphql.ini /etc/php/7.1/mods-available/z_graphql.ini
sudo ln -s /etc/php/7.1/mods-available/z_graphql.ini /etc/php/7.1/cli/conf.d/21-graphql.ini
sudo ln -s /usr/lib/php/20151012/graphql.so /usr/lib/php/20160303/graphql.so
git clone https://github.com/dosten/graphql-parser-php
cd graphql-parser-php
sudo phpize
sudo sed -i 's/graphql_parse_string/graphql_parse_string_with_experimental_schema_support/g' graphql.c
sudo ./configure
sudo make && sudo make install
cd /opt/pho-cli
composer install

# see https://github.com/phonetworks/pho-kernel/blob/master/bin/bootstrap.sh
## neo4j
## http://www.exegetic.biz/blog/2016/09/installing-neo4j-ubuntu-16-04/
## http://debian.neo4j.org/
sudo apt install -y htop
sudo apt install -y default-jre default-jre-headless
wget --no-check-certificate -O - https://debian.neo4j.org/neotechnology.gpg.key | sudo apt-key add -
echo 'deb http://debian.neo4j.org/repo stable/' | sudo tee /etc/apt/sources.list.d/neo4j.list
sudo apt update
sudo apt install -y neo4j
sudo rm /var/lib/neo4j/data/dbms/auth
sudo service neo4j restart
curl -H "Content-Type: application/json" -X POST -d '{"password":"password"}' -u neo4j:neo4j http://localhost:7474/user/neo4j/password
## todo
## perhaps remove mysql?
