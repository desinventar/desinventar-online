<?php
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

// 2009-09-16 (jhcaiced) Autoconfigure software directory
if (! isset($_SERVER['DESINVENTAR_WEB']))
{
	$_SERVER['DESINVENTAR_WEB'] = dirname(dirname(__FILE__));
}
if (! isset($_SERVER['DESINVENTAR_SRC']))
{
	$_SERVER['DESINVENTAR_SRC'] = dirname(dirname(dirname(__FILE__)));
}

define('SRCDIR'  , $_SERVER['DESINVENTAR_SRC']);

$loader = require_once __DIR__ . '/../../vendor/autoload.php';
$loader->add('DesInventar', __DIR__. '/../../src');

require_once __DIR__. '/../../src/common/fb_wrapper.php';
$config = DesInventar\Common\ConfigLoader::getInstance(
	array(
		'/etc/desinventar/config.php',
		__DIR__ . '/../../config/config_local.php',
	),
	__DIR__ . '/../../config/config.php',
	'php'
);
$config->version = require_once __DIR__ . '/../../config/version.php';

$appOptions = array();
$appOptions['UseRemoteMaps'] = 1;
$appOptions['IsOnline'] = 0;

// 2009-07-22 (jhcaiced) Adapted Configuration and Startup for 
// using with PHP Command Line 
if (isset($_SERVER['HTTP_HOST']))
{
	// Online Modes (HTTP)
	if (isset($_SERVER['WINDIR']))
	{
		// Running on a Windows Server
		define('MODE', 'online');
		define('ARCH', 'WINDOWS');
		define('MAPSERV', 'mapserv.exe');
		// 2011-02-25 (jhcaiced) Use DOCUMENT_ROOT to get installation path	
		$Install_Dir = dirname(dirname($_SERVER['DOCUMENT_ROOT']));
		define('TEMP', $Install_Dir . '/tmp');
		define('FONTSET' , $Install_Dir . '/fontswin.txt');	
		// MS4W doesn't load the gd extension by default, so we do here now...
		if (!extension_loaded( 'gd' ))
		{
			//dl( 'php_gd2.'.PHP_SHLIB_SUFFIX);
		}
		if (! isset($_SERVER['DESINVENTAR_WEB']))
		{
			$_SERVER['DESINVENTAR_WEB'] = $Install_Dir . '/Apache/htdocs';
		}
		$Install_Dir = dirname($Install_Dir);
		if (empty($_SERVER['DESINVENTAR_WWWDIR'])) {
			$_SERVER['DESINVENTAR_WWWDIR'] = $Install_Dir . '/www';
		}
		if (empty($_SERVER['DESINVENTAR_DATADIR'])) {
			$_SERVER['DESINVENTAR_DATADIR'] = $Install_Dir . '/data';
		}
		if (empty($_SERVER['DESINVENTAR_CACHEDIR'])) {
			$_SERVER['DESINVENTAR_CACHEDIR'] = $Install_Dir . '/tmp';
		}
		if (!empty($_SERVER['REDIRECT_DESINVENTAR_MODE'])) {
			$_SERVER['DESINVENTAR_MODE'] = $_SERVER['REDIRECT_DESINVENTAR_MODE'];
		}
		// Disable Remote Maps by Default
		$appOptions['UseRemoteMaps'] = 0;
	}
	else
	{
		// Running on a Linux Server
		define('MODE', 'online');
		define('ARCH', 'LINUX');
		define('MAPSERV', 'mapserv');
		$distro = 'linux';
		if (file_exists('/usr/bin/lsb_release'))
		{
			$distro = strtolower(exec('/usr/bin/lsb_release -s -i'));
		}
		$_SERVER['DISTRO'] = $distro;

		if (isset($_SERVER['DESINVENTAR_LIBS_PHP'])) {
			define('DESINV_LIBS_PHP', $_SERVER['DESINVENTAR_LIBS_PHP']);
		}

		define('FONTSET' , '/usr/share/fonts/liberation/fonts.txt');
		define('TEMP', '/var/tmp/desinventar');
		if (! isset($_SERVER['DESINVENTAR_WEB']))
		{
			$_SERVER['DESINVENTAR_WEB']      = '/usr/share/desinventar/web';
		}
		if (empty($_SERVER['DESINVENTAR_WWWDIR'])) {
			$_SERVER['DESINVENTAR_WWWDIR'] = '/var/www/desinventar';
		}
		if (preg_match('/desinventar.org$/', $_SERVER['HTTP_HOST']))
		{
			$appOptions['IsOnline'] = 1;
		}
	}
	// Define the Smarty library directory (installed by composer)
	define('SMARTYDIR', SRCDIR  . '/vendor/smarty/smarty/libs');
	define('JPGRAPHDIR', $config->paths['jpgraph_dir']);
}
else
{
	// Running a Command Line Script
	define('MODE', 'command');
	if (! isset($_SERVER['DESINVENTAR_WWWDIR']))
	{
		$_SERVER['DESINVENTAR_WWWDIR'] = '/var/www/desinventar';
	}
	define('TEMP', '/var/tmp/desinventar');
}

// Override configuration values if set as Apache variables
if (!empty($_SERVER['DESINVENTAR_CACHEDIR'])) {
	$config->smarty['cachedir'] = $_SERVER['DESINVENTAR_CACHEDIR'];
}
if (!empty($_SERVER['DESINVENTAR_DATADIR'])) {
	$config->database['db_dir'] = $_SERVER['DESINVENTAR_DATADIR'];
}
if (!empty($_SERVER['DESINVENTAR_MODE']))
{
	$config->flags['mode'] = $_SERVER['DESINVENTAR_MODE'];
}
if (!empty($_SERVER['DESINVENTAR_LIBS_URL'])) {
	$config->paths['libs_url'] = $_SERVER['DESINVENTAR_LIBS_URL'];
}

define('BASE'    , $_SERVER['DESINVENTAR_WEB']);
define('WWWDIR'  , $_SERVER['DESINVENTAR_WWWDIR']);
define('WWWDATA' , '/desinventar-data');
define('WWWURL'  , '/');
define('DBDIR'   , $config->database['db_dir'] . '/database');
define('VAR_DIR' , $config->database['db_dir']);
define('TMP_DIR' , TEMP);
require_once(BASE . '/include/usersession.class.php');
require_once(BASE . '/include/date.class.php');
require_once(BASE . '/include/diobject.class.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/direcord.class.php');
require_once(BASE . '/include/diuser.class.php');
require_once(BASE . '/include/query.class.php');
require_once(BASE . '/include/constants.php');
require_once(BASE . '/include/exception.php');
require_once(BASE . '/include/common.php');
require_once(BASE . '/include/xml2array.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/digeolevel.class.php');
require_once(BASE . '/include/digeocarto.class.php');
require_once(BASE . '/include/didisaster.class.php');
require_once($config->paths['druuid_dir'] . '/lib.uuid.php');

// Set a default exception handler to avoid ugly messages in screen
if ($config->flags['mode'] != 'devel') {
	set_exception_handler('global_exception_handler');
}

// SETTINGS
date_default_timezone_set('UTC');
$time_start = microtime_float();
$SessionId = (string)UUID::mint(4);
if (MODE != 'command')
{
	$cmd = getCmd();
	// Session Management
	session_name('DESINVENTAR_SSID');
	$SessionId = '';
	if ($cmd == 'cmdUserLogin') {
		// When we are doing the user authentication, we want to make
		// sure we have the same sessionId, even when we are 
		// making a CORS call. (i.e. http makes an https call for auth)
		$SessionId = getParameter('SessionId', '');
		if (! empty($SessionId)) {
			// When setting a session_id value, it must be called before session_start()
			session_id($SessionId);
		}
	}
	session_start();
	$SessionId = session_id();
}
// 2009-01-15 (jhcaiced) Start by create/recover the session 
// information, even for anonymous users
$us = new UserSession($SessionId, $config);
if (!$us->isConnected()) {
	// Validate that main database exists (core.db)
	showErrorMsg(debug_backtrace(), null, 'Cannot initialize database connection');
	exit(0);
}

$us->awake();
if (MODE != 'command')
{
	error_reporting(E_ALL && ~E_NOTICE);
	header('Content-Type: text/html; charset=UTF-8');
	// This header allows connections from non secure clients, we keep it for compatibility
	header('Access-Control-Allow-Origin: *');
	define('DEFAULT_CHARSET', 'UTF-8');

	$confdir = dirname($_SERVER['SCRIPT_FILENAME']) . '/conf';
	$confdir = dirname(dirname(__FILE__)) . '/conf';
	$templatedir = dirname($_SERVER['SCRIPT_FILENAME']) . '/templates';

	// Smarty configuration
	require_once(SMARTYDIR . '/Smarty.class.php');
	$t = new Smarty();
	$t->debugging       = false;
	$t->config_dir      = $confdir;
	$t->template_dir    = $templatedir;
	$t->compile_dir     = $config->smarty['cachedir'];
	$t->left_delimiter  = '{-';
	$t->right_delimiter = '-}';
	$t->cache_dir = $config->smarty['cachedir'];
	$t->cache_lifetime  = 3600;
	$t->force_compile   = false;
	$t->compile_check = false;
	if ($config->flags['mode'] == 'devel') {
		//$t->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
		$t->force_compile = true;
		//$t->compile_check   = true;
	}

	// Choose Language (First from Configuration, next from Url Parameter, next from UserSession table, then autodetect from browser)
	$lg = '';
	if ($lg == '') {
		$lg = getParameter('lang');	
	}
	if (! empty($config->general['lang'])) {
		$lg = $config->general['lang'];
	}
	if ($lg == '') {
		$lg = $us->LangIsoCode;
	}
	if ($lg == '') {
		$lg = getBrowserClientLanguage();
	}
	if ($lg == '') {
		$lg = 'eng';
	}
	// 2009-02-21 (jhcaiced) Fix some languages from two to three character code
	if ($lg == 'es') { $lg = 'spa'; }
	if ($lg == 'en') { $lg = 'eng'; }
	if ($lg == 'fr') { $lg = 'fre'; }
	if ($lg == 'pr') { $lg = 'por'; }
	if ($lg == 'pt') { $lg = 'por'; }

	$config->general['lang'] = $lg;
	$us->setLangIsoCode($lg);
	$us->update();

	$_SESSION['lang'] = $lg;
	$t->assign('lg'  , $lg);
	$t->assign('lang', $lg);

	// 2010-05-25 (jhcaiced) Handle the versioning of js files, used to force refresh of
	// these files when doing changes.
	$t->assign('majorversion', $config->version['major_version']);
	$t->assign('version'     , $config->version['version']);
	$t->assign('jsversion'   , $config->version['version']);

	// Configure DESINVENTAR (web) application location	
	if (isset($_SERVER['REDIRECT_DESINVENTAR_URL']))
	{
		$_SERVER['DESINVENTAR_URL'] = $_SERVER['REDIRECT_DESINVENTAR_URL'];
	}
	if (isset($_SERVER['DESINVENTAR_URL']))
	{
		$desinventarURL = $_SERVER['DESINVENTAR_URL'];
	}
	else
	{		
		$desinventarURL = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'],'/'));
	}
	// Configure DESINVENTAR (portal) application location
	$desinventarURLPortal = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'],'/'));
	if (isset($_SERVER['REDIRECT_DESINVENTAR_PORTAL']))
	{
		$_SERVER['DESINVENTAR_PORTAL'] = $_SERVER['REDIRECT_DESINVENTAR_PORTAL'];
	}
	if (isset($_SERVER['DESINVENTAR_PORTAL']))
	{
		$desinventarURLPortal = $_SERVER['DESINVENTAR_PORTAL'];
	}
	// Remove trailing slash in URL
	if (substr($desinventarURLPortal, strlen($desinventarURLPortal) - 1, 1) == '/')
	{
		$desinventarURLPortal = substr($desinventarURLPortal, 0, strlen($desinventarURLPortal) - 1);
	}

	// Build a complete URL for the application
	$config->params = array(
		'url' => DesInventar\Common\Util::getUrl()
	);

	// General Information (common to portal/app)
	$t->assign('desinventarMode'        , $config->flags['mode']);
	$t->assign('desinventarURL'         , $desinventarURL);
	$t->assign('desinventarLibs'        , $config->paths['libs_url']);
	$t->assign('desinventar_extjs_url', $config->paths['libs_url'] . '/extjs/3.4.0');
	$t->assign('desinventarOpenLayersURL', $config->paths['libs_url'] . '/openlayers/2.11');
	$t->assign('desinventarURLPortal'   , $desinventarURLPortal);
	$t->assign('desinventarVersion'     , $config->version['version']);
	$t->assign('desinventarLang'        , $lg);
	$t->assign('desinventarUserId'      , $us->UserId);
	$t->assign('desinventarUserFullName', $us->getUserFullName());
	$t->assign('config', json_encode(array('flags' => $config->flags, 'params' => $config->params, 'version' => $config->version)));
}
