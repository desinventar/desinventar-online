<?php

namespace DesInventar\Legacy;

use Exception;

class PersistQuery
{
    public static function getQueryFromXml($xmlStr)
    {
        try {
            $xml = simplexml_load_string($xmlStr);
            if (!$xml) {
                return [];
            }
            $query = $xml->diquery;
            $version = (string) $query->version;
            if ($version !== '1.1') {
                return [];
            }
            $encodedString = (string) $query->value;
            return unserialize(base64_decode($encodedString));
        } catch (Exception $e) {
            return [];
        }
    }
}
