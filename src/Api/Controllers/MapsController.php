<?php

namespace Api\Controllers;

class MapsController extends ApiController
{
    public function getKml($request, $response, $args)
    {
        $this->logRequest($request);
        $kmlFile = $this->container->get('config')->maps['tmp_dir'] . '/map_' . $args['mapId'] . '.kml';
        if (empty($args['mapId']) || !file_exists($kmlFile)) {
            throw new \Exception('Map error', 404);
        }
        $sOutFilename = 'DesInventar_ThematicMap_' . $args['mapId'] . '.kml';
        return $response
            ->withHeader('Content-type', 'application/vnd.google-earth.kml+xml')
            ->withHeader('Content-Disposition', 'attachment;filename=' . basename($sOutFilename))
            ->write(file_get_contents($kmlFile));
    }
}
