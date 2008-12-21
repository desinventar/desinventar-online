<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/

/* Main loader..*/

//ob_start( 'ob_gzhandler' );

/* SETTINGS */
// "C:/desinventar8/ms4w/Apache/htdocs/";
// "/var/www/html/desinventar/test/";
define('LNX', true); // false if install on Windows machine..
define('USR', "di8db");
define('PSW', "di8db");
define('DTB', "di8db");
if (isset($_SERVER["DI8WEB"]))
	define("BASE", $_SERVER["DI8WEB"]);
else
	define("BASE", "/var/www/localhost/htdocs/mayandar/DI8");

define("TEMP", "/tmp");
define("DESINVENTARDIR", "/usr/share/desinventar");
define("SMARTYDIR", "/usr/share/Smarty");
define("JPGRAPHDIR", "/usr/share/php/jpgraph");
define("XMLRPCDIR", "/usr/share/php/xmlrpc");

$lg						= "es";
$dicore_host 	= "127.0.0.1"; //"66.150.227.232";
$dicore_port 	= 8081;

///////////////////////////////////////////

// Start manage of SESSION 
session_name("DI8SESSID");
session_start();
error_reporting(E_ALL);
header('Content-Type: text/html; charset=UTF-8');
define("DEFAULT_CHARSET", 'UTF-8');

/* Smarty configuration */
require_once(SMARTYDIR . '/Smarty.class.php');
/* XMLRPC Library */
require_once(XMLRPCDIR . '/xmlrpc.inc');

// Test and Create missing directories
define("VAR_DIR", BASE . '/var');
define("TMP_DIR", BASE . '/tmp');
define("DICT_DIR", DESINVENTARDIR);            // Dictionary Files Directory
define("MAPS_DIR", VAR_DIR. '/maps');          // mapfiles dir
define("LOGO_DIR", VAR_DIR. '/logo');          // database logos dir
define("CART_DIR", VAR_DIR. '/carto');         // Cartography shapes dir
define("CACHEDIR", '/var/cache/Smarty/di8');   // /var/cache/Smarty/di8
define("SMTY_DIR", CACHEDIR . '/templates_c'); // Smarty temp dir
define("TMPM_DIR", CACHEDIR . '/tempmap');     // Mapserver temp dir


createIfNotExistDirectory(VAR_DIR);
createIfNotExistDirectory(TMP_DIR);
createIfNotExistDirectory(MAPS_DIR);
createIfNotExistDirectory(MAPS_DIR ."/templates");
createIfNotExistDirectory(LOGO_DIR);
createIfNotExistDirectory(CART_DIR);
createIfNotExistDirectory(SMTY_DIR);
createIfNotExistDirectory(TMPM_DIR);

// dicore objects
define ("DI_EVENT",			1);
define ("DI_CAUSE",			2);
define ("DI_GEOLEVEL",	3);
define ("DI_GEOGRAPHY",	4);
define ("DI_DISASTER",	5);
define ("DI_DBINFO",		6);
define ("DI_DBLOG",			7);
define ("DI_USER",			8);
define ("DI_REGION",		9);
define ("DI_EEFIELD",	 10);
define ("DI_EEDATA",	 11);

// dicore command
define ("CMD_NEW",			1);
define ("CMD_UPDATE",		2);
define ("CMD_DELETE",		3);

// Error Codes
define ("ERR_NO_ERROR",					 1);
define ("ERR_UNKNOWN_ERROR",		-1);
define ("ERR_INVALID_COMMAND",	-2);
define ("ERR_OBJECT_EXISTS",		-3);
define ("ERR_NO_DATABASE",			-4);
define ("ERR_INVALID_PASSWD",		-5);
define ("ERR_ACCESS_DENIED",		-6);
define ("ERR_OBJECT_NOT_FOUND",	-7);
define ("ERR_CONSTRAINT_FAIL",	-8);
define ("ERR_NO_CONNECTION",		-99);


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

// Function to get method of DICORE by XMLRPC 
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
}

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

/*
	function sendMessage($to, $submit, $body) {
		$headers = "From: di8support@desinventar.org \r\n";
		$headers.= "Content-Type: text/html; charset=UTF-8 ";
		$headers.= "MIME-Version: 1.0 ";
		$mail_sent = @mail($to, $submit, $body, $headers);
		return $mail_sent ? "Mail sent" : "Mail failed";
	}
*/

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

$_SESSION['lang'] = $lg;

$t->assign ("lg", $lg);

</script>
