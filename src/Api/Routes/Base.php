<?php

namespace Api\Routes;

use Slim\App;
use Slim\Http\Request as Request;

class Base
{
    protected $app = null;
    protected $container = null;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->container = $app->getContainer();
    }

    public function parseBody(Request $request)
    {
        $json = json_encode($request->getParsedBody());
        return json_decode($json ? $json : '', true);
    }
}
