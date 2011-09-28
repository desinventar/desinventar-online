# This rules are meant to be used inside a <Directory> section of Apache config
# and don't forget to set RewriteBase correctly
RewriteRule ^.*\.inc$ - [F]
RewriteRule ^.*\.txt$ - [F]
RewriteRule ^.*\.tpl$ - [F]
RewriteRule ^.*\.xml$ - [F]
