#!/bin/bash

echo "Provisioning virtual machine"

if ! [ -f /home/vagrant/.ran_first_setup ];
then
    sudo apt-get update > /dev/null
    locale-gen UTF-8
    #sudo apt-get install language-pack-UTF-8 > /dev/null

    echo "Installing apache"
    sudo apt-get install -y apache2 > /dev/null

    echo "Installing PHP"
    sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt php5-cli php5-common php5-mysql php5-curl php5-intl -y > /dev/null
    sudo a2enmod php5
    sudo a2enmod rewrite
    sudo a2enmod headers
    sudo php5enmod mcrypt

    sudo service apache2 restart

    echo "Preparing Mysql"
    sudo apt-get install debconf-utils -y > /dev/null
    sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password pass1234"
    sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password pass1234"

    echo "Installing Mysql"
    sudo apt-get install mysql-server -y > /dev/null

#    echo "Modifying Hosts file"
#    sudo echo "127.0.1.1 agenda.dev" >> /etc/hosts
#    sudo echo "192.168.33.170 agenda.dev" >> /etc/hosts

    echo "Modifying Hostname file"
    sudo echo "vagrant-agenda" > /etc/hostname

    echo "Installing Composer"
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer

    echo "Installing NodeJS"
    curl -sL https://deb.nodesource.com/setup_5.x | sudo -E bash -
    sudo apt-get install -y nodejs
    sudo npm install npm -g

    echo "Installing nodemon"
    npm install -g nodemon
    echo "Installing node-sass"
    npm install -g node-sass
    
    echo "Installing other utilities"
    sudo apt-get -y install vim nfs-common portmap unzip wget snmp git-core git
    sudo apt-get -y install build-essential libssl-dev

    echo 'Setting up PhpMyAdmin'

    APP_PASS="pass1234"
    ROOT_PASS="pass1234"
    APP_DB_PASS="pass1234"

    echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/app-password-confirm password $APP_PASS" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/mysql/admin-pass password $ROOT_PASS" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/mysql/app-pass password $APP_DB_PASS" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | debconf-set-selections

    apt-get install -y phpmyadmin
    echo 'Copy phinx.yml.dist to phinx.yml'
    \cp -rf /vagrant/rest/phinx.yml.dist /vagrant/conp-rest/phinx.yml

    echo 'Copy local dev db credentials file'
    \cp -rf /vagrant/rest/src/db-credentials.php.dist /vagrant/rest/src/db-credentials.php

    sudo -u vagrant echo 'Already ran first setup' > /home/vagrant/.ran_first_setup
fi

echo 'Copying agenda.dev config file'
# use \cp to avoid using a possible alias instead of the real cp
sudo \cp -rf /vagrant/.vagrant-provision/conf/agenda.dev.conf /etc/apache2/sites-available/agenda.dev.conf
echo 'Enabling agenda.dev'
sudo a2ensite agenda.dev

echo "Creating the build-tools folder and adds package.json there"
mkdir -p /vagrant/build-tools
chown vagrant:vagrant /vagrant/build-tools
rm /vagrant/build-tools/package.json
#sudo -u vagrant ln -s /vagrant/conp-presentation/package.json /vagrant/build-tools/package.json
\cp -rf /vagrant/presentation/package.json /vagrant/build-tools/package.json

echo 'Modifying user and goup for apache'
#This may not be such a good idea but it solves permission issues due to sharing the folder between host and guest
sudo sed -i -- 's/APACHE_RUN_USER=www\-data/APACHE_RUN_USER=vagrant/g' /etc/apache2/envvars
sudo sed -i -- 's/APACHE_RUN_GROUP=www\-data/APACHE_RUN_GROUP=vagrant/g' /etc/apache2/envvars

echo 'Restarting apache'
sudo service apache2 restart

echo 'Creating the db ( only if it was not created previously )'
mysql -uroot -ppass1234 < /vagrant/.vagrant-provision/create_db.sql

#sudo locale-gen UTF-8
#export LANGUAGE=en_US.UTF-8
#export LANG=en_US.UTF-8
#export LC_ALL=en_US.UTF-8
#locale-gen en_US.UTF-8

echo "Finished provisioning"
