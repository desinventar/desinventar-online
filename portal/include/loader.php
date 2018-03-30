<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

require_once 'constants.php';

$cacheDir = getenv('DESINVENTAR_CACHEDIR');
if (empty($cacheDir) && isset($_SERVER['DESINVENTAR_CACHEDIR'])) {
    $cacheDir = $_SERVER['DESINVENTAR_CACHEDIR'];
}
if (empty($cacheDir) && isset($_SERVER['CACHEDIR'])) {
    $cacheDir = $_SERVER['CACHEDIR'];
}
if (empty($cacheDir)) {
    $cacheDir =  '/var/cache/smarty/desinventar/portal';
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once BASE . '/include/common.php';

// SETTINGS
date_default_timezone_set('UTC');
// Session Management
session_name('DESINVENTAR_SSID');
session_start();

error_reporting(E_ALL && ~E_NOTICE);
header('Content-Type: text/html; charset=UTF-8');

$confdir = dirname($_SERVER['SCRIPT_FILENAME']) . '/../conf';
$confdir = dirname(dirname(__FILE__)) . '/conf';
$templatedir = dirname($_SERVER['SCRIPT_FILENAME']) . '/../templates';
// Smarty configuration
require_once SMARTYDIR . '/Smarty.class.php';
$t = new Smarty();
$t->debugging       = false;
$t->config_dir      = $confdir;
$t->template_dir    = $templatedir;
$t->compile_dir     = $cacheDir;
$t->left_delimiter  = '{-';
$t->right_delimiter = '-}';
$t->force_compile   = true;
$t->caching         = 0;
$t->cache_lifetime  = 3600;
$t->compile_check   = false;

// Choose Language (First from Parameter, next from UserSession table, then autodetect from browser)
$lg = getParameter('lang');
if ($lg == '') {
    $lg = getBrowserClientLanguage();
}
if ($lg == '') {
    $lg = 'eng';
}

// 2009-02-21 (jhcaiced) Fix some languages from two to three character code
if ($lg == 'es') {
    $lg = 'spa';
}
if ($lg == 'en') {
    $lg = 'eng';
}
if ($lg == 'fr') {
    $lg = 'fre';
}
if ($lg == 'pr') {
    $lg = 'por';
}
if ($lg == 'pt') {
    $lg = 'por';
}

$_SESSION['lang'] = $lg;

// 2010-05-25 (jhcaiced) Handle the versioning of js files, used to force refresh of
// these files when doing changes.
$t->assign('majorversion', MAJORVERSION);
$t->assign('jsversion', JSVERSION);
$t->assign('lg', $lg);
$t->assign('lang', $lg);

// Configure DesInventar application location
$desinventarURL = getenv('DESINVENTAR_URL');
if (empty($desinventarURL)) {
    $desinventarURL = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/'));
}
if (isset($_SERVER['REDIRECT_DESINVENTAR_URL'])) {
    $_SERVER['DESINVENTAR_URL'] = $_SERVER['REDIRECT_DESINVENTAR_URL'];
}
if (empty($desinventarURL) && isset($_SERVER['DESINVENTAR_URL'])) {
    $desinventarURL = $_SERVER['DESINVENTAR_URL'];
}
if (substr($desinventarURL, strlen($desinventarURL) - 1, 1) == '/') {
    $desinventarURL = substr($desinventarURL, 0, strlen($desinventarURL) - 1);
}

// Configure (portal) application location
$desinventarURLPortal = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
// Remove trailing slash in URL
if (substr($desinventarURLPortal, strlen($desinventarURLPortal) - 1, 1) == '/') {
    $desinventarURLPortal = substr($desinventarURLPortal, 0, strlen($desinventarURLPortal) - 1);
}
// General Information (common to portal/app)
$t->assign('desinventarURL', $desinventarURL);
$t->assign('desinventarURLPortal', $desinventarURLPortal);
$t->assign('desinventarVersion', VERSION);
$t->assign('desinventarLang', $lg);
$t->assign('desinventarUserId', '');
$t->assign('desinventarUserFullName', '');
