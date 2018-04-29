<?php

namespace DesInventar\Database;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

class Session extends Record
{
    const ERR_NO_ERROR = 1;

    protected $pdo = null;
    protected $tableName = 'UserSession';
    protected $fieldMap = [
        'id' => ['sqlite' => 'SessionId'],
        'region_id' => ['sqlite' => 'RegionId'],
        'user_id' => ['sqlite' => 'UserId'],
        'valid' => ['sqlite' => 'Valid'],
        'created' => ['sqlite' => 'Start'],
        'modified' => ['sqlite' => 'LastUpdate'],
        'iso_code' => ['sqlite' => 'LangIsoCode'],
    ];

    public function read($id)
    {
        $query = $this->factory->newSelect();
        $query->from($this->tableName)
            ->cols($this->getAliasedFields(['*']))
            ->where($this->getField('id') . '=:id')
            ->bindValue('id', $id);
        return $this->readFirst($query);
    }

    public function login($userId, $passwd)
    {
        $query = $this->factory->newSelect();
        $query->from('User')
            ->cols(['*'])
            ->where('UserPasswd=:UserPasswd')
            ->where('(UserId=:UserId OR UserNotes LIKE :UserNotes)')
            ->bindValues(array(
                'UserPasswd' => $passwd,
                'UserId' => $userId,
                'UserNotes' => '%(UserName=' . $userId. ')%'
            ));
        return $this->readFirst($query);
    }

    public function awake($id, $lastUpdate)
    {
        $query = $this->factory->newUpdate();
        $query->table($this->tableName)
            ->cols(['LastUpdate'])
            ->where('SessionId=:id')
            ->bindValue('id', $id)
            ->bindValue('LastUpdate', $lastUpdate);
        $this->pdo->perform($query->getStatement(), $query->getBindValues());
        return true;
    }

    public function setUser($id, $userId)
    {
        $query = $this->factory->newUpdate();
        $query->table($this->tableName)
            ->cols(['UserId'])
            ->where('SessionId=:id')
            ->bindValues(['id' => $id, 'UserId' => $userId]);
        $this->pdo->perform($query->getStatement(), $query->getBindValues());
        return self::ERR_NO_ERROR;
    }

    public function create($params)
    {
        $query = $this->factory->newInsert();
        $query->into($this->tableName)
            ->cols([
                'SessionId',
                'RegionId',
                'UserId',
                'Valid',
                'LangIsoCode',
                'Start',
                'LastUpdate'
            ])->bindValues($params);
        $this->pdo->perform($query->getStatement(), $query->getBindValues());
        return true;
    }

    public function update($id, $params)
    {
        $query = $this->factory->newUpdate();
        $query->table($this->tableName)
            ->cols([
                'SessionId',
                'RegionId',
                'UserId',
                'Valid',
                'LangIsoCode',
                'Start',
                'LastUpdate'
            ])
            ->where('SessionId=:id')
            ->bindValue('SessionId', $id)
            ->bindValues($params);
        $this->pdo->perform($query->getStatement(), $query->getBindValues());
        return true;
    }

    public function delete($id)
    {
        $query = $this->factory->newDelete();
        $query->from($this->tableName)
            ->where('SessionId=:id')
            ->bindValue('SessionId', $id);
        $this->pdo->perform($query->getStatement(), $query->getBindValues());
        return true;
    }
}
