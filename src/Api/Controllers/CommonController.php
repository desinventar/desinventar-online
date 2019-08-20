<?php

namespace Api\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

use Api\Controllers\ApiController;
use Api\Helpers\JsonApiResponse;

use DesInventar\Common\Version;

class CommonController extends ApiController
{
    public function routes($app)
    {
        $container = $this->container;
        $app->get('/version', function (Request $request, Response $response, $args) use ($container) {
            $version = new Version($container->get('config')->flags['mode']);
            return (new JsonApiResponse($response))->data($version->getVersionArray());
        });
    }
}
