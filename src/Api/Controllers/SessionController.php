<?php

namespace Api\Controllers;

use Exception;

use Slim\Http\Request;
use Slim\Http\Response;

use Api\Controllers\ApiController;
use Api\Helpers\JsonApiResponse;

use DesInventar\Actions\UserLoginAction;
use DesInventar\Actions\UserLogoutAction;
use DesInventar\Common\Language;

class SessionController extends ApiController
{
    public function routes($app)
    {
        $container = $this->container;
        $self = $this;
        $app->get('/info', function (Request $request, Response $response, $args) use ($container) {
            $session = $container->get('session')->getSegment('');
            $info = [
                'language' => $session->get('language'),
                'isUserLoggedIn' => $session->get('isUserLoggedIn') ? true : false
            ];
            return (new JsonApiResponse($response))->data($info);
        });

        $app->post('/change-language', function (Request $request, Response $response, $args) use ($container, $self) {
            $body = $self->parseBody($request);
            $language = $body['language'];
            if (! (new Language())->isValidLanguage($language)) {
                return (new JsonApiResponse($response))->error(['message' => 'Invalid Language Code']);
            }
            $session = $container->get('session')->getSegment('');
            $session->set('language', $language);
            return (new JsonApiResponse($response))->data(['language' => $language]);
        });

        $app->post('/login', function (Request $request, Response $response, $args) use ($container, $self) {
            $body = $self->parseBody($request);
            return (new JsonApiResponse($response))->data(
                (new UserLoginAction(
                    $container->get('db')->getCoreConnection(),
                    $container->get('logger'),
                    $container->get('session')->getSegment('')
                ))->execute(
                    $body['username'],
                    $body['password']
                )
            );
        });

        $app->post('/logout', function (Request $request, Response $response, $args) use ($container) {
            return (new JsonApiResponse($response))->data(
                (new UserLogoutAction(
                    $container->get('db')->getCoreConnection(),
                    $container->get('logger'),
                    $container->get('session')
                ))->execute()
            );
        });
    }
}
