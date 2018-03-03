<?php

namespace DesInventar\Common;

use Ramsey\Uuid\UuidFactory;

class Util
{
    public static function getUrl()
    {
        if (!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['REQUEST_URI'])) {
            return '';
        }
        $url_proto = 'http';
        if (is_ssl()) {
            $url_proto = 'https';
        }
        $url = $url_proto . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $url;
    }

    public function uuid4()
    {
        $uuid = new UuidFactory();
        return $uuid->uuid4()->toString();
    }

    public function escapeQuotes($prmValue)
    {
        $prmValue = str_replace('"', '', $prmValue);
        return $prmValue;
    }

    public function jsonSafeEncode($var)
    {
        return json_encode($this->jsonFixCyr($var));
    }

    public function jsonFixCyr($var)
    {
        if (is_array($var)) {
            $new = array();
            foreach ($var as $k => $v) {
                $new[$this->jsonFixCyr($k)] = $this->jsonFixCyr($v);
            }
            $var = $new;
        } elseif (is_object($var)) {
            $vars = get_class_vars(get_class($var));
            foreach ($vars as $m => $v) {
                $var->$m = $this->jsonFixCyr($v);
            }
        } elseif (is_string($var)) {
            $var = iconv(DEFAULT_CHARSET, 'utf-8', $var);
        }
        return $var;
    }
}
