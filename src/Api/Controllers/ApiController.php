<?php

namespace Api\Controllers;

class ApiController
{
    protected $container;
    protected $logger;

    public function __construct($container)
    {
        $this->container = $container;
        $this->logger = $container['logger'];
    }

    protected function logRequest($request)
    {
        $this->logger->debug($request->getMethod());
    }

    protected function logResponse($response)
    {
        $this->logger->debug($response->getStatusCode());
    }

    protected function logArgs($args)
    {
        $this->logger->debug(print_r($args, true));
    }
}
