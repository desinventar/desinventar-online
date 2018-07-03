<?php
require_once __DIR__ . '/../web/include/loader.php';
require_once $config->paths['web_dir'] . '/include/diregion.class.php';
require_once $config->paths['web_dir'] . '/include/didisaster.class.php';
require_once $config->paths['web_dir'] . '/include/didisasterimport.class.php';
require_once $config->paths['web_dir'] . '/include/diimport.class.php';

use DesInventar\Common\Util;
use \DesInventar\Legacy\DIRegion;
use \DesInventar\Legacy\DIDisasterImport;
use \DesInventar\Legacy\DIGeography;
use \DesInventar\Legacy\DIEvent;
use \DesInventar\Legacy\DICause;
use \DesInventar\Legacy\DIImport;
use Fostam\GetOpts\Handler;

$getopts = new Handler();
$getopts->addOption('csvFile')->long('csv')->required()->argument('csv-file');
$getopts->addOption('params')->long('params')->required()->argument('json-file');
$getopts->addOption('databaseId')->long('db')->required()->argument('db-id');
$getopts->addOption('username')->long('username')->required()->argument('username');
$getopts->addOption('password')->long('password')->required()->argument('password');
try {
    $getopts->parse();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
$us->login($getopts->get('username'), $getopts->get('password'), false);
$us->open($getopts->get('databaseId'));
$import = new DIImport($us, $getopts->get('params'));
$import->importFromCSV($getopts->get('csvFile'), true);

$us->close();
$us->logout();
