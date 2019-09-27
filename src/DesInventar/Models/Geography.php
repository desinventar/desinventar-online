<?php

namespace DesInventar\Models;

use PDO;
use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

class Geography extends Record
{
    protected $tableName = 'Geography';

    public function findById($id)
    {
        $query = $this->factory->newSelect();
        $query->from($this->tableName)
            ->cols(['*'])
            ->where('GeographyId=:id')
            ->bindValue('id', $id);
        return $this->readFirst($query);
    }

    public function findByCode($code)
    {
        $query = $this->factory->newSelect();
        $query->from($this->tableName)
            ->cols(['*'])
            ->where('GeographyCode=:code')
            ->bindValue('code', $code);
        return $this->readFirst($query);
    }

    public function update($id, $values)
    {
        $query = $this->factory->newUpdate();
        $query
            ->table($this->tableName)
            ->cols(array_keys($values))
            ->where('GeographyId = :id')
            ->bindValues(array_merge(
                $values,
                ['id' => $id]
            ));
        return $this->pdo->perform($query->getStatement(), $query->getBindValues());
    }

    public function findNextChildId($id)
    {
        $query = $this->factory->newSelect();
        $query->from($this->tableName)
            ->cols(['COUNT(*) AS C'])
            ->where('GeographyId LIKE :id')
            ->bindValue('id', "{$id}%")
            ->orderBy(['GeographyId']);
        $res = $this->readFirst($query);
        return intval($res['C']);
    }

    public function findChildren($id)
    {
        $level = intval(strlen($id)/5);
        $query = $this->factory->newSelect();
        $query->from($this->tableName)
            ->cols([
                'GeographyId AS id',
                'GeographyCode AS code',
                'GeographyName AS name'
            ])
            ->where('GeographyId LIKE :id')
            ->where('GeographyLevel = :level')
            ->bindValue('id', "{$id}%")
            ->bindValue('level', $level)
            ->orderBy(['GeographyCode']);
            $sth = $this->pdo->perform($query->getStatement(), $query->getBindValues());
            return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateFQNameByCode($code)
    {
        $geography = $this->findByCode($code);
        if (!$geography) {
            return false;
        }
        $FQName = $geography['GeographyId'] . ' => ';
        $names = [];
        $id = '';
        for ($i = 0; $i < floor(strlen($geography['GeographyId'])/5); $i++) {
            $id .= substr($geography['GeographyId'], $i * 5, 5);
            $name = ($this->findById($id))['GeographyName'];
            $names[] = $name;
        }
        $FQName = implode('/', $names);
        $this->update($geography['GeographyId'], ['GeographyFQName' => $FQName]);
        return $FQName;
    }
}
