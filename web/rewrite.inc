# This rules are meant to be used inside a <Directory> section of Apache config
# and don't forget to set RewriteBase correctly
#
# Pass through existing files and directories
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule . - [L]
# Do not serve some extensions
RewriteRule ^.*\.inc$ - [F]
RewriteRule ^.*\.txt$ - [F]
RewriteRule ^.*\.tpl$ - [F]
RewriteRule ^.*\.xml$ - [F]
RewriteRule ^kml/([a-zA-Z0-9\-\._]+)/$        thematicmap.php?cmd=getkml&MAPID=$1
RewriteRule ^wms[/]*$                         mapserver.php
RewriteRule ^wms/worldmap/$                   mapserver.php
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/$        mapserver.php?MAPID=$1&LAYERS=effects [QSA]
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/legend/$ mapserver.php?MAPID=$1&LAYER=effects&REQUEST=getlegendgraphic [QSA]
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/(.+)/$   mapserver.php?MAPID=$1&LAYERS=$2 [QSA]
RewriteRule ^scripts/(.*)-(.*).js  scripts/$1.js [L]
RewriteRule ^css/(.*)-(.*).css  css/$1.css [L]
#
RewriteRule ^([a-zA-Z0-9\-_]+)/?$                    #$1 [NC,NE,R=302]
RewriteRule ^([a-zA-Z0-9\-_]+)/([a-zA-Z0-9\-_]+)/(.*)/?$            index.php?RegionId=$1&cmd=$2&params=$3 [QSA]
#