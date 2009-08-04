<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

/* Main loader..*/

//ob_start( 'ob_gzhandler' );


/* SETTINGS */
// "C:/desinventar8/ms4w/Apache/htdocs/";
// "/var/www/html/desinventar/test/";

// 2009-07-22 (jhcaiced) Adapted Configuration and Startup for 
// using with PHP Command Line 
if (isset($_SERVER["HTTP_HOST"])) {
	// Online Modes (HTTP)
	if (isset($_SERVER["WINDIR"])) {
		// Running on a Windows Server
		define('MODE', "offline");
		define('ARCH', 'WINDOWS');
		define('MAPSERV', "mapserv.exe");
		// 2009-05-01 (jhcaiced) Read Registry to obtain MS4W 
		//                       installation path	
		$shell = new COM("WScript.Shell") or die("Requires Windows Scripting Host");
		$ms4wpath=$shell->RegRead("HKEY_LOCAL_MACHINE\\SOFTWARE\\MS4W\\Install_Dir");
		define("SMARTYDIR", $ms4wpath . "\apps\smarty");
		define("JPGRAPHDIR", $ms4wpath . "\apps\jpgraph");
		define("TEMP", $ms4wpath . "/tmp");
		// MS4W doesn't load the gd extension by default, so we do here now...
		if (!extension_loaded( 'gd' )) {
			//dl( 'php_gd2.'.PHP_SHLIB_SUFFIX);
		}
	} else {
		// Running on a Linux Server
		define('MODE', "online");
		define('ARCH', "LINUX");
		define('MAPSERV', "mapserv");
		define("SMARTYDIR", "/usr/share/Smarty");
		define("TEMP", "/tmp");
		define("JPGRAPHDIR", "/usr/share/php/jpgraph");
	}
} else {
	// Running a Command Line Script
	define('MODE', "command");
}

// 2009-07-04 (jhcaiced) Added FirePHP debug system
// This lines try to detect if FirePHP Core is installed,
// if not, create a dummy class/function to avoid errors.
if (MODE != 'command') {
	if (file_exists('/usr/share/pear/FirePHPCore/fb.php')) {
		require_once('FirePHPCore/fb.php');
	} else {
		function fb() {
			// dummy fb() function, doesn't do anything...
		}
	}
} else {
		function fb($Msg) {
			print "fb: " . $Msg . "\n";
		}
}

function showErrorMsg($sMsg) {
	fb($sMsg);
}

/* Configure BASE Directory from HTTPD Config - Linux + WIndows */
/* Apache MS4W creates enviroment variables as REDIRECT_XXXXX */
if (isset($_SERVER["REDIRECT_DI8_WEB"])) {
	$_SERVER["DI8_WEB"] = $_SERVER["REDIRECT_DI8_WEB"];
}

/* Configure WWWDIR - Linux + Windows */
if (isset($_SERVER["REDIRECT_DI8_WWWDIR"])) {
	$_SERVER["DI8_WWWDIR"] = $_SERVER["REDIRECT_DI8_WWWDIR"];
}
if (! isset($_SERVER["DI8_WWWDIR"])) {
	$_SERVER["DI8_WWWDIR"] = "/var/www/desinventar";
}
/* Configure DATADIR - Linux + Windows */
if (isset($_SERVER["REDIRECT_DI8_DATADIR"])) {
	$_SERVER["DI8_DATADIR"] = $_SERVER["REDIRECT_DI8_DATADIR"];
}
if (! isset($_SERVER["DI8_DATADIR"])) {
	$_SERVER["DI8_DATADIR"] = "/var/lib/desinventar";
}

/* Configure CACHEDIR - Linux + Windows */
if (isset($_SERVER["REDIRECT_DI8_CACHEDIR"])) {
	$_SERVER["DI8_CACHEDIR"] = $_SERVER["REDIRECT_DI8_CACHEDIR"];
}
if (! isset($_SERVER["DI8_CACHEDIR"])) {
	$_SERVER["DI8_CACHEDIR"] = "/var/cache/Smarty/di8";
}

if (isset($_SERVER["SHELL"])) {
	$_SERVER["DI8_CACHEDIR"] = "/tmp";
}
if (isset($_SERVER["DI8_WEB"])) {
	define("BASE", $_SERVER["DI8_WEB"]);
	define("SOFTDIR" , "/usr/share/desinventar");
	define("WWWDIR"  , $_SERVER["DI8_WWWDIR"]);
	define("WWWDATA" , "/desinventar-data");
	define("WWWURL"  , "/");
	define("DATADIR" , $_SERVER["DI8_DATADIR"]);
	define("CACHEDIR", $_SERVER["DI8_CACHEDIR"]);
	define("FONTDIR" , "/usr/share/fonts/liberation/fonts.txt");	
} else {
	if (isset($_SERVER["DI8_WEBLOCAL"])) {
		define("BASE", $_SERVER["DI8_WEBLOCAL"]);
	} else {
		define("BASE", "D:/desinventar/devel/web");
		//define("BASE", "/home/gentoo/mayandar/devel/desinventar/web");
	}
//	define("WWWURL"  , "/mayandar/desinventar");
//	define("DATADIR" , "/var/lib/desinventar");
	define("SOFTDIR" , BASE);
	define("WWWDIR"  , BASE . "/tmp");
	define("WWWDATA" , "../tmp");
	define("WWWURL"  , "/");
	define("DATADIR" , "D:/desinventar/data");
	define("CACHEDIR", DATADIR . '/tmp');
	define("FONTDIR" , DATADIR . '/fonts.txt');
}
define("VAR_DIR" , DATADIR);
define("TMP_DIR" , DATADIR);
define("SMTY_DIR", CACHEDIR); // Smarty temp dir
define("TMPM_DIR", CACHEDIR); // Mapserver temp dir
// Test and Create missing directories
createIfNotExistDirectory(VAR_DIR);
createIfNotExistDirectory(TMP_DIR);
createIfNotExistDirectory(SMTY_DIR);
createIfNotExistDirectory(TMPM_DIR);

$lg          = "spa";
$dicore_host = "127.0.0.1"; //"66.150.227.232";
$dicore_port = 8081;

require_once(BASE . "/include/usersession.class.php");
require_once(BASE . "/include/query.class.php");
require_once(BASE . "/include/diobject.class.php");
require_once(BASE . "/include/constants.php");

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
$us->load($us->sSessionId);
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
	$t->force_compile = true;
	$t->caching = false;
	$t->compile_check = true;
	$t->cache_lifetime = -1;
	$t->config_dir = '../include';
	$t->template_dir = 'templates';
	$t->compile_dir = SMTY_DIR;
	$t->left_delimiter = '{-';
	$t->right_delimiter = '-}';

	// Choose Language
	if (isset($_GET['lang']) && !empty($_GET['lang']))
		$lg = $_GET['lang'];
	elseif (isset($_SESSION['lang']))
		$lg = $_SESSION['lang'];

	// 2009-02-21 (jhcaiced) Fix some languages from two to three character code
	if ($lg == 'es') { $lg = 'spa'; }
	if ($lg == 'en') { $lg = 'eng'; }
	if ($lg == 'pr') { $lg = 'por'; }

	$_SESSION['lang'] = $lg;

	$t->assign ("lg", $lg);
}
	
// Common Functions
function createIfNotExistDirectory($sMyPath) {
	if (!file_exists($sMyPath)) {
		error_reporting(E_ALL & ~E_WARNING);
		mkdir($sMyPath);
		error_reporting(E_ALL);
	}
}

function testMap($laypath) {
	if (file_exists($laypath .".shp") && file_exists($laypath .".dbf"))
		return true;
	return false;
}

// Check if session is of a user..
function checkUserSess() {
	// NOTE: need a function checkSession in dicore
	if (isset($us->UserId) && isset($us->sSessionId) &&
			strlen($us->sSessionId) > 0)
		if (strlen($us->UserId) > 0)
			return true;
	return false;
}

// Check if session is of anonymous
function checkAnonSess() {
	if (isset($us->UserId) && isset($us->sSessionId) &&
			strlen($us->sSessionId) > 0)
		if (strlen($us->UserId) == 0)
			return true;
	return false;
}

function iserror ($val) {
	if (is_numeric($val))
		if ($val < 0)
			return true;
	return false;
}
	
function showerror ($val) {
	switch ($val) {
		case ERR_UNKNOWN_ERROR:		$error = "Desconocido"; break;
		case ERR_INVALID_COMMAND:	$error = "Comando inv&aacute;lido"; break;
		case ERR_OBJECT_EXISTS:		$error = "Objeto ya existe"; break;
		case ERR_NO_DATABASE:			$error = "Sin conexi&oacute;n a la BD"; break;
		case ERR_INVALID_PASSWD: 	$error = "Clave inv&aacute;lida"; break;
		case ERR_ACCESS_DENIED: 	$error = "Acceso denegado a Usuario"; break;
		case ERR_OBJECT_NOT_FOUND:$error = "Objeto no funciona"; break;
		case ERR_CONSTRAINT_FAIL:	$error = "Permisos insuficientes"; break;
		case ERR_NO_CONNECTION:		$error = "Sin conexi&oacute;n al Sistema"; break;
		default: 									$error = "No codificado"; break;
	}
	$res = "<span style='color:red'><b>Error:$error</b></span>";
	// Very Serious Errors inmediatly notify to Portal Administrator.. 
	if ($val == ERR_NO_CONNECTION || $val == ERR_NO_DATABASE) {
		$res .= "<span style='font-size: x-small'> (Se notificar&aacute; automaticamente al administrador)</span>";
		// SendMessage ("root@di..", "Severe DI8 Not connection", "Error: $res");
	}
	return $res;
}

// To prevent display errors with strings containing cr,lf,quotes etc. remove them
function str2js($str) {
	$str2 = ereg_replace("[\r\n]", " \\n\\\n", $str);
	$str2 = ereg_replace('"', '-', $str2);
	$str2 = ereg_replace("'", "-", $str2);
	return $str2;
	//return preg_replace('/([^ :!#$%@()*+,-.\x30-\x5b\x5d-\x7e])/e',
	//	"'\\x'.(ord('\\1')<16? '0': '').dechex(ord('\\1'))",$s);
}

// Pseudo-random UUID according to RFC 4122 
function uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), 
			mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
}

// Fix in form _POST var CusQry to let '', ""
function fixPost($post) {
	if (isset($post['__CusQry'])) {
		$post['__CusQry'] = stripslashes($post['__CusQry']);
	}
}

</script>