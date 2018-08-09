<?php

namespace Api\Helpers;

use koenster\PHPLanguageDetection\BrowserLocalization;

class SessionMiddleware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next)
    {
        $session = $this->container->get('session')->getSegment('');
        $language = $session->get('language');
        if (empty($language)) {
            $browser = new BrowserLocalization();
            $session->set('language', $browser->detect());
        }
        if (!$next) {
            return $response;
        }
        return $next($request, $response);
    }
}
