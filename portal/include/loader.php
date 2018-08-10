<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
use DesInventar\Common\ConfigLoader;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once 'common.php';

$config = new ConfigLoader(__DIR__ . '/../../config');

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

$t = new Smarty();
$t->debugging       = false;
$t->config_dir      = $confdir;
$t->template_dir    = $templatedir;
$t->compile_dir     = $config->portal['cache_dir'];
$t->left_delimiter  = '{-';
$t->right_delimiter = '-}';
$t->force_compile   = true;
$t->caching         = 0;
$t->cache_lifetime  = 3600;
$t->compile_check   = false;

// Configure DesInventar application location
$desinventarURL = $config->portal['app_url'];
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
$t->assign('desinventarUserId', '');
$t->assign('desinventarUserFullName', '');
