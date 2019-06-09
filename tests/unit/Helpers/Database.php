<?php

namespace Test\Helpers;

use PDO;

class Database
{
    protected static $filename = null;
    public static function copyDatabase()
    {
        if (self::$filename) {
            self::removeDatabase();
        }
        $filename= tempnam(sys_get_temp_dir(), 'database_') . '.db';
        copy(__DIR__ . '/../../../files/database/desinventar.db', $filename);
        self::$filename = $filename;
        return $filename;
    }

    public static function removeDatabase()
    {
        unlink(self::$filename);
        self::$filename = null;
    }

    public static function getConnection()
    {
        return new PDO('sqlite:' . self::$filename);
    }
}
