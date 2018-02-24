<?php

define('MODE', $config->flags['env']);
define('ARCH', $config->flags['arch']);
define('MAPSERV', $config->maps['mapserver']);
define('FONTSET', $config->paths['fonts']);
define('JPGRAPHDIR', $config->paths['src_dir'] . '/vendor/jpgraph/src');
define('BASE', $config->paths['web_dir']);
define('DBDIR', $config->database['db_dir'] . '/database');
define('VAR_DIR', $config->database['db_dir']);
