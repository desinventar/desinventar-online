<?php

namespace Api\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

use Api\Controllers\ApiController;
use Api\Helpers\JsonApiResponse;

class DevelController extends ApiController
{
    public function routes($app)
    {
        $app->post('/sample', [$this, 'sample']);
    }

    public function sample(Request $request, Response $response, $args)
    {
        return (new JsonApiResponse($response))->data([
            'args' => $args,
            'body' => $this->parseBody($request)
        ]);
    }
}
