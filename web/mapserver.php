<?php
use DesInventar\Common\MapServer;

require_once('include/loader.php');

$mapserver = new MapServer($config);
$queryString = $mapserver->getQueryString($_GET);
$url = $mapserver->getMapServerUrl($queryString);

header('Content-type: ' . $mapserver->getMapImageFormat());
echo file_get_contents($url);
