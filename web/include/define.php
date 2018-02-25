<?php

define('MODE', $config->flags['env']);
define('ARCH', $config->flags['arch']);
define('BASE', $config->paths['web_dir']);
define('DBDIR', $config->database['db_dir'] . '/database');
define('VAR_DIR', $config->database['db_dir']);
