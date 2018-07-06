<?php

namespace Api\Controllers;

use DesInventar\Common\Version;

class CommonController extends ApiController
{
    public function version($request, $response, $args)
    {
        $this->logRequest($request);
        $this->logResponse($response);
        $this->logArgs($args);
        $version = new Version($this->container->get('config')->flags['mode']);
        return $this->container->get('jsonapi')->data($version->getVersionArray());
    }
}
