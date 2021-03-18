#filename: initvagrant.sh
#!/usr/bin/env bash

###########################################
# + Apache                                #
# + PHP 7.1                               #
# + MySQL 5.6 or MariaDB 10.1             #
# + NodeJs, Git, Composer, etc...         #
###########################################



# ---------------------------------------------------------------------------------------------------------------------
# Variables & Functions
# ---------------------------------------------------------------------------------------------------------------------
APP_DATABASE_NAME='sport_analytics'

echoTitle () {
    echo -e "\033[0;30m\033[42m -- $1 -- \033[0m"
}



# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Virtual Machine Setup'
# ---------------------------------------------------------------------------------------------------------------------
# Update packages
apt-get update -qq
apt-get -y install git curl vim



# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Installing and Setting: Apache'
# ---------------------------------------------------------------------------------------------------------------------
# Install packages
apt-get install -y apache2 libapache2-mod-fastcgi apache2-mpm-worker

# linking Vagrant directory to Apache 2.4 public directory
# rm -rf /var/www
# ln -fs /vagrant /var/www

# Add ServerName to httpd.conf
echo "ServerName localhost" > /etc/apache2/httpd.conf

# Setup hosts file
VHOST=$(cat <<EOF
    <VirtualHost *:80>
      DocumentRoot "/var/www/html"
      ServerName app.dev
      ServerAlias app.dev
      <Directory "/var/www/html">
        AllowOverride All
        Require all granted
      </Directory>
    </VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-enabled/000-default.conf

# Loading needed modules to make apache work
a2enmod actions fastcgi rewrite
sudo service apache2 restart



# ---------------------------------------------------------------------------------------------------------------------
# echoTitle 'MYSQL-Database'
# ---------------------------------------------------------------------------------------------------------------------
# Setting MySQL (username: root) ~ (password: password)
sudo debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password password password'
sudo debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password_again password password'

# Installing packages
apt-get install -y mysql-server-5.6 mysql-client-5.6 mysql-common-5.6

# Setup database
mysql -uroot -ppassword -e "CREATE DATABASE IF NOT EXISTS $APP_DATABASE_NAME;";
mysql -uroot -ppassword -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'password';"
mysql -uroot -ppassword -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'password';"

sudo service mysql restart

# Import SQL file
mysql -u root -p password database < database.sql



# ---------------------------------------------------------------------------------------------------------------------
# echoTitle 'Maria-Database'
# ---------------------------------------------------------------------------------------------------------------------
# Remove MySQL if installed
# sudo service mysql stop
# apt-get remove --purge mysql-server-5.6 mysql-client-5.6 mysql-common-5.6
# apt-get autoremove
# apt-get autoclean
# rm -rf /var/lib/mysql/
# rm -rf /etc/mysql/

# Install MariaDB
# export DEBIAN_FRONTEND=noninteractive
# debconf-set-selections <<< 'mariadb-server-10.0 mysql-server/root_password password root'
# debconf-set-selections <<< 'mariadb-server-10.0 mysql-server/root_password_again password root'
# apt-get install -y mariadb-server

# Set MariaDB root user password and persmissions
# mysql -u root -proot -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION; FLUSH PRIVILEGES;"

# Open MariaDB to be used with Sequel Pro
# sed -i 's|127.0.0.1|0.0.0.0|g' /etc/mysql/my.cnf

# Restart MariaDB
# sudo service mysql restart



# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Installing: PHP'
# ---------------------------------------------------------------------------------------------------------------------
# Add repository
add-apt-repository ppa:ondrej/php
apt-get update
apt-get install -y python-software-properties software-properties-common

# Remove PHP5
# apt-get purge php5-fpm -y
# apt-get --purge autoremove -y

# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Setting: PHP with Apache'
# ---------------------------------------------------------------------------------------------------------------------
sudo apt-get install -y php5 libapache2-mod-php5 php5-mcrypt

# Enable php modules
# php71enmod mcrypt (error)

# Trigger changes in apache
#a2enconf php7.1-fpm

# Install packages
# apt-get install -y php5-cli
rm /var/www/html/index.html
sudo service apache2 restart

# Packages Available:
# apt-cache search php7-*



# ---------------------------------------------------------------------------------------------------------------------
# echoTitle 'Installing & Setting: X-Debug'
# ---------------------------------------------------------------------------------------------------------------------
# cat << EOF | sudo tee -a /etc/php/7.1/mods-available/xdebug.ini
# xdebug.scream=1
# xdebug.cli_color=1
# xdebug.show_local_vars=1
# EOF



# ---------------------------------------------------------------------------------------------------------------------
# Others
# ---------------------------------------------------------------------------------------------------------------------
#echoTitle 'Installing: Node 6 and update NPM'
#curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
#apt-get install -y nodejs
#npm install npm@latest -g

#echoTitle 'Installing: Git'
#apt-get install -y git

#echoTitle 'Installing: Composer'
#curl -s https://getcomposer.org/installer | php
#mv composer.phar /usr/local/bin/composer



# ---------------------------------------------------------------------------------------------------------------------
# Others
# ---------------------------------------------------------------------------------------------------------------------
# Output success message
echoTitle "Your machine has been provisioned"
echo "-------------------------------------------"
echo "-------------------------------------------"
echo "-------------------------------------------"
echo "MySQL is available on port 3306 with username 'root' and password 'password'"
echo "(you have to use 127.0.0.1 as opposed to 'localhost')"
echo "Apache is available on port 80"
echo -e "Head over to http://192.168.100.100 to get started"