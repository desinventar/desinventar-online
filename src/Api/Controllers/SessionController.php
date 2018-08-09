<?php

namespace Api\Controllers;

class SessionController extends ApiController
{
    public function getSessionInfo($request, $response, $args)
    {
        $this->logAll($request, $response, $args);
        $session = $this->container->get('session')->getSegment('');
        return $this->container->get('jsonapi')->data([
            'language' => $session->get('language')
        ]);
    }
}
