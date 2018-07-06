<?php

namespace Api\Controllers;

class MapsController
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getKml($request, $response, $args)
    {
        $kmlFile = $this->container->get('config')->maps['tmp_dir'] . '/map_' . $args['mapId'] . '.kml';
        if (empty($args['mapId']) || !file_exists($kmlFile)) {
            throw new \Exception('Map error', 404);
        }
        $sOutFilename = 'DesInventar_ThematicMap_' . $args['mapId'] . '.kml';
        return $response
            ->withHeader('Content-type: text/kml')
            ->withHeader('Content-Disposition: attachment;filename=' . urlencode($sOutFilename))
            ->write(file_get_contents($kmlFile));
    }
}
