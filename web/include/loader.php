<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/
// 2009-09-16 (jhcaiced) Autoconfigure software directory
if (! isset($_SERVER["DI8_WEB"])) {
	$_SERVER["DI8_WEB"] = dirname(dirname(__FILE__));
}

// This is the version of the software
define('VERSION', '8.2.0.80');
define('JSVERSION', '2010-06-04.03');

// 2009-07-22 (jhcaiced) Adapted Configuration and Startup for 
// using with PHP Command Line 
if (isset($_SERVER["HTTP_HOST"])) {
	// Online Modes (HTTP)
	if (isset($_SERVER["WINDIR"])) {
		// Running on a Windows Server
		define('MODE', "online");
		define('ARCH', 'WINDOWS');
		define('MAPSERV', "mapserv.exe");
		// 2009-05-01 (jhcaiced) Read Registry to obtain MS4W 
		//                       installation path	
		$shell = new COM("WScript.Shell") or die("Requires Windows Scripting Host");
		$Install_Dir = $shell->RegRead("HKEY_LOCAL_MACHINE\\Software\\OSSO\\DesInventar8\Install_Dir");		
		define("SMARTYDIR", $Install_Dir . "/ms4w/apps/Smarty/libs");
		define("JPGRAPHDIR", $Install_Dir . "/ms4w/apps/jpgraph/src");
		define("TEMP", $Install_Dir . "/tmp");
		// MS4W doesn't load the gd extension by default, so we do here now...
		if (!extension_loaded( 'gd' )) {
			//dl( 'php_gd2.'.PHP_SHLIB_SUFFIX);
		}
		if (! isset($_SERVER["DI8_WEB"])) {
			$_SERVER['DI8_WEB'] = $Install_Dir . '/ms4w/Apache/htdocs';
		}
		$_SERVER['DI8_WWWDIR'] = $Install_Dir . '/www';
		$_SERVER['DI8_DATADIR'] = $Install_Dir . '/data';
		$_SERVER['DI8_CACHEDIR'] = $Install_Dir . '/tmp';
		define("FONTSET" , $Install_Dir . '/data/main/fontswin.txt');	
	} else {
		// Running on a Linux Server
		define('MODE', "online");
		define('ARCH', "LINUX");
		define('MAPSERV', "mapserv");
		define("SMARTYDIR", "/usr/share/php/Smarty");
		define("TEMP", "/tmp");
		define("JPGRAPHDIR", "/usr/share/php/jpgraph");
		define("FONTSET" , "/usr/share/fonts/liberation/fonts.txt");
		if (! isset($_SERVER["DI8_WEB"])) {
			$_SERVER["DI8_WEB"]      = "/usr/share/desinventar-8.2/web";
		}
		$_SERVER["DI8_WWWDIR"]   = "/var/www/desinventar-8.2";
		$_SERVER["DI8_DATADIR"]  = "/var/lib/desinventar-8.2";
		$_SERVER["DI8_CACHEDIR"] = "/var/cache/Smarty/di8";
	}
} else {
	// Running a Command Line Script
	define('MODE', "command");
	$_SERVER["DI8_WWWDIR"]   = "/var/www/desinventar-8.2";
	$_SERVER["DI8_DATADIR"]  = "/var/lib/desinventar-8.2";
	$_SERVER["DI8_CACHEDIR"] = "/var/cache/Smarty/di8";
	define('TEMP', '/tmp');
}
define("BASE"    , $_SERVER["DI8_WEB"]);
define("WWWDIR"  , $_SERVER["DI8_WWWDIR"]);
define("WWWDATA" , "/desinventar-8.2-data");
define("WWWURL"  , "/");
define("DATADIR" , $_SERVER["DI8_DATADIR"]);
define("CACHEDIR", $_SERVER["DI8_CACHEDIR"]);
define("VAR_DIR" , DATADIR);
define("TMP_DIR" , TEMP);
define("SMTY_DIR", CACHEDIR); // Smarty temp dir
define("TMPM_DIR", CACHEDIR); // Mapserver temp dir
$lg          = "spa";
require_once(BASE . "/include/fb.php");
require_once(BASE . "/include/usersession.class.php");
require_once(BASE . "/include/diobject.class.php");
require_once(BASE . '/include/diuser.class.php');
require_once(BASE . "/include/query.class.php");
require_once(BASE . "/include/constants.php");
require_once(BASE . "/include/common.php");
require_once(BASE . "/include/xml2array.php");

/* SETTINGS */
$time_start = microtime_float();
$SessionId = uuid();
if (MODE != "command") {
	// Session Management
	session_name("DI8SESSID");
	session_start();
	$SessionId = session_id();
}

// 2009-01-15 (jhcaiced) Start by create/recover the session 
// information, even for anonymous users
$us = new UserSession($SessionId);
$us->awake();

if (MODE != "command") {
	error_reporting(E_ALL && ~E_NOTICE);
	header('Content-Type: text/html; charset=UTF-8');
	define("DEFAULT_CHARSET", 'UTF-8');

	/* Smarty configuration */
	require_once(SMARTYDIR . '/Smarty.class.php');
	/* SMARTY template */
	$t = new Smarty();
	$t->debugging = false;
	$t->config_dir = BASE . '/conf';
	$t->template_dir = BASE . '/templates';
	$t->compile_dir = SMTY_DIR;
	$t->left_delimiter = '{-';
	$t->right_delimiter = '-}';
	// Smarty caching settings...
	$t->force_compile = true;
	$t->caching = 0;
	$t->cache_lifetime = 3600;
	$t->compile_check = true;

	// Choose Language
	$lg = getParameter('lang');
	if ($lg == '') {
		if (isset($_SESSION['lang'])) {
			$lg = $_SESSION['lang'];
		} else {
			$lg = getBrowserClientLanguage();
		}
	}
	// 2009-02-21 (jhcaiced) Fix some languages from two to three character code
	if ($lg == 'es') { $lg = 'spa'; }
	if ($lg == 'en') { $lg = 'eng'; }
	if ($lg == 'fr') { $lg = 'fre'; }
	if ($lg == 'pr') { $lg = 'por'; }
	if ($lg == 'pt') { $lg = 'por'; }

	$_SESSION['lang'] = $lg;
	$t->assign ("lg", $lg);

	// 2010-05-25 (jhcaiced) Handle the versioning of js files, used to force refresh of
	// these files when doing changes.
	$jsversion = JSVERSION;
	/*
	$hostname  = $_SERVER["SERVER_NAME"];
	if ( ($hostname == 'devel.desinventar.org') ||
	     ($hostname == '192.168.0.13') ) {
	     $jsversion = time();
	}
	*/	
	$t->assign('jsversion', $jsversion);
}
</script>
