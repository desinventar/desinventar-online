<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function getBrowserClientLanguage()
{
	// 2009-08-13 (jhcaiced) Try to detect the interface language 
	// for the user based on the information sent by the browser...
	$LangStr = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	$IsoLang = '';
	foreach(preg_split('#,#',$LangStr) as $LangItem)
	{
		if ($IsoLang == '')
		{
			$Index = strpos($LangItem, ';'); 
			if ($Index == '')
			{
				$Index = strlen($LangItem);
			}
			$LangItem = substr($LangItem, 0, $Index);
			$Index = strpos($LangItem, '-'); 
			if ($Index == '')
			{
				$Index = strlen($LangItem);
			}
			$LangItem = substr($LangItem, 0, $Index);
			switch($LangItem)
			{
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
	if ($IsoLang == '')
	{
		$IsoLang = 'eng';
	}
	return $IsoLang;
} // function

function is_ssl() {
	if ( isset($_SERVER['HTTPS']) ) {
		if ( 'on' == strtolower($_SERVER['HTTPS']) ) {
			return true;
		}
		if ( '1' == $_SERVER['HTTPS'] ) {
			return true;
		}
	} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
	return false;
}

function getParameter($prmName, $prmDefault='')
{
	$prmValue = $prmDefault;
	if (isset($_GET[$prmName]))
	{
		$prmValue = $_GET[$prmName];
	}
	elseif (isset($_POST[$prmName]))
	{
		$prmValue = $_POST[$prmName];
	}
	$prmValue = trim($prmValue);
	return $prmValue;
}

function getCmd() {
	return getParameter('cmd', getParameter('prmQueryCommand', getParameter('_CMD','')));
}

function showDebugMsg($sMsg)
{
	echo $sMsg . "<br />\n";
}

function createIfNotExistDirectory($sMyPath)
{
	if (!file_exists($sMyPath))
	{
		error_reporting(E_ALL & ~E_WARNING);
		mkdir($sMyPath);
		error_reporting(E_ALL);
	}
}

function testMap($laypath)
{
	$iReturn = false;
	if (file_exists($laypath .".shp") && file_exists($laypath .".dbf"))
	{
		$iReturn = true;
	}
	return $iReturn;
}

// Check if session is of a user..
function checkUserSess()
{
	$iReturn = false;
	// NOTE: need a function checkSession in dicore
	if ( (isset($us->UserId)) && 
	     (isset($us->sSessionId)) &&
	     (strlen($us->sSessionId) > 0) )
	{
		if (strlen($us->UserId) > 0)
		{
			$iReturn = true;
		}
	}
	return $iReturn;
}

// Check if session is of anonymous
function checkAnonSess()
{
	$iReturn = false;
	if ( (isset($us->UserId)) && 
	     (isset($us->sSessionId)) &&
	     (strlen($us->sSessionId) > 0) )
	{
		if (strlen($us->UserId) == 0)
		{
			$iReturn = true;
		}
	}
	return $iReturn;
}

function iserror ($val)
{
	$iReturn = false;
	if (is_numeric($val))
	{
		if ($val <= 0)
		{
			$iReturn = true;
		}
	}
	return $iReturn;
}
	
function showerror ($val)
{
	switch ($val)
	{
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
	if ($val == ERR_NO_CONNECTION || $val == ERR_NO_DATABASE)
	{
		$res .= " (Automatic notification is required)";
	}
	return $res;
}

// To prevent display errors with strings containing cr,lf,quotes etc. remove them
function str2js($str)
{
	$str2 = ereg_replace("[\r\n]", " \\n\\\n", $str);
	$str2 = ereg_replace('"', '', $str2);
	$str2 = ereg_replace("'", "", $str2);
	return $str2;
	//return preg_replace('/([^ :!#$%@()*+,-.\x30-\x5b\x5d-\x7e])/e',
	//	"'\\x'.(ord('\\1')<16? '0': '').dechex(ord('\\1'))",$s);
}

// Pseudo-random UUID according to RFC 4122 
function uuid()
{
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), 
			mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
}

function fixPost($post)
{
	if (isset($post['QueryCustom']))
	{
		$post['QueryCustom'] = stripslashes($post['QueryCustom']);
	}
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function generatePasswd($length=6,$level=2)
{
	list($usec, $sec) = explode(' ', microtime());
	srand((float) $sec + ((float) $usec * 100000));
	$validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
	$validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";
	
	$password  = "";
	$counter   = 0;
	while ($counter < $length)
	{
		$actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);
		// All character must be different
		if (!strstr($password, $actChar))
		{
			$password .= $actChar;
			$counter++;
		} //if
	} //while
	return $password;
} //function

// Process a number, adding a space to separate each triplet
// according to standard scientific representation.
function showStandardNumber($value)
{
	$v = trim($value);
	$s = '';
	$m = strlen($v)%3 - 1;
	if ($m < 0)
	{
		$m = 2;
	}
	for($i=0; $i<strlen($v); $i++)
	{	
		$s .= $v[$i];
		$m--;
		if ($m < 0)
		{
			$s .= ' ';
			$m = 2;
		}
	}
	return $s;
}

function rrmdir($dir)
{
	if (is_dir($dir))
	{
		$objects = scandir($dir);
		foreach ($objects as $object)
		{
			if ($object != "." && $object != "..")
			{
				if (filetype($dir."/".$object) == "dir")
				{
					rrmdir($dir."/".$object);
				}
				else
				{
					unlink($dir."/".$object);
				}
			}
		}
		reset($objects);
		rmdir($dir);
	} #if
}  #rrmdir()

function padNumber($prmValue, $prmLength)
{
	$value = $prmValue;
	while(strlen($value) < $prmLength)
	{
		$value = '0' . $value;
	}
	return $value;
}

function getFont($prmFontName)
{
	$font = $prmFontName;
	if (isset($_SERVER['WINDIR']))
	{
		$font = $_SERVER['WINDIR'] . '/fonts/' . $prmFontName;
	}
	return $font;
} #getFont()


function showErrorMsg($prm_debug, $e, $prm_error_message)
{
	$debug = array_merge(array('class' => '', 'type' => '', 'function' => '', 'line' => ''), $prm_debug[0]);
	$error_locator = $debug['class'] . $debug['type'] . $debug['function'] . ':' . $debug['line'];
	$error_message = '[DESINVENTAR_ERROR] ' . $error_locator;
	$error_pieces = array();
	if (!empty($prm_error_message)) {
		$error_pieces[] = $prm_error_message;
	}
	if ($e != null) {
		$error_pieces[] = $e->getMessage();
	}
	if (count($error_pieces) > 0) {
		$error_message .= ' => ' . implode(':', $error_pieces);
	}
	error_log($error_message);
}

