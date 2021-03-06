<?php

namespace DesInventar\Common;

use Ramsey\Uuid\UuidFactory;

class Util
{
    public function getUrl()
    {
        if (!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['REQUEST_URI'])) {
            return '';
        }
        $url_proto = 'http';
        if ($this->isSslConnection()) {
            $url_proto = 'https';
        }
        $url = $url_proto . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $url;
    }

    public function getBaseUrl()
    {
        $url = $this->getUrl();
        $lastSlashIndex = strrpos($url, '/');
        if ($lastSlashIndex) {
            $url = substr($url, 0, $lastSlashIndex);
        }
        return $url;
    }

    public function isSslConnection()
    {
        if (isset($_SERVER['HTTPS'])) {
            if ('on' == strtolower($_SERVER['HTTPS'])) {
                return true;
            }
            if ('1' == $_SERVER['HTTPS']) {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }

    public function removeSpecialChars($value)
    {
        $response = str_replace(['"', "'"], '', $value);
        $response = str_replace(["\r","\n"], " ", $response);
        $response = str_replace(["\\n\\"], "", $response);
        $response = preg_replace('/ +/', ' ', $response);
        return $response;
    }

    public function replaceChars($regex, $replacmentValue, $value)
    {
        return preg_replace($regex, $replacmentValue, $value);
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
            $var = iconv('UTF-8', 'utf-8', $var);
        }
        return $var;
    }
}
