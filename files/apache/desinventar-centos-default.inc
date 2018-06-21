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

Alias /desinventar-data /var/local/desinventar/tmp/data
<Location /desinventar-data>
	Order deny,allow
	Allow from all
	Allow from 127.0.0.1
</Location>

Alias /graphs /var/local/desinventar/tmp/graphs
<Location /graphs>
	Order deny,allow
	Allow from all
	Allow from 127.0.0.1
</Location>
