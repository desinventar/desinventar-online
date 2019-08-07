<?php

namespace Api\Controllers;

use Exception;

use Slim\Http\Request;
use Slim\Http\Response;

use Api\Controllers\ApiController;
use DesInventar\Actions\UserLoginAction;
use DesInventar\Actions\UserLogoutAction;
use DesInventar\Common\Language;

class SessionController extends ApiController
{
    public function getSessionInfo(Request $request, Response $response, $args)
    {
        $this->logAll($request, $response, $args);

        $session = $this->container->get('session')->getSegment('');
        $info = [
            'language' => $session->get('language'),
            'isUserLoggedIn' => $session->get('isUserLoggedIn') ? true : false
        ];
        return $this->container->get('jsonapi')->data($info);
    }

    public function changeLanguage(Request $request, Response $response, $args)
    {
        $this->logAll($request, $response, $args);

        $body = $this->parseBody($request);
        $language = $body['language'];
        if (! (new Language())->isValidLanguage($language)) {
            return $this->container->get('jsonapi')->error(['message' => 'Invalid Language Code']);
        }
        $session = $this->container->get('session')->getSegment('');
        $session->set('language', $language);
        return $this->container->get('jsonapi')->data(['language' => $language]);
    }

    public function login(Request $request, Response $response, $args)
    {
        $this->logAll($request, $response, $args);

        $request = $this->container->get('request');
        $body = $this->parseBody($request);
        return $this->container->get('jsonapi')->data(
            (new UserLoginAction(
                $this->container->get('db')->getCoreConnection(),
                $this->container->get('session')->getSegment(''),
                $this->container->get('logger')
            ))->execute(
                $body['username'],
                $body['password']
            )
        );
    }

    public function logout(Request $request, Response $response, $args)
    {
        $this->logAll($request, $response, $args);
        return $this->container->get('jsonapi')->data(
            (new UserLogoutAction(
                $this->container->get('db')->getCoreConnection(),
                $this->container->get('session')
            ))->execute()
        );
    }
}
