<?php
/*
  DesInventar - http://www.desinventar.org
  (c) Corporacion OSSO
*/

use Slim\App;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Aura\Session\SessionFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;

use DesInventar\Common\Util;

use Api\Helpers\JsonApiResponse;
use Api\Helpers\SessionMiddleware;

use Api\Controllers\CommonController;
use Api\Controllers\MapsController;
use Api\Controllers\SessionController;

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

$container['oldindex'] = function ($c) use ($settings) {
    $session = $c->get('session')->getSegment('');
    $oldIndex = new \DesInventar\LegacyIndex(
        $settings['template'],
        $settings['session'],
        $c->get('util')->getLanguageIsoCode($session->get('language'), Util::ISO_639_2),
        $settings['config']
    );
    return $oldIndex;
};

$container['jsonapi'] = function ($c) {
    return new JsonApiResponse($c->response);
};

$container['notFoundHandler'] = function ($c) {
    return function (Request $request, Response $response) use ($c) {
        throw new Exception('Page not found');
    };
};

$container['errorHandler'] = function ($container) {
    return function (Request $request, Response $response, \Exception $exception) use ($container) {
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

$container['SessionController'] = function ($c) {
    return new SessionController($c);
};

$app->add(new SessionMiddleware($container));

$app->map(['GET', 'POST'], '/', function (Request $request, Response $response, $args) use ($container) {
    return $container->get('oldindex')->getResponse('');
});

$app->group('/common', function () use ($app) {
    $app->get('/version', 'CommonController:version');
});

$app->group('/maps', function () use ($app) {
    $app->get('/kml/{mapId}/', 'MapsController:getKml');
});

$app->group('/session', function () use ($app) {
    $app->get('/info', 'SessionController:getSessionInfo');
    $app->post('/change-language', 'SessionController:changeLanguage');
});

$app->run();
