<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
*/

function getBrowserClientLanguage() {
	// 2009-08-13 (jhcaiced) Try to detect the interface language 
	// for the user based on the information sent by the browser...
	$LangStr = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	$IsoLang = '';
	foreach(split(',',$LangStr) as $LangItem) {
		if ($IsoLang == '') {
			$Index = strpos($LangItem, ';'); 
			if ($Index == '') { $Index = strlen($LangItem); }
			$LangItem = substr($LangItem, 0, $Index);
			$Index = strpos($LangItem, '-'); 
			if ($Index == '') { $Index = strlen($LangItem); }
			$LangItem = substr($LangItem, 0, $Index);
			switch($LangItem) {
			case 'en':
				$IsoLang = 'eng';
				break;
			case 'es':
				$IsoLang = 'spa';
				break;
			case 'pt':
				$IsoLang = 'por';
				break;
			} //switch
		} //if
	} //foreach
		
	// Default Case
	if ($IsoLang == '') { $IsoLang = 'eng'; }
	return $IsoLang;
} // function

function getParameter($prmName, $prmDefault) {
	$prmValue = $prmDefault;
	if (isset($_GET[$prmName])) {
		$prmValue = $_GET[$prmName];
	} elseif (isset($_POST[$prmName])) {
		$prmValue = $_POST[$prmName];
	}
	return $prmValue;
}

function showDebugMsg($sMsg) {
	print $sMsg . "<br />\n";
}

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
		if ($val <= 0)
			return true;
	return false;
}
	
function showerror ($val) {
	switch ($val) {
		case ERR_UNKNOWN_ERROR:     $error = "Desconocido"; break;
		case ERR_INVALID_COMMAND:   $error = "Comando inv&aacute;lido"; break;
		case ERR_OBJECT_EXISTS:     $error = "Objeto ya existe"; break;
		case ERR_NO_DATABASE:       $error = "Sin conexi&oacute;n a la BD"; break;
		case ERR_INVALID_PASSWD:    $error = "Clave inv&aacute;lida"; break;
		case ERR_ACCESS_DENIED:     $error = "Acceso denegado a Usuario"; break;
		case ERR_OBJECT_NOT_FOUND:  $error = "Objeto no funciona"; break;
		case ERR_CONSTRAINT_FAIL:   $error = "Permisos insuficientes"; break;
		case ERR_NO_CONNECTION:     $error = "Sin conexi&oacute;n al Sistema"; break;
		default:                    $error = "No codificado"; break;
	}
	$res = "Error: $error";
	// Very Serious Errors inmediatly notify to Portal Administrator.. 
	if ($val == ERR_NO_CONNECTION || $val == ERR_NO_DATABASE) {
		$res .= " (Automatic notification is required)";
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

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


</script>
