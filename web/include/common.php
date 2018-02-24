<?php
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
    foreach (preg_split('#,#', $LangStr) as $LangItem) {
        if ($IsoLang == '') {
            $Index = strpos($LangItem, ';');
            if ($Index == '') {
                $Index = strlen($LangItem);
            }
            $LangItem = substr($LangItem, 0, $Index);
            $Index = strpos($LangItem, '-');
            if ($Index == '') {
                $Index = strlen($LangItem);
            }
            $LangItem = substr($LangItem, 0, $Index);
            switch ($LangItem) {
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
    if ($IsoLang == '') {
        $IsoLang = 'eng';
    }
    return $IsoLang;
} // function

function is_ssl()
{
    if (isset($_SERVER['HTTPS'])) {
        if ('on' == strtolower($_SERVER['HTTPS'])) {
            return true;
        }
        if ('1' == $_SERVER['HTTPS']) {
            return true;
        }
    } elseif (isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}

function getParameter($prmName, $prmDefault = '')
{
    $prmValue = $prmDefault;
    if (isset($_GET[$prmName])) {
        $prmValue = $_GET[$prmName];
    } elseif (isset($_POST[$prmName])) {
        $prmValue = $_POST[$prmName];
    }
    $prmValue = trim($prmValue);
    return $prmValue;
}

function getCmd()
{
    return getParameter('cmd', getParameter('prmQueryCommand', getParameter('_CMD', '')));
}

function showDebugMsg($sMsg)
{
    echo $sMsg . "<br />\n";
}

function createIfNotExistDirectory($sMyPath)
{
    if (!file_exists($sMyPath)) {
        error_reporting(E_ALL & ~E_WARNING);
        mkdir($sMyPath);
        error_reporting(E_ALL);
    }
}

function testMap($laypath)
{
    $iReturn = false;
    if (file_exists($laypath .".shp") && file_exists($laypath .".dbf")) {
        $iReturn = true;
    }
    return $iReturn;
}

// Check if session is of a user..
function checkUserSess()
{
    $iReturn = false;
    // NOTE: need a function checkSession in dicore
    if ((isset($us->UserId)) &&
         (isset($us->sSessionId)) &&
         (strlen($us->sSessionId) > 0) ) {
        if (strlen($us->UserId) > 0) {
            $iReturn = true;
        }
    }
    return $iReturn;
}

// Check if session is of anonymous
function checkAnonSess()
{
    $iReturn = false;
    if ((isset($us->UserId)) &&
         (isset($us->sSessionId)) &&
         (strlen($us->sSessionId) > 0) ) {
        if (strlen($us->UserId) == 0) {
            $iReturn = true;
        }
    }
    return $iReturn;
}

function iserror($val)
{
    $iReturn = false;
    if (is_numeric($val)) {
        if ($val <= 0) {
            $iReturn = true;
        }
    }
    return $iReturn;
}

function showerror($val)
{
    $map = [
        ERR_UNKNOWN_ERROR => "Desconocido",
        ERR_INVALID_COMMAND => "Comando inv&aacute;lido",
        ERR_OBJECT_EXISTS => "Objeto ya existe",
        ERR_NO_DATABASE => "Sin conexi&oacute;n a la BD",
        ERR_INVALID_PASSWD => "Clave inv&aacute;lida",
        ERR_ACCESS_DENIED => "Acceso denegado a Usuario",
        ERR_OBJECT_NOT_FOUND => "Objeto no funciona",
        ERR_CONSTRAINT_FAIL => "Permisos insuficientes",
        ERR_NO_CONNECTION => "Sin conexi&oacute;n al Sistema"
    ];

    $error = "No codificado";
    if (isset($map[$val])) {
        $error = $map[$val];
    }
    $res = "Error: $error";
    // Very Serious Errors inmediatly notify to Portal Administrator..
    if ($val == ERR_NO_CONNECTION || $val == ERR_NO_DATABASE) {
        $res .= " (Automatic notification is required)";
    }
    return $res;
}

// To prevent display errors with strings containing cr,lf,quotes etc. remove them
function str2js($str)
{
    $str2 = preg_replace("[\r\n]", " \\n\\\n", $str);
    $str2 = preg_replace('"', '', $str2);
    $str2 = preg_replace("'", "", $str2);
    return $str2;
}

function fixPost($post)
{
    if (isset($post['QueryCustom'])) {
        $post['QueryCustom'] = stripslashes($post['QueryCustom']);
    }
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function generatePasswd($length = 6, $level = 2)
{
    list($usec, $sec) = explode(' ', microtime());
    srand((float) $sec + ((float) $usec * 100000));
    $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
    $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

    $password  = "";
    $counter   = 0;
    while ($counter < $length) {
        $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);
        // All character must be different
        if (!strstr($password, $actChar)) {
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
    $value = trim($value);
    $str = '';
    $max = strlen($value)%3 - 1;
    if ($max < 0) {
        $max = 2;
    }
    for ($i=0; $i<strlen($value); $i++) {
        $str .= $value[$i];
        $max--;
        if ($max < 0) {
            $str .= ' ';
            $max = 2;
        }
    }
    return $str;
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") {
                    rrmdir($dir."/".$object);
                    break;
                }
                unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    } #if
}  #rrmdir()

function padNumber($prmValue, $prmLength)
{
    $value = $prmValue;
    while (strlen($value) < $prmLength) {
        $value = '0' . $value;
    }
    return $value;
}

function getFont($prmFontName)
{
    $font = $prmFontName;
    if (isset($_SERVER['WINDIR'])) {
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
