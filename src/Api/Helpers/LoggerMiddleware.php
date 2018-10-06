<?php

namespace Api\Helpers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class LoggerMiddleware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $config = ($this->container->get('config'))->debug;
        if (!$config['request']) {
            return $next($request, $response);
        }
        $route = $request->getAttribute('route');
        if (!$route) {
            return $next($request, $response);
        }
        $logger = $this->container->get('logger');
        $logger->info(
            'REQUEST: ' . $request->getMethod() . ' ' . $route->getPattern(),
            ['args' => $route->getArguments(), 'body' => $request->getParsedBody()]
        );
        if (!$config['response']) {
            return $next($request, $response);
        }
        $response = $next($request, $response);
        $logger->info(
            'RESPONSE: ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase(),
            [$this->getResponseMessageToLog($response)]
        );
        return $response;
    }

    protected function getResponseMessageToLog(Response $response)
    {
        $jsonBody = json_decode($response->getBody(), true);
        if (!$jsonBody) {
            return ['message' => 'Not a JSON response'];
        }
        return $jsonBody;
    }
}
