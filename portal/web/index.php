<?php
/**
 * DesInventar - http://www.desinventar.org
 * (c) Corporación OSSO
 */

use Slim\App;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Aura\Session\SessionFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;

use DesInventar\Common\Util;
use DesInventar\Common\ConfigLoader;
use DesInventar\Common\Version;
use koenster\PHPLanguageDetection\BrowserLocalization;

require_once('../include/loader.php');

$app = new App;

$container = $app->getContainer();
$container['session'] = function ($container) {
    $sessionFactory = new SessionFactory();
    return $sessionFactory->newInstance($_COOKIE);
};

$container['util'] = function ($container) {
    return new Util();
};

$container['config'] = function ($c) use ($config) {
    return new ConfigLoader(__DIR__ . '/../../config');
};

$container['logger'] = function () {
    $logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler('php://stderr'));
    $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::WARNING));
    return $logger;
};

$app->get('/', function (Request $request, Response $response, array $args) use ($container, $t) {
    $portaltype = $request->getParam('portaltype', 'desinventar');
    $t->assign('desinventarPortalType', $portaltype);

    $version = new Version($container->get('config')->flags['mode']);
    $t->assign('jsversion', $version->getVersion());

    $session = $container->get('session')->getSegment('');
    $language = $session->get('language');
    if (empty($language)) {
        $browser = new BrowserLocalization();
        $language = $session->get('language', $browser->detect());
        $session->set('language', $language);
    }
    $langCode = $container->get('util')->getLanguageIsoCode($language, Util::ISO_639_2);
    $t->assign('lang', $langCode);

    $response->getBody()->write($t->fetch('index.tpl'));
    return $response;
});

$app->post('/change-language', function (Request $request, Response $response) use ($container) {
    $body = $request->getParsedBody();
    $langCode = $body['language'];
    $session = $container->get('session')->getSegment('');
    $language = $container->get('util')->getLanguageIsoCode($langCode, Util::ISO_639_1);
    $session->set('language', $language);
    return $response->withJson([]);
});

$app->run();
