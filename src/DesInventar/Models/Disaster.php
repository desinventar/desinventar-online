<?php

namespace DesInventar\Models;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

class Disaster extends Record
{
    protected $tableName = 'Disaster';

    public function updateGeography($oldId, $newId)
    {
        $query = $this->factory->newUpdate();
        $query
            ->table($this->tableName)
            ->cols(['GeographyId'])
            ->where('GeographyId = :id')
            ->bindValues([
                'GeographyId' => $newId,
                'id' => $oldId
            ]);
        return $this->pdo->perform($query->getStatement(), $query->getBindValues());
    }
}
