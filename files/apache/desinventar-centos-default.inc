<Location />
	Order allow,deny
	Allow from all
</Location>
<Directory /usr/share/desinventar/web>
	RewriteEngine on
	RewriteBase /
	Include /usr/share/desinventar/web/rewrite.inc
</Directory>

Alias /desinventar-data /var/www/desinventar
<Location /desinventar-data>
	Order deny,allow
	Allow from all
	Allow from 127.0.0.1
</Location>

