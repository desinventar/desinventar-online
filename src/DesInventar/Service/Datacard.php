<?php

namespace DesInventar\Service;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

class Datacard
{
    protected $pdo = null;

    public function __construct(ExtendedPdo $pdo)
    {
        $this->pdo = $pdo;
        $this->factory = new QueryFactory('sqlite');
    }

    public function nextSerialSuffix($prefix, $separator)
    {
        $query = $this->factory->newSelect();
        $query->from('Disaster')
            ->cols(['DisasterSerial'])
            ->where('(DisasterSerial LIKE :prefix)')
            ->orderBy(['DisasterSerial DESC'])
            ->limit(1)
            ->bindValues([
                'prefix' => $prefix . $separator . '%',
            ]);
        $serial = '';
        $sth = $this->pdo->perform($query->getStatement(), $query->getBindValues());
        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $serial = $row['DisasterSerial'];
            break;
        }
        $serial = substr($serial, strlen($prefix) + 1);
        $serial = (int) $serial;
        if (empty($serial)) {
            $serial = 0;
        }
        $serial = $serial + 1;
        return $serial;
    }

    public function nextSerial($prefix, $length, $separator)
    {
        $serial = sprintf('%0' . (int)$length . 'd', $this->nextSerialSuffix($prefix, $separator));
        return $prefix . $separator . $serial;
    }
}
