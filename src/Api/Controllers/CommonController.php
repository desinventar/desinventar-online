<?php

namespace Api\Controllers;

use DesInventar\Common\Version;

class CommonController extends ApiController
{
    public function version($request, $response, $args)
    {
        $this->logAll($request, $response, $args);
        $version = new Version($this->container->get('config')->flags['mode']);
        return $this->container->get('jsonapi')->data($version->getVersionArray());
    }
}
