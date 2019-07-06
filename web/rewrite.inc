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

RewriteCond %{REQUEST_URI} ==/mapserver.php
RewriteRule . - [L]

RewriteRule ^guide/$                          metguide.php [L]
RewriteRule ^wms[/]*$                         mapserver.php [L]
RewriteRule ^wms/worldmap/$                   mapserver.php [L]
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/$        mapserver.php?MAPID=$1&LAYERS=effects [QSA,L]
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/legend/$ mapserver.php?MAPID=$1&LAYER=effects&REQUEST=getlegendgraphic [QSA,L]
RewriteRule ^wms/([a-zA-Z0-9\-\._]+)/(.+)/$   mapserver.php?MAPID=$1&LAYERS=$2 [QSA,L]
RewriteRule ^scripts/(.*)-(.*).js  scripts/$1.js [L]
RewriteRule ^css/(.*)-(.*).css  css/$1.css [L]
#
RewriteRule ^datacards/?$                           index.php?cmd=datacards [QSA,L]
RewriteRule ^([a-zA-Z0-9\-_]+)/?$                    #$1 [NC,NE,R=302]
RewriteRule . index.php [L]
#
