# This rules are meant to be used inside a <Directory> section of Apache config
# and don't forget to set RewriteBase correctly
# Disable Access to some files
RewriteRule ^.*\.inc$ - [F]
RewriteRule ^.*\.txt$ - [F]
RewriteRule ^.*\.tpl$ - [F]
RewriteRule ^.*\.xml$ - [F]
# Special Commands by URL
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^kml/([a-zA-Z0-9\-\._]+)/$        thematicmap.php?cmd=getkml
RewriteRule ^wms[/]*$                         mapserver.php
RewriteRule ^wms/worldmap/$                   mapserver.php
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/$        mapserver.php?MAPID=$1&LAYERS=effects [QSA]
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/legend/$ mapserver.php?MAPID=$1&LAYER=effects&REQUEST=getlegendgraphic [QSA]
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/(.+)/$   mapserver.php?MAPID=$1&LAYERS=$2 [QSA]
RewriteRule ^admin[/]*$       index.php?cmd=cmdAdminMain
RewriteRule ^admin/users[/]*$ index.php?cmd=cmdAdminUsers
RewriteRule ^admin/db[/]*$    index.php?cmd=cmdAdminDB
#
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z0-9\-_]+)/?$                    index.php?RegionId=$1
RewriteRule ^([a-zA-Z0-9\-_]+)/datacards[/]$         index.php?RegionId=$1&cmd=cmdDatacards
RewriteRule ^([a-zA-Z0-9\-_]+)/events[/]$            index.php?RegionId=$1&cmd=cmdDatabaseEvents
RewriteRule ^([a-zA-Z0-9\-_]+)/users[/]$             index.php?RegionId=$1&cmd=cmdDatabaseUsers
RewriteRule ^([a-zA-Z0-9\-_]+)/user/account[/]$      index.php?RegionId=$1&cmd=cmdUserAccount
RewriteRule ^([a-zA-Z0-9\-_]+)/geolevels[/]$         index.php?RegionId=$1&cmd=cmdGeolevels
RewriteRule ^([a-zA-Z0-9\-_]+)/([a-zA-Z0-9\-_]+)/?$  index.php?RegionId=$1&cmd=$2
#
# Rewrite URLs of the form 'x' to the form 'index.php?q=x'.
#RewriteCond %{REQUEST_URI} !=/favicon.ico
#RewriteCond %{REQUEST_URI} !=/header.php
#RewriteCond %{REQUEST_URI} !=/footer.php
#RewriteRule ^(.*)$ index.php?cmd=$1 [L,QSA]
