# This rules are meant to be used inside a <Directory> section of Apache config
# and don't forget to set RewriteBase correctly
RewriteRule ^.*\.inc$ - [F]
RewriteRule ^.*\.txt$ - [F]
RewriteRule ^.*\.tpl$ - [F]
RewriteRule ^.*\.xml$ - [F]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(gar-isdr-[0-9]+)/?$  index.php?portaltype=$1
#RewriteCond %{REQUEST_FILENAME} !-f
#RewriteCond %{REQUEST_FILENAME} !-d
#RewriteRule ^gar-isdr-([0-9]+)/(.*)$ $2 [L,QSA]
