<?php

use DesInventar\Common\Version;
use DesInventar\Common\ConfigLoader;

$app->group('/common', function () use ($app, $container) {
    $app->get('/version', function () use ($app) {
        $config = new ConfigLoader(dirname(dirname(getcwd())) . '/config');
        $version = new Version($config->flags['mode']);
        return $this['jsonapi']->data($version->getVersionArray());
    });

    $app->post('/login', function (Request $request) {
        return $this['jsonapi']->data(true);
    });

    $app->post('/logout', function () {
        return $this['jsonapi']->data(true);
    });
});
