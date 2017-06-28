<?php
return array(
    // Default settings
    'general' => array(
    ),
    // Default paths
    'smarty' => array(
        'cachedir' => '/var/cache/smarty/desinventar',
    ),
    'paths' => array(
        'libs_url' => '//cdn-desinventar.inticol.com/js',
    ),
    'maps' => array(
        'mapserver_uri' => '/cgi-bin/mapserv',
        'mapserver' => 'mapserv',
        'proj_lib' => '/usr/share/proj',
        'worldmap_dir' => '/var/lib/desinventar/worldmap',
    ),
    'flags' => array(
        'mode' => 'normal',
        'env' => 'online',
        'debug' => false,
        'auto_login' => 0,
        'general_secure_login' => 0,
    ),
    // Database connection definition (default is sqlite)
    'database' => array(
        'driver' => 'sqlite',
        'db_host' => '127.0.0.1',
        'db_port' => '',
        'db_user' => '',
        'db_passwd' => '',
        'db_dir' => '/var/lib/desinventar',
    )
);
