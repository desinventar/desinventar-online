<?php
require_once __DIR__ . '/../web/include/loader.php';

use DesInventar\Common\Util;
use DesInventar\Legacy\DIImport;

use Fostam\GetOpts\Handler;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$getopts = new Handler();
$getopts->addOption('csvFile')->long('csv')->required()->argument('csv-file');
$getopts->addOption('paramsFile')->long('params')->required()->argument('json-file');
$getopts->addOption('databaseId')->long('db')->required()->argument('db-id');
$getopts->addOption('username')->long('username')->required()->argument('username');
$getopts->addOption('password')->long('password')->required()->argument('password');
$getopts->addOption('dry')->long('dry');
$getopts->addOption('lineCount')->long('count')->argument('count');
$getopts->addOption('offset')->long('offset')->argument('offset');
$getopts->addOption('loglevel')->long('loglevel')->required()->argument('loglevel');

try {
    $getopts->parse();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
$params = DIImport::loadParamsFromFile($getopts->get('paramsFile'));
if ($getopts->get('lineCount')) {
    $params['lineCount'] = $getopts->get('lineCount');
}
if ($getopts->get('offset')) {
    $params['offset'] = $getopts->get('offset');
}

$logLevel = intval($getopts->get('loglevel') ? $getopts->get('loglevel') : Logger::DEBUG);

$us->login($getopts->get('username'), $getopts->get('password'), false);
$us->open($getopts->get('databaseId'));

$doImport = $getopts->get('dry') ? false : true;

$logger = new Logger('import');
$logger->pushHandler(new StreamHandler('php://stdout', $logLevel));

$import = new DIImport($us, $logger, $params);
$import->importFromCSV($getopts->get('csvFile'), $doImport);

$us->close();
$us->logout();
