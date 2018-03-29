<?php

define('MAJORVERSION', '10');
define('MINORVERSION', '01.008');
define('CACHEVERSION', '002');
define('RELEASEDATE', '2018-02-27');
define('VERSION', MAJORVERSION . '.' . MINORVERSION);
define('JSVERSION', VERSION . '-' . CACHEVERSION);

define('BASE', dirname(dirname(__FILE__)));
define('SMARTYDIR', BASE . '/vendor/smarty/smarty/libs');
define('FONTSET', '/usr/share/fonts/liberation/fonts.txt');

define('DEFAULT_CHARSET', 'UTF-8');
