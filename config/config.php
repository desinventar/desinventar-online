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
        'fonts' => '/usr/share/fonts/liberation/fonts.txt',
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
        'arch' => 'LINUX',
        'debug' => false,
        'auto_login' => 0,
        'general_secure_login' => 0,
    ),
    'logger' => [
        'file' => 'php://stdout',
        // Monolog levels: DEBUG=100, INFO=200, NOTICE=250, WARNING=300,
        // ERROR=400, CRITICAL=500, ALERT=550, EMERGENCY=600
        'level' => 100,
    ],
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
