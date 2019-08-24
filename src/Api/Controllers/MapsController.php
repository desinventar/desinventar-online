<?php

namespace Api\Controllers;

use Exception;

use Slim\Http\Request;
use Slim\Http\Response;

class MapsController extends ApiController
{
    public function routes($app)
    {
        $container = $this->container;
        $app->get('/kml/{mapId}/', function (Request $request, Response $response, $args) use ($container) {
            $container->get('logger')->debug($request !== null);
            $mapId = substr($args['mapId'], 0, 20);
            $kmlFile = $container->get('config')->paths['tmp_dir'] . '/map_' . $mapId . '.kml';
            if (empty($mapId) || !file_exists($kmlFile)) {
                throw new Exception('Map error', 404);
            }
            $sOutFilename = 'DesInventar_ThematicMap_' . $mapId . '.kml';

            return $response
                ->withHeader('Content-type', 'application/vnd.google-earth.kml+xml')
                ->withHeader('Content-Disposition', 'attachment;filename=' . basename($sOutFilename))
                ->write(file_get_contents($kmlFile) . '');
        });
    }
}
