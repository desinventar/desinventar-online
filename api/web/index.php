<?php

require_once __DIR__.'/../../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;

use DesInventar\Common\ConfigLoader;
use DesInventar\Common\Version;

use Api\Service\JsonApi;

$slimSettings = [
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
];

$app = new \Slim\App(['settings' => $slimSettings]);
$container = $app->getContainer();

// Configure logger, and then replace it with a logger to the httpd server
$container['logger'] = function () {
    $logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler('php://stderr'));
    $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::WARNING));
    return $logger;
};

$container['config'] = function ($c) {
    return new ConfigLoader('');
};

$container['jsonapi'] = function ($c) {
    return new JsonApi($c->response);
};

$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) {
        throw new \Exception('Page not found');
    };
};

$container['errorHandler'] = function ($container) {
    return function ($request, $response, \Exception $exception) use ($container) {
        return $container['jsonapi']->error([
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ], 404);
    };
};

$app->get('/', function (Request $request, Response $response) use ($container) {
    $answer = [
        'text' => 'DesInventar Api Server',
        'copyright' => '(c) Corporación OSSO - 1998 - 2018'
    ];
    return $container->get('jsonapi')->data($answer);
});

$app->group('/common', function () use ($app, $container) {
    $app->get('/version', function () use ($container) {
        $version = new Version($container->get('config')->flags['mode']);
        return $container->get('jsonapi')->data($version->getVersionArray());
    });

    $app->post('/login', function (Request $request) use ($container) {
        return $container->get('jsonapi')->data(true);
    });

    $app->post('/logout', function () use ($container) {
        return $container->get('jsonapi')->data(true);
    });
});

$app->run();
