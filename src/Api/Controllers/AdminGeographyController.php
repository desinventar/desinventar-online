<?php

namespace Api\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

use Api\Controllers\ApiController;
use Api\Helpers\JsonApiResponse;

use DesInventar\Actions\AdminGeographyGetByCodeAction;
use DesInventar\Actions\AdminGeographyRenameByCodeAction;
use DesInventar\Actions\AdminGeographyUpdateFQNameByCodeAction;

class AdminGeographyController extends ApiController
{
    public function routes($app)
    {
        $app->get('/geography/{code}', [$this, 'getByCode']);
        $app->put('/geography/{code}/update-fqname', [$this, 'updateFQNameByCode']);
        $app->get('/geography/rename/{code}/{newCode}', [$this, 'renameByCode']);
    }

    public function getByCode(Request $request, Response $response, $args)
    {
        return (new JsonApiResponse($response))->data([
            'args' => $args,
            'attr' => $request->getAttributes(),
            'response' => (
                new AdminGeographyGetByCodeAction(
                    $this->container->get('db')->getDbConnection($args['regionId']),
                    $this->container->get('logger')
                )
            )->execute($args['code'])
        ]);
    }

    public function updateFQNameByCode(Request $request, Response $response, $args)
    {
        return (new JsonApiResponse($response))->data([
            'args' => $args,
            'attr' => $request->getAttributes(),
            'response' => (
                new AdminGeographyUpdateFQNameByCodeAction(
                    $this->container->get('db')->getDbConnection($args['regionId']),
                    $this->container->get('logger')
                )
            )->execute($args['code'])
        ]);
    }

    public function renameByCode(Request $request, Response $response, $args)
    {
        return (new JsonApiResponse($response))->data([
            'args' => $args,
            'attr' => $request->getAttributes(),
            'response' => (
                new AdminGeographyRenameByCodeAction(
                    $this->container->get('db')->getDbConnection($args['regionId']),
                    $this->container->get('logger')
                )
            )->execute($args['code'], $args['newCode'])
        ]);
    }
}
