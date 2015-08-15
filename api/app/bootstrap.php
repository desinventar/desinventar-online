<?php
// /app/bootstrap.php
$loader = require_once __DIR__ . '/../../vendor/autoload.php';
$loader->add('DesInventar', __DIR__. '/../src');

require_once __DIR__ . '/../../web/include/loader.php';

