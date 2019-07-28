<?php

namespace DesInventar\Common;

use Exception;
use PDO;
use Aura\Sql\ExtendedPdo;

class DatabaseConnection
{
    private static $instance = null;
    protected $config = null;
    protected $core = null;
    protected $base = null;

    private function __construct($config)
    {
        $this->config = $config;
        $this->core = $this->openSqliteDatabase($this->config['db_dir'] . '/main/core.db');
        $this->base = $this->openSqliteDatabase($this->config['db_dir'] . '/main/base.db');
    }

    public static function getInstance($config)
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection($config);
        }
        return self::$instance;
    }

    public function getCoreConnection()
    {
        return $this->core;
    }

    protected function openSqliteDatabase($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception('Cannot find database file: ' . $filename);
        }
        try {
            $pdo = new PDO('sqlite:' . $filename);
            // set the error reporting attribute
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_TIMEOUT, 10.0);
            $pdo->sqliteCreateFunction('regexp', [__CLASS__, 'sqliteRegexp'], 2);
            return new ExtendedPdo($pdo);
        } catch (Exception $e) {
            throw new Exception('Cannot open database connection: Error: ' . print_r($e, true));
        }
    }
}
