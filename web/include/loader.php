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
define('LNX', true); // false if install on Windows machine..

define("TEMP", "/tmp");
define("SMARTYDIR", "/usr/share/Smarty");
define("JPGRAPHDIR", "/usr/share/php/jpgraph");
//define("XMLRPCDIR", "/usr/share/php/xmlrpc");

if (isset($_SERVER["DI8WEB"])) {
	define("BASE", $_SERVER["DI8WEB"]);
	define("SOFTDIR" , "/usr/share/desinventar");
	define("WWWDIR"  , "/var/www/desinventar");
	define("WWWURL"  , "/desinventar-data");
	define("DATADIR" , "/var/lib/desinventar");
	define("CACHEDIR", "/var/cache/Smarty");
	define("FONTDIR" , "/usr/share/fonts/liberation/fonts.txt");	
	define("DICT_DIR", SOFTDIR . "/files");
	define("VAR_DIR" , DATADIR);
	define("TMP_DIR" , DATADIR);
	define("SMTY_DIR", CACHEDIR); // Smarty temp dir
	define("TMPM_DIR", CACHEDIR);     // Mapserver temp dir
} else {
	if (isset($_SERVER["DI8WEBLOCAL"])) {
		define("BASE", $_SERVER["DI8WEBLOCAL"]);
	} else {
		//define("BASE", "/var/www/html/desinventar");
		define("BASE", "/home/gentoo/mayandar/devel/desinventar/web");
	}
	define("SOFTDIR" , BASE);
	define("WWWDIR"  , BASE . "/tmp");
	define("WWWURL"  , "../tmp");
	define("DATADIR" , "/var/lib/desinventar");
	define("VAR_DIR" , DATADIR . '/var');
	define("TMP_DIR" , DATADIR . '/tmp');
	define("CACHEDIR", TMP_DIR);   			 // /var/cache/Smarty/di8
	define("FONTDIR" , VAR_DIR . '/fonts.txt');
 	define("SMTY_DIR", CACHEDIR . '/templates_c'); // Smarty temp dir
	define("TMPM_DIR", CACHEDIR . '/tempmap');     // Mapserver temp dir
}
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
require_once(BASE . "/include/constants.php");

// Session Management
session_name("DI8SESSID");
session_start();

/*
print "Script    : " . $_SERVER['SCRIPT_NAME'] . "<br>";
print "FileName  : " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
print "SessionId : " . session_id() . "<br>";
*/
// 2009-01-15 (jhcaiced) Start by create/recover the session 
// information, even for anonymous users
if (!isset($_SESSION['sessioninfo'])) { 
	$us = new UserSession(session_id());
	$_SESSION['sessioninfo'] = $us;
} else {
	$us = $_SESSION['sessioninfo'];
	$us->load($us->sSessionId);
}
$us->awake();

error_reporting(E_ALL);
header('Content-Type: text/html; charset=UTF-8');
define("DEFAULT_CHARSET", 'UTF-8');

/* Smarty configuration */
require_once(SMARTYDIR . '/Smarty.class.php');
/* XMLRPC Library */
//require_once(XMLRPCDIR . '/xmlrpc.inc');

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
	if (isset($_SESSION['username']) && isset($_SESSION['sessionid']) &&
			strlen($_SESSION['sessionid']) > 0)
		if (strlen($_SESSION['username']) > 0)
			return true;
	return false;
}

// Check if session is of anonymous
function checkAnonSess() {
	if (isset($_SESSION['username']) && isset($_SESSION['sessionid']) &&
			strlen($_SESSION['sessionid']) > 0)
		if (strlen($_SESSION['username']) == 0)
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
	$res = '<span style="color:red"><b>Error:</b> '. $error .'</span> ';
	// Very Serious Errors inmediatly notify to Portal Administrator.. 
	if ($val == ERR_NO_CONNECTION || $val == ERR_NO_DATABASE) {
		$res .= '<span style="font-size: x-small"> (Se notificar&aacute; automaticamente al administrador)</span>';
		// SendMessage ("root@di..", "Severe DI8 Not connection", "Error: $res");
	}
	return $res;
}

/* OBSOLETE: Function to get method of DICORE by XMLRPC 
function callRpcDICore($rpcmethod, $rpcargs) {
	global $dicore_host, $dicore_port;
	$xmlrpcargs = array();
	$c = $f = $r = null;
	$c = new xmlrpc_client("", $dicore_host, $dicore_port);
	if (!is_array($rpcargs))
		return ERR_INVALID_COMMAND;
	// encode real args
	foreach ($rpcargs as $val)
		array_push($xmlrpcargs, php_xmlrpc_encode($val));
	$f = new xmlrpcmsg($rpcmethod, $xmlrpcargs);
	$r =& $c->send($f, 3600);
	if (!$r->faultCode())
		return php_xmlrpc_decode($r->value());
	else {
		//echo "Code: (" . htmlspecialchars($r->faultCode()) . ") Reason: '" . htmlspecialchars($r->faultString()) . "'\n";
		return ERR_NO_CONNECTION;
	}
}*/

// To prevent fails in Strings with RETURNS or "s eliminate this.. 
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

</script>
