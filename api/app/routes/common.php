<?php

use DesInventar\Common\Version;

$app->group('/common', function () use ($app, $container) {
    $app->get('/version', function () use ($app) {
        $version = new Version($container['config']->flags['mode']);
        return $this['jsonapi']->data($version->getVersionArray());
    });

    $app->post('/login', function (Request $request) {
        return $this['jsonapi']->data(true);
    });

    $app->post('/logout', function () {
        return $this['jsonapi']->data(true);
    });
});
