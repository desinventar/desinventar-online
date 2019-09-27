<?php

namespace Test\Helpers;

use Exception;
use PDO;
use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

class Database
{
    public const REGION = __DIR__ . '/../../../files/database/desinventar.db';
    public const CORE = __DIR__ . '/../../../files/database/core.db';

    protected $sourceDb = null;
    protected $logger = null;
    protected $filename = null;
    protected $removeWhenDone = true;

    public function __construct($sourceDb, $logger)
    {
        if (!file_exists($sourceDb)) {
            throw new Exception('Cannot find source database: ' . $sourceDb);
        }
        $this->sourceDb = $sourceDb;
        $this->logger = $logger;
    }

    public function copyDatabase($filename = null)
    {
        if ($this->filename) {
            self::removeDatabase();
        }
        $this->removeWhenDone = !($filename !== '');
        if (!$filename) {
            $filename = tempnam(sys_get_temp_dir(), 'database_');
        }
        if (!$filename) {
            return false;
        }
        if (!copy($this->sourceDb, $filename)) {
            throw new Exception('Failed to copy filename: ' . $this->sourceDb . ' => ' . $filename);
        };
        $this->filename = $filename;
        return $filename;
    }

    public function removeDatabase()
    {
        if ($this->removeWhenDone && file_exists($this->filename)) {
            unlink($this->filename);
        }
        $this->filename = null;
        $this->removeWhenDone = true;
    }

    public function getConnection()
    {
        if (!file_exists($this->filename)) {
            throw new Exception('Database::getConnection(): Cannot find database file: ' . $this->filename);
        }
        return new ExtendedPdo('sqlite:' . $this->filename);
    }

    public function seed($tableName, $fileName)
    {
        if (!file_exists($fileName)) {
            throw new Exception('Cannot open JSON data file');
        }
        $json = file_get_contents($fileName);
        if (!$json) {
            $json = '';
        }
        $data = json_decode($json);
        if (!$data) {
            throw new Exception('Cannot read JSON data from file');
        }
        $this->seedFromArray($tableName, $data);
    }

    public function seedFromArray($tableName, $data)
    {
        $query = (new QueryFactory('sqlite'))->newInsert();
        $query->into($tableName);
        foreach ($data as $row) {
            $query->cols($row);
            $query->addRow();
        }
        $conn = $this->getConnection();
        $conn->perform($query->getStatement(), $query->getBindValues());
    }
}
