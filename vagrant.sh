#!/usr/bin/env bash

sudo debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password_again password root'

sudo apt-get update
sudo apt-get upgrade

sudo apt-get install -y apache2 mysql-server-5.5 php5 php5-mysql php5-gd php5-curl php-apc php5-mcrypt vim unzip

# set web root
rm -rf /var/www/html
mkdir -p /vagrant/app
ln -fs /vagrant/app /var/www/html

# conf apache
sudo echo "export APACHE_RUN_USER=vagrant" >> /etc/apache2/envvars
sudo echo "export APACHE_RUN_GROUP=vagrant" >> /etc/apache2/envvars

sudo a2enmod rewrite

# bind mysql to all
cat /etc/mysql/my.cnf | sed 's/bind-address/#bind-address/' > mymod.cnf
sudo cp mymod.cnf /etc/mysql/my.cnf
rm mymod.cnf

mysql -u root -proot -p -e "CREATE USER 'root'@'%' IDENTIFIED BY 'root'"
mysql -u root -proot -p -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION"
mysql -u root -proot -p -e "UPDATE mysql.user SET Password=PASSWORD('root') WHERE User='root'"
mysql -u root -proot -p -e "FLUSH PRIVILEGES"

# set php timezone
echo "Configuring PHP..."
echo "- Timezone"
wget -q -O tzupdate.zip https://github.com/victorhaggqvist/tzupdate/archive/master.zip
unzip -q tzupdate.zip
sudo echo "date.timezone=\"$(./tzupdate-master/tzupdate -p)\"" >> /etc/php5/apache2/php.ini
echo "- Display Errors = On"
sudo echo "display_errors = On" >> /etc/php5/apache2/php.ini

echo "Configuring Apache site"
sudo rm /etc/apache2/sites-enabled/*
sudo ln -s /vagrant/apache.conf /etc/apache2/sites-enabled/apache.conf

# restart once for all
sudo service apache2 restart
sudo service mysql restart
