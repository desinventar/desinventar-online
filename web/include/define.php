<?php

define('MODE', $config->flags['env']);
define('ARCH', $config->flags['arch']);
define('MAPSERV', $config->maps['mapserver']);
define('TEMP', $config->paths['tmp']);
define('FONTSET', $config->paths['fonts']);
define('JPGRAPHDIR', $config->paths['jpgraph']);
define('BASE', $config->paths['web_dir']);
define('WWWDIR', $config->paths['www_dir']);
define('WWWDATA', $config->paths['www_data']);
define('DBDIR', $config->database['db_dir'] . '/database');
define('VAR_DIR', $config->database['db_dir']);
define('TMP_DIR', $config->paths['tmp']);
