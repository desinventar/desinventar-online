<?php

namespace Api\Helpers;

use DesInventar\Helpers\LanguageDetect;
use DesInventar\Common\Util;

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
            $language = (new LanguageDetect())->detect();
            $session->set('language', $language);
        }
        if (!$next) {
            return $response;
        }
        return $next($request, $response);
    }
}
