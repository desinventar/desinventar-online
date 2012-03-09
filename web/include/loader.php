<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

// 2009-09-16 (jhcaiced) Autoconfigure software directory
if (! isset($_SERVER['DESINVENTAR_WEB']))
{
	$_SERVER['DESINVENTAR_WEB'] = dirname(dirname(__FILE__));
}

// This is the version of the software
define('MAJORVERSION', '2012');
define('MINORVERSION', '065');
define('VERSION'     , MAJORVERSION . '.' . MINORVERSION);
define('JSVERSION'   , '2012-03-05.004');

$appOptions = array();
$appOptions['UseRemoteMaps'] = 1;

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
		define('SMARTYDIR', $Install_Dir . '/apps/Smarty');
		define('JPGRAPHDIR', $Install_Dir . '/apps/jpgraph');
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
		$_SERVER['DESINVENTAR_WWWDIR'] = $Install_Dir . '/www';
		$_SERVER['DESINVENTAR_DATADIR'] = $Install_Dir . '/data';
		$_SERVER['DESINVENTAR_MAPDIR'] = $Install_Dir . '/files/worldmap';
		$_SERVER['DESINVENTAR_CACHEDIR'] = $Install_Dir . '/tmp';		
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

		switch($distro)
		{
			case 'debian':
				//smarty3 package location
				define('SMARTYDIR', '/usr/share/php/smarty3');
				$_SERVER['DESINVENTAR_CACHEDIR'] = '/var/cache/smarty3/desinventar';
			break;
			default:
				define('SMARTYDIR', '/usr/share/php/Smarty');
				$_SERVER['DESINVENTAR_CACHEDIR'] = '/var/cache/Smarty/desinventar';
			break;
		}
		define('JPGRAPHDIR', '/usr/share/php/jpgraph');
		define('FONTSET' , '/usr/share/fonts/liberation/fonts.txt');
		define('TEMP', '/var/tmp/desinventar');
		if (! isset($_SERVER['DESINVENTAR_WEB']))
		{
			$_SERVER['DESINVENTAR_WEB']      = '/usr/share/desinventar/web';
		}
		$_SERVER['DESINVENTAR_WWWDIR']   = '/var/www/desinventar';
		if (! isset($_SERVER['DESINVENTAR_DATADIR']))
		{
			$_SERVER['DESINVENTAR_DATADIR']  = '/var/lib/desinventar';
		}
		$_SERVER['DESINVENTAR_MAPDIR'] = '/usr/share/desinventar/worldmap';
	}
}
else
{
	// Running a Command Line Script
	define('MODE', 'command');
	if (! isset($_SERVER['DESINVENTAR_WWWDIR']))
	{
		$_SERVER['DESINVENTAR_WWWDIR']   = '/var/www/desinventar';
	}
	if (! isset($_SERVER['DESINVENTAR_DATADIR']))
	{
		$_SERVER['DESINVENTAR_DATADIR']  = '/var/lib/desinventar';
	}
	$_SERVER['DESINVENTAR_MAPDIR'] = '/usr/share/desinventar/worldmap';
	if (! isset($_SERVER['DESINVENTAR_CACHEDIR']))
	{
		$_SERVER['DESINVENTAR_CACHEDIR'] = '/var/cache/Smarty/desinventar';
	}
	define('TEMP', '/var/tmp/desinventar');
}

// DesInventar Mode
// normal (production, cache enabled etc.)
// devel  (development, no cache, etc.)
$desinventarMode = 'normal';
if (isset($_SERVER['REDIRECT_DESINVENTAR_MODE']))
{
	$_SERVER['DESINVENTAR_MODE'] = $_SERVER['REDIRECT_DESINVENTAR_MODE'];
}
if (isset($_SERVER['DESINVENTAR_MODE']))
{
	$desinventarMode = $_SERVER['DESINVENTAR_MODE'];
}

define('BASE'    , $_SERVER['DESINVENTAR_WEB']);
define('WWWDIR'  , $_SERVER['DESINVENTAR_WWWDIR']);
define('WWWDATA' , '/desinventar-data');
define('WWWURL'  , '/');
define('DATADIR' , $_SERVER['DESINVENTAR_DATADIR']);
define('DBDIR'   , DATADIR . '/database');
define('MAPDIR'  , $_SERVER['DESINVENTAR_MAPDIR']);
define('CACHEDIR', $_SERVER['DESINVENTAR_CACHEDIR']);
define('VAR_DIR' , DATADIR);
define('TMP_DIR' , TEMP);
define('SMTY_DIR', CACHEDIR); // Smarty temp dir
require_once(BASE . '/include/fb.php');
require_once(BASE . '/include/usersession.class.php');
require_once(BASE . '/include/date.class.php');
require_once(BASE . '/include/diobject.class.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/direcord.class.php');
require_once(BASE . '/include/diuser.class.php');
require_once(BASE . '/include/query.class.php');
require_once(BASE . '/include/constants.php');
require_once(BASE . '/include/common.php');
require_once(BASE . '/include/xml2array.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/digeolevel.class.php');
require_once(BASE . '/include/digeocarto.class.php');
require_once(BASE . '/include/didisaster.class.php');

// SETTINGS
date_default_timezone_set('UTC');
$time_start = microtime_float();
$SessionId = uuid();
if (MODE != 'command')
{
	// Session Management
	session_name('DESINVENTAR_SSID');
	session_start();
	$SessionId = session_id();
}

// 2009-01-15 (jhcaiced) Start by create/recover the session 
// information, even for anonymous users
$us = new UserSession($SessionId);
$us->awake();
if (MODE != 'command')
{
	error_reporting(E_ALL && ~E_NOTICE);
	header('Content-Type: text/html; charset=UTF-8');
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
	$t->compile_dir     = SMTY_DIR;
	$t->left_delimiter  = '{-';
	$t->right_delimiter = '-}';
	$t->force_compile   = true;
	$t->caching         = 0;
	$t->cache_lifetime  = 3600;
	$t->compile_check   = true;

	// Choose Language (First from Parameter, next from UserSession table, then autodetect from browser)
	$lg = getParameter('lang');
	if ($lg == '') 
	{
		$lg = $us->LangIsoCode;
	}
	if ($lg == '')
	{
		$lg = getBrowserClientLanguage();
	}
	if ($lg == '')
	{
		$lg = 'eng';
	}
	$us->setLangIsoCode($lg);
	
	// 2009-02-21 (jhcaiced) Fix some languages from two to three character code
	if ($lg == 'es') { $lg = 'spa'; }
	if ($lg == 'en') { $lg = 'eng'; }
	if ($lg == 'fr') { $lg = 'fre'; }
	if ($lg == 'pr') { $lg = 'por'; }
	if ($lg == 'pt') { $lg = 'por'; }

	$_SESSION['lang'] = $lg;
	$t->assign('lg'  , $lg);
	$t->assign('lang', $lg);

	// 2010-05-25 (jhcaiced) Handle the versioning of js files, used to force refresh of
	// these files when doing changes.
	$t->assign('majorversion', MAJORVERSION);
	$t->assign('version'     , VERSION);
	$t->assign('jsversion'   , JSVERSION);

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
		/*
		if ($desinventarURL == '')
		{
			//$desinventarURL = $_SERVER['HTTP_HOST'];
		}
		else
		{
			if (substr($desinventarURL, strlen($desinventarURL) - 1, 1) != '/')
			{
				$desinventarURL .= '/';
			}
		}
		*/
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
	
	// General Information (common to portal/app)
	$t->assign('desinventarMode'        , $desinventarMode);
	$t->assign('desinventarURL'         , $desinventarURL);
	$t->assign('desinventarURLPortal'   , $desinventarURLPortal);
	$t->assign('desinventarVersion'     , VERSION);
	$t->assign('desinventarLang'        , $lg);
	$t->assign('desinventarUserId'      , $us->UserId);
	$t->assign('desinventarUserFullName', $us->getUserFullName());
	// OpenLayers Location
	$t->assign('desinventarOpenLayersURL', '/openlayers');
}
</script>
