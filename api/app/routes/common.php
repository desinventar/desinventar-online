<?php

$app->group('/common', function () use ($app, $container) {
    $app->get('/version', function () use ($app) {
        return $this['jsonapi']->data($this->config->version);
    });

    $app->post('/login', function (Request $request) {
        return $this['jsonapi']->data(true);
    });

    $app->post('/logout', function () {
        return $this['jsonapi']->data(true);
    });
});
