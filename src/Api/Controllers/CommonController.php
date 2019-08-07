<?php

namespace Api\Controllers;

use DesInventar\Common\Version;

use Slim\Http\Request;
use Slim\Http\Response;

class CommonController extends ApiController
{
    public function version(Request $request, Response $response, $args)
    {
        $this->logAll($request, $response, $args);
        $version = new Version($this->container->get('config')->flags['mode']);
        return $this->container->get('jsonapi')->data($version->getVersionArray());
    }
}
