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

require_once('include/loader.php');
require_once('include/diregion.class.php');
require_once('include/diregiondb.class.php');
require_once('include/diregionrecord.class.php');
require_once('include/geography_operations.php');
require_once('include/database_operations.php');
require_once('include/query_operations.php');

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

$container['logger'] = function () {
    $logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler('php://stderr'));
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

$container['config'] = function ($c) use ($config) {
    return $config;
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

$app->map(['GET', 'POST'], '/', function ($request, $response, $args) use ($container) {
    $oldIndex = $container['oldindex'];
    return $oldIndex->getResponse('');
});

$app->group('/common', function () use ($app, $container) {
    $app->get('/version', function () use ($container) {
        $version = new Version($container->get('config')->flags['mode']);
        return $container->get('jsonapi')->data($version->getVersionArray());
    });
});

$app->run();
