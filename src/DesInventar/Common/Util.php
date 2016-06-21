<?php
/**
 * Util Class
 */

namespace DesInventar\Common;

class Util
{
    public static function getUrl()
    {
        $url_proto = 'http';
        if (is_ssl()) {
            $url_proto = 'https';
        }
        $url = $url_proto . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $url;
    }
}
