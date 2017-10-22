<?php

namespace DesInventar\Service;

class Datacard extends Service
{
    protected function nextSerialSuffix($year, $prefix, $separator, $length)
    {
        $query = $this->factory->newSelect();
        $query->from('Disaster')
            ->cols(['DisasterSerial'])
            ->where('(DisasterBeginTime LIKE :year)')
            ->where('(DisasterSerial REGEXP :prefix)')
            ->orderBy(['DisasterSerial DESC'])
            ->limit(100)
            ->bindValues([
                'year' => $year . '%',
                'prefix' => '/^' . $prefix . addSlashes($separator) . '[0-9]{' . $length . '}.*$/',
            ]);
        $serial = '';
        $sth = $this->pdo->perform($query->getStatement(), $query->getBindValues());
        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $serial = (int) substr($row['DisasterSerial'], strlen($prefix) + strlen($separator));
            if (! empty($serial)) {
                break;
            }
        }
        if (empty($serial)) {
            $serial = 0;
        }
        $serial = $serial + 1;
        return $serial;
    }

    public function nextSerial($year, $prefix, $length, $separator)
    {
        $serial = sprintf('%0' . (int)$length . 'd', $this->nextSerialSuffix($year, $prefix, $separator, $length));
        return $prefix . $separator . $serial;
    }
}
