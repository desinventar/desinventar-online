<?php
require_once __DIR__.'/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\ErrorHandler;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

use Api\Service\JsonApi;

$app = new Silex\Application();

// Initialize some variables from the legacy code
$app['user_session'] = $us;
$app['config'] = $config;

if ($app['config']->flags['debug']) {
    $app['debug'] = true;
}

// Configure logger, and then replace it with a logger to the httpd server
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
    'monolog.level' => Logger::WARNING,
));
$app['monolog'] = $app->share($app->extend('monolog', function ($monolog, $app) {
    $monolog->setHandlers([new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::WARNING)]);
    return $monolog;
}));

$app['jsonapi'] = $app->share(function () {
    return new JsonApi();
});

// Convert errors in to Exceptions
ErrorHandler::register();

$app->error(function (\Exception $e, $code) use ($app) {
    $response = [
        'code' => $code,
        'message' => 'Something went wrong with this request',
    ];
    if ($app['debug']) {
        $response['message'] = $e->getMessage();
    }
    return $app['jsonapi']->error($response);
});

$app->before(function (Request $request) {
    if ((0 === strpos($request->headers->get('Content-Type'), 'application/json')) ||
        (0 === strpos($request->headers->get('Content-Type'), 'application/vnd.api+json'))) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->get('/', function () {
    return new Response('DesInventar Api Server (c) Corporación OSSO - 2017');
});

$app->mount('/common', new Api\Controller\CommonControllerProvider());

return $app;
