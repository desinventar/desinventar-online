DesInventar Portal - http://www.desinventar.org
(c) 1998-2017 Corporacion OSSO

Requirements
------------
- Apache httpd 2.2
- PHP 5.2/5.3
- php-Smarty 3
- jQuery (>=1.4)
- jQueryUI (>=1.8)

HTTPD Configuration Sample
--------------------------
Alias /desinventar_portal /var/www/desinventar_portal/portal
<Location /desinventar_portal>
	SetEnv DESINVENTAR_CACHEDIR /var/cache/smarty/desinventar/portal
    SetEnv DESINVENTAR_URL http://online.desinventar.org/desinventar/
</Location>

Docker Basic Commands
---------------------
- Rebuild container image
docker build -t desinventar/portal .
- Run container with default build
docker run -p 8000:80 --name portal desinventar/portal
- Run container with share dir for development
docker run -p 8000:80 -v /host/src:/usr/share/desinventar --name portal desinventar/portal
