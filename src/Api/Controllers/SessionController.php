<?php

namespace Api\Controllers;

use DesInventar\Common\Language;

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

    public function changeLanguage($request, $response, $args)
    {
        $this->logAll($request, $response, $args);
        $body = $request->getParsedBody();
        $language = $body['language'];
        if (! (new Language())->isValidLanguage($language)) {
            return $this->container->get('jsonapi')->error(['message' => 'Invalid Language Code']);
        }
        $session = $this->container->get('session')->getSegment('');
        $session->set('language', $language);
        return $this->container->get('jsonapi')->data(['language' => $language]);
    }
}
