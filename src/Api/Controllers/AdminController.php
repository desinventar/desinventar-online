<?php

namespace Api\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

use Api\Controllers\ApiController;
use Api\Helpers\JsonApiResponse;

class AdminController extends ApiController
{
    public function routes($app)
    {
        $container = $this->container;
        $app->get('/', function (Request $request, Response $response, $args) use ($container) {
            return (new JsonApiResponse($response))->data([
                'args' => $args,
                'userId' => $container->get('session')->getSegment('')->get('userId'),
                'attr' => $request->getAttributes()
            ]);
        });
    }
}
