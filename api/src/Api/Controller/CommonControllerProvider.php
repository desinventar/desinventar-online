<?php
namespace Api\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\ControllerProviderInterface;

class CommonControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/version', function () use ($app) {
            return $app['jsonapi']->data($app['config']->version);
        });

        $controllers->post('/login', function (Request $request) use ($app) {
            $status = $app['user_session']->login(
                $request->get('username'),
                $request->get('password'),
                \UserSession::PASSWORD_IS_CLEAR
            );
            return $app['jsonapi']->data(['status' => $status]);
        });

        $controllers->post('/logout', function (Application $app) {
            $app['user_session']->logout();
            return $app['jsonapi']->data(true);
        });
        return $controllers;
    }
}
