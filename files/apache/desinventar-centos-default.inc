<Location />
	Order allow,deny
	Allow from all
</Location>
<Directory /usr/share/desinventar/web>
	RewriteEngine on
	RewriteBase /
	Include /usr/share/desinventar/web/rewrite.inc
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

Alias /api /usr/share/desinventar/api/web
<Location /api>
	Order deny,allow
	Allow from all
	Allow from 127.0.0.1
</Location>
<Directory /usr/share/desinventar/api/web>
	Options -MultiViews
    RewriteEngine On
    RewriteBase /api
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [QSA,L]
</Directory>
