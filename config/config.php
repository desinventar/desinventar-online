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
		'datadir' => '/var/lib/desinventar',
		'libs_url' => '//cdn-desinventar.inticol.com/js',
		'jpgraph_dir' => '/usr/share/php/jpgraph-3.0.7/src'
	),
	'maps' => array(
		'mapserver_uri' => '/cgi-bin/mapserv',
		'proj_lib' => '/usr/share/proj',
		'worldmap_dir' => '/usr/share/desinventar/worldmap'
	),
	'flags' => array(
		'mode' => 'normal',
		'auto_login' => 0
	),
	// Database connection definition (default is sqlite)
	'database' => array(
		'driver' => 'sqlite',
		'db_host' => '127.0.0.1',
		'db_port' => '',
		'db_user' => '',
		'db_passwd' => '',
		'db_dir' => '/var/lib/desinventar'
	)
);
