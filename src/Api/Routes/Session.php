<?php

namespace Api\Routes;

use Exception;

use Slim\Http\Request as Request;

use Api\Routes\Base;
use DesInventar\Actions\UserLoginAction;
use DesInventar\Actions\UserLogoutAction;
use DesInventar\Common\Language;

class Session extends Base
{
    public function getSessionInfo()
    {
        $session = $this->container->get('session')->getSegment('');
        $info = [
            'language' => $session->get('language'),
            'isUserLoggedIn' => $session->get('isUserLoggedIn') ? true : false
        ];
        return $this->container->get('jsonapi')->data($info);
    }

    public function changeLanguage($request)
    {
        $body = $this->parseBody($request);
        $language = $body['language'];
        if (! (new Language())->isValidLanguage($language)) {
            return $this->container->get('jsonapi')->error(['message' => 'Invalid Language Code']);
        }
        $session = $this->container->get('session')->getSegment('');
        $session->set('language', $language);
        return $this->container->get('jsonapi')->data(['language' => $language]);
    }

    public function login(Request $request)
    {
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

    public function logout()
    {
        return $this->container->get('jsonapi')->data(
            (new UserLogoutAction(
                $this->container->get('db')->getCoreConnection(),
                $this->container->get('session')
            ))->execute()
        );
    }
}
