# This rules are meant to be used inside a <Directory> section of Apache config
# and don't forget to set RewriteBase correctly
# Disable Access to some files
RewriteRule ^.*\.inc$ - [F]
RewriteRule ^.*\.txt$ - [F]
RewriteRule ^.*\.tpl$ - [F]
RewriteRule ^.*\.xml$ - [F]
# Special Commands by URL
RewriteRule ^admin[/]*$      index.php?cmd=cmdAdminMain
RewriteRule ^admin/users[/]* index.php?cmd=cmdAdminUsers
RewriteRule ^admin/db[/]*    index.php?cmd=cmdAdminDB
# Rewrite URLs of the form 'x' to the form 'index.php?q=x'.
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !=/favicon.ico
RewriteCond %{REQUEST_URI} !=/header.php
RewriteCond %{REQUEST_URI} !=/footer.php
RewriteRule ^(.*)$ index.php?cmd=$1 [L,QSA]
