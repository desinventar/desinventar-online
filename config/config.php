<?php
return array(
	// Default paths
	'smarty' => array(
		'cachedir' => '/var/cache/smarty/desinventar',
	),
	'paths' => array(
		'datadir' => '/var/lib/desinventar',
		'libs_url' => '//cdn-desinventar.inticol.com/js',
	),
	'flags' => array(
		'mode' => 'normal'
	),
	// Default database connection for core.db
	'db_core' => array(
		'driver' => 'sqlite',
		'db_host' => '127.0.0.1',
		'db_port' => '',
		'db_user' => '',
		'db_passwd' => '',
	),
	// Default connection parameters for databases
	'db_database' => array(
		'driver' => 'sqlite',
		'db_host' => '127.0.0.1',
		'db_port' => '',
		'db_user' => '',
		'db_passwd' => '',
	)
);
