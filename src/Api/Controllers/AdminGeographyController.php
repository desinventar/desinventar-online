<?php

namespace Api\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

use Api\Controllers\ApiController;

class AdminGeographyController extends ApiController
{
    public function info(Request $request, Response $response, $args)
    {
        $this->logAll($request, $response, $args);
        return $this->container->get('jsonapi')->data([
            'args' => $args,
            'userId' => $this->container->get('session')->getSegment('')->get('userId'),
            'attr' => $request->getAttributes()
        ]);
    }
}
