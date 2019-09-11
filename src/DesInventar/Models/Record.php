<?php

namespace DesInventar\Models;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

class Record
{
    protected $pdo = null;
    protected $logger = null;
    protected $adapter = null;
    protected $factory = null;
    protected $fieldMap = array();
    protected $fields = array();

    public function __construct(ExtendedPdo $pdo, $logger, $adapter = 'sqlite')
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->adapter = $adapter;
        $this->factory = new QueryFactory($this->adapter);
    }

    protected function getField($field)
    {
        if (isset($this->fields[$field])) {
            return $this->fields[$field];
        }
        if (isset($this->fieldMap[$field][$this->adapter])) {
            return $this->fieldMap[$field][$this->adapter];
        }
        return $field;
    }

    protected function getMappedFields()
    {
        if (!empty($this->fields)) {
            return $this->fields;
        }
        $fields = array();
        foreach (array_keys($this->fieldMap) as $key) {
            $fields[$key] = $this->getField($key);
        }
        $this->fields = $fields;
        return $fields;
    }

    protected function getAliasedFields($fields)
    {
        $fieldList = [];
        foreach ($fields as $field) {
            if ($field == '*') {
                $fieldList = $this->getMappedFields();
                break;
            }
            $fieldList[$field] = $this->getField($field);
        }
        $response = [];
        foreach ($fieldList as $field => $alias) {
            $response[$alias] = $field;
        }
        return $response;
    }

    protected function readFirst($query)
    {
        $response = null;
        $sth = $this->pdo->perform($query->getStatement(), $query->getBindValues());
        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $response = $row;
            break;
        }
        return $response;
    }
}
