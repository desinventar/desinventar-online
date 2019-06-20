<?php

namespace Test\Helpers;

use PDO;
use Exception;

class Database
{
    protected static $filename = null;
    protected static $removeWhenDone = true;
    public static function copyDatabase($filename = null)
    {
        if (self::$filename) {
            self::removeDatabase();
        }
        self::$removeWhenDone = !($filename !== '');
        if (!$filename) {
            $filename = tempnam(sys_get_temp_dir(), 'database_');
        }
        if (!$filename) {
            return false;
        }
        copy(__DIR__ . '/../../../files/database/desinventar.db', $filename);
        self::$filename = $filename;
        return $filename;
    }

    public static function removeDatabase()
    {
        if (self::$removeWhenDone && file_exists(self::$filename)) {
            unlink(self::$filename);
        }
        self::$filename = null;
        self::$removeWhenDone = true;
    }

    public static function getConnection()
    {
        return new PDO('sqlite:' . self::$filename);
    }
}
