<Location />
	Order allow,deny
	Allow from all
	Header set Access-Control-Allow-Origin "*"
</Location>

<Directory /var/www/html/web>
	RewriteEngine on
	RewriteBase /
	Include /var/www/html/web/rewrite.inc
</Directory>

Alias /download /var/tmp/desinventar
<Location /download>
	Order deny,allow
	Allow from all
	Allow from 127.0.0.1
</Location>
