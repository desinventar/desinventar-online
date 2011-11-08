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
RewriteRule ^admin[/]*$       index.php?cmd=cmdAdminMain
RewriteRule ^admin/users[/]*$ index.php?cmd=cmdAdminUsers
RewriteRule ^admin/db[/]*$    index.php?cmd=cmdAdminDB
#
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z0-9\-_]+)/?$                    index.php?RegionId=$1
RewriteRule ^([a-zA-Z0-9\-_]+)/([a-zA-Z0-9\-_]+)/?$  index.php?RegionId=$1&cmd=$2
# Rewrite URLs of the form 'x' to the form 'index.php?q=x'.
#RewriteCond %{REQUEST_URI} !=/favicon.ico
#RewriteCond %{REQUEST_URI} !=/header.php
#RewriteCond %{REQUEST_URI} !=/footer.php
#RewriteRule ^(.*)$ index.php?cmd=$1 [L,QSA]
