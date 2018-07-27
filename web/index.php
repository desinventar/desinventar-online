<?php
/*
  DesInventar - http://www.desinventar.org
  (c) Corporacion OSSO
*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Aura\Session\SessionFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;

use DesInventar\Service\JsonApi;
use DesInventar\Common\Util;
use DesInventar\Common\Version;
use DesInventar\Common\ConfigLoader;

use Api\Controllers\CommonController;
use Api\Controllers\MapsController;

require_once('include/loader.php');
require_once('include/diregion.class.php');
require_once('include/diregiondb.class.php');
require_once('include/diregionrecord.class.php');
require_once('include/geography_operations.php');
require_once('include/database_operations.php');

require_once('LegacyIndex.php');

$settings = [
    'template' => $t,
    'session' => $us,
    'language' => $lg,
    'config' => $config
];

$app = new \Slim\App;
$container = $app->getContainer();
$container['session'] = function ($container) {
    $sessionFactory = new SessionFactory();
    return $sessionFactory->newInstance($_COOKIE);
};

$container['util'] = function ($container) {
    return new Util();
};

$container['config'] = function ($c) use ($config) {
    return $config;
};

$container['logger'] = function ($c) {
    $loggerConfig = $c['config']->logger;
    $logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler($loggerConfig['file'], $loggerConfig['level']));
    $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::WARNING));
    return $logger;
};

$container['oldindex'] = function () use ($settings) {
    $oldIndex = new \DesInventar\LegacyIndex(
        $settings['template'],
        $settings['session'],
        $settings['language'],
        $settings['config']
    );
    return $oldIndex;
};

$container['jsonapi'] = function ($c) {
    return new JsonApi($c->response);
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        throw new Exception('Page not found');
    };
};

$container['errorHandler'] = function ($container) {
    return function ($request, $response, \Exception $exception) use ($container) {
        return $response->withJson([
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ], 404);
    };
};

$container['CommonController'] = function ($c) {
    return new CommonController($c);
};

$container['MapsController'] = function ($c) {
    return new MapsController($c);
};

$app->map(['GET', 'POST'], '/', function ($request, $response, $args) use ($container) {
    $oldIndex = $container['oldindex'];
    return $oldIndex->getResponse('');
});

$app->group('/common', function () use ($app) {
    $app->get('/version', 'CommonController:version');
});

$app->group('/maps', function () use ($app) {
    $app->get('/kml/{mapId}/', 'MapsController:getKml');
});

$app->run();
