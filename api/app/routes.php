<?php
$routeDir = __DIR__ . '/routes';
$routeFiles = array_filter(scandir($routeDir), function ($x) {
    return pathinfo($x, PATHINFO_EXTENSION) == 'php';
});

foreach ($routeFiles as $routeFile) {
    $require = $routeDir . '/' . $routeFile;
    require_once $require;
}
