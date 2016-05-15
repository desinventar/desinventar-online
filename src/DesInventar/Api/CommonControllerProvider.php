<?php
namespace DesInventar\Api;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\ControllerProviderInterface;

class CommonControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->post('/login', function (Request $request) use ($app) {
            $status = $app['user_session']->login(
                $request->get('username'),
                $request->get('password'),
                \UserSession::PASSWORD_IS_CLEAR
            );
            return $app->json(array('status' => $status));
        });
        $controllers->post('/logout', function (Application $app) {
            $app['user_session']->logout();
            return $app->json(true);
        });
        return $controllers;
    }
}
