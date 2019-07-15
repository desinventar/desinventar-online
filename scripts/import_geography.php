<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../web/include/loader.php';

use DesInventar\Common\Util;
use DesInventar\Helpers\Dbf;
use DesInventar\Legacy\GeographyOperations;

use Fostam\GetOpts\Handler;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$getopts = new Handler();
$getopts->addOption('database')->long('database')->required()->argument('database');
$getopts->addOption('username')->long('username')->required()->argument('username');
$getopts->addOption('password')->long('password')->required()->argument('password');
$getopts->addOption('dbfFile')->long('dbf')->required()->argument('dbfFile');
$getopts->addOption('level')->long('level')->required()->argument('level');
$getopts->addOption('code')->long('code')->required()->argument('code');
$getopts->addOption('name')->long('name')->required()->argument('name');
$getopts->addOption('parentCode')->long('parentCode')->argument('parentCode');

$logger = new Logger('import');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

try {
    $getopts->parse();
} catch (Exception $e) {
    $logger->error($e->getMessage());
}

$us->login($getopts->get('username'), $getopts->get('password'), false);
$us->open($getopts->get('database'));

$service = new GeographyOperations($us->q->dreg, $logger);

$service->importFromDbf(
    $getopts->get('level'),
    $getopts->get('dbfFile'),
    [
        'code' => $getopts->get('code'),
        'name' => $getopts->get('name'),
        'parentCode' => $getopts->get('parentCode') ? $getopts->get('parentCode') : null
    ]
);

$us->close();
$us->logout();
