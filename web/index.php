<?php
use Slim\App;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Aura\Session\SessionFactory;

use DesInventar\Common\Language;
use DesInventar\Common\Util;
use DesInventar\Common\DatabaseConnection;

use DesInventar\Models\Role;
use DesInventar\Helpers\LoggerHelper;

use Api\Helpers\JsonApiResponse;
use Api\Helpers\SessionMiddleware;
use Api\Helpers\LoggerMiddleware;

use Api\Middleware\AuthMiddleware;
use Api\Middleware\DevelMiddleware;

use RateLimit\Middleware\RateLimitMiddleware;
use RateLimit\RateLimiterFactory;

use Api\Controllers\AdminController;
use Api\Controllers\CommonController;
use Api\Controllers\DevelController;
use Api\Controllers\MapsController;
use Api\Controllers\SessionController;

require_once('include/loader.php');
require_once('include/geography_operations.php');
require_once('include/database_operations.php');
require_once('LegacyIndex.php');

$app = new \Slim\App([
    'settings' => [
        // Only set this if you need access to route within middleware
        'determineRouteBeforeAppMiddleware' => true
    ]
]);

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

$container['db'] = function ($c) {
    return DatabaseConnection::getInstance($c['config']->database);
};

$container['logger'] = function ($c) {
    return LoggerHelper::logger($c['config']->logger);
};

$settings = [
    'template' => $t,
    'session' => $us,
    'config' => $config
];
$container['oldindex'] = function ($c) use ($settings, $container) {
    $session = $c->get('session')->getSegment('');
    $oldIndex = new \DesInventar\LegacyIndex(
        $container,
        $settings['template'],
        $settings['session'],
        (new Language())->getLanguageIsoCode($session->get('language'), Language::ISO_639_2),
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
        error_log($exception->getMessage());
        return $response->withJson([
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ], 404);
    };
};

$app->add(new SessionMiddleware($container));

$app->add(RateLimitMiddleware::createDefault(
    RateLimiterFactory::createRedisBackedRateLimiter([
        'host' => $config->redis['host'],
        'port' => intval($config->redis['port']),
    ], $config->api['rate_limit'], $config->api['rate_window'])
));

if ($config->debug['request']) {
    $app->add(new LoggerMiddleware($container));
}

$app->map(['GET', 'POST'], '/', function (Request $request, Response $response, $args) use ($container) {
    return $container->get('oldindex')->getResponse();
});

$app->group('/admin/{regionId}', function () use ($app) {
    $container = $app->getContainer();
    return (new AdminController($container, $container->get('logger')))->routes($app);
})->add(
    new AuthMiddleware(
        $container->get('db')->getCoreConnection(),
        $container->get('logger'),
        $container->get('session')->getSegment(''),
        Role::ROLE_ADMINREGION
    )
);

$app->group('/devel', function () use ($app) {
    $container = $app->getContainer();
    (new DevelController($container, $container->get('logger')))->routes($app);
})->add(
    new DevelMiddleware($container->get('logger'))
);

$app->group('/common', function () use ($app) {
    $container = $app->getContainer();
    return (new CommonController($container, $container->get('logger')))->routes($app);
});

$app->group('/maps', function () use ($app) {
    $container = $app->getContainer();
    return (new MapsController($container, $container->get('logger')))->routes($app);
});

$app->group('/session', function () use ($app) {
    $container = $app->getContainer();
    return (new SessionController($container, $container->get('logger')))->routes($app);
});

$app->run();
