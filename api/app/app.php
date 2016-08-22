<?php
// /app/app.php
require_once __DIR__.'/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

// Initialize some variables from the legacy code
$app['user_session'] = $us;
$app['config'] = $config;

$app->get('/', function () {
    return new Response('DesInventar Api Server (c) Corporación OSSO - 2016');
});

$app->get('/version', function () use ($app) {
    return $app->json(array('version' => time()));
});

$app->mount('/common', new DesInventar\Api\CommonControllerProvider());

return $app;
