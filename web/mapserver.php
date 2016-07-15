<?php
require_once('include/loader.php');
$mapserver = new \DesInventar\Common\MapServer($config);
$queryString = $mapserver->getQueryString($_GET);
$url = $mapserver->getMapServerUrl($queryString);

header('Content-type: ' . $mapserver->getMapImageFormat());
echo file_get_contents($url);
