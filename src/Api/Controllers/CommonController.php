<?php

namespace Api\Controllers;

use DesInventar\Common\Version;

class CommonController
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function version($request, $response, $args)
    {
        $version = new Version($this->container->get('config')->flags['mode']);
        return $this->container->get('jsonapi')->data($version->getVersionArray());
    }
}
