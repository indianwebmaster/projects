# apache2_vhost_create.sh
# Manoj Thakur 30-Mar-2014
#
# This script is to auto create a vhost-subdomain under apache2.
# This script works for apache2

basedir=/var/www

domains="\
    domain1.com
    "

for domain in $domains
do
    cd $basedir
    mkdir -p vhosts/$domain/htdocs
    mkdir -p vhosts/log/$domain
    chmod -R 775 vhosts/$domain
    chown -R www-data vhosts/$domain
    chgrp -R wwwuser vhosts/$domain

    echo "<html><head><title>Welcome to $domain</title></head>" > vhosts/$domain/htdocs/index.html
    echo "<body><h2>Welcome to $domain</h2></body>" >> vhosts/$domain/htdocs/index.html
    echo "</html>" >> vhosts/$domain/htdocs/index.html

cat << EndOfApacheConf > /etc/apache2/sites-available/$domain
# domain: $domain
# public: /var/www/vhosts/$domain/htdocs
# log: /var/www/vhosts/log/$domain

<VirtualHost *:80>
    # Admin Email, Server Nam (domain name) and any aliases
    ServerAdmin    webmaster@$domain
    ServerName    $domain
    ServerAlias    www.$domain

    # Index file and Document Root
    DirectoryIndex    index.html index.php
    DocumentRoot    /var/www/vhosts/$domain/htdocs

    # Custom log file locations
    LogLevel    warn
    ErrorLog    /var/www/vhosts/log/$domain/error.log
    CustomLog    /var/www/vhosts/log/$domain/access.log combined
</VirtualHost>
EndOfApacheConf

    a2ensite $domain
done
service apache2 reload

