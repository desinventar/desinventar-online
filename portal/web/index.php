<?php
use Slim\App;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Aura\Session\SessionFactory;

use DesInventar\Common\Language;
use DesInventar\Common\Util;
use DesInventar\Common\ConfigLoader;
use DesInventar\Common\Version;

use DesInventar\Helpers\LoggerHelper;
use DesInventar\Helpers\LanguageDetect;

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

$container['config'] = function ($c) {
    return new ConfigLoader(__DIR__ . '/../../config');
};

$container['logger'] = function () {
    return LoggerHelper::logger([]);
};

$app->get('/', function (Request $request, Response $response, array $args) use ($container, $t) {
    $portaltype = getenv('DESINVENTAR_PORTAL_TYPE')
        ? getenv('DESINVENTAR_PORTAL_TYPE')
        : $request->getParam('portaltype', 'desinventar');
    $t->assign('desinventarPortalType', $portaltype);

    $version = new Version($container->get('config')->flags['mode']);
    $t->assign('jsversion', $version->getVersion());

    $session = $container->get('session')->getSegment('');
    $language = $session->get('language');
    if (empty($language)) {
        $language = (new LanguageDetect())->detect();
        $session->set('language', $language);
    }
    $langCode = (new Language())->getLanguageIsoCode($language, Language::ISO_639_2);
    $t->assign('lang', $langCode);

    $response->getBody()->write($t->fetch('index.tpl'));
    return $response;
});

$app->post('/change-language', function (Request $request, Response $response) use ($container) {
    $body = $request->getParsedBody();
    $langCode = $body['language'];
    $session = $container->get('session')->getSegment('');
    $language = (new Language())->getLanguageIsoCode($langCode, Language::ISO_639_1);
    $session->set('language', $language);
    return $response->withJson([]);
});

$app->run();
