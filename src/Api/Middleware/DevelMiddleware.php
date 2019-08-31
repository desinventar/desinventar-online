<?php

namespace Api\Middleware;

use Exception;

class DevelMiddleware
{
    protected $logger = null;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function __invoke($request, $response, $next)
    {
        $env = getenv('APP_ENV');
        if (!in_array($env, ['devel', 'development'])) {
            $this->logger->error(strtr(
                '[$class] devel middleware denied access in environment: $env',
                ['$class' => get_class($this), '$env' => $env]
            ));
            throw new Exception('Access denied');
        }
        return $next($request, $response);
    }
}
