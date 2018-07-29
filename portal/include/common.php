<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

function getBrowserClientLanguage()
{
    // 2009-08-13 (jhcaiced) Try to detect the interface language
    // for the user based on the information sent by the browser...
    $LangStr = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $IsoLang = '';
    foreach (split(',', $LangStr) as $LangItem) {
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
            }
        }
    }

    // Default Case
    if ($IsoLang == '') {
        $IsoLang = 'eng';
    }
    return $IsoLang;
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
