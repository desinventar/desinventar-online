<?php

namespace DesInventar\Service;

class RegionInfo extends Service
{
    protected $defaults = [
        'SerialSuffixSize' => 5,
        'SerialCloneSuffixSize' => 3,
    ];

    public function update($params)
    {
        $now = $this->pdo->quote(date(\DateTime::ISO8601));
        $query = $this->factory->newInsert();
        $query->into('info')
            ->cols(['InfoKey', 'InfoValue'])
            ->set('RecordCreation', $now)
            ->set('RecordSync', $now)
            ->set('RecordUpdate', $now);

        foreach ($params as $key => $value) {
            $query->bindValues(['InfoKey' => $key, 'InfoValue' => $value]);
            $this->pdo->perform($query->getStatement(), $query->getBindValues());
        }
    }

    public function getAll($keys)
    {
        $query = $this->factory->newSelect();
        $query->from('info')->cols(['InfoKey','InfoValue'])->where('InfoKey IN (:keys)');
        $response = $this->defaults;
        $response = array_merge(
            array_reduce(
                $keys,
                function ($result, $item) {
                    $result[$item] = null;
                    return $result;
                },
                array()
            ),
            $this->defaults
        );
        foreach ($this->pdo->fetchAssoc($query->getStatement(), ['keys' => $keys]) as $key => $row) {
            if (!empty($row['InfoValue'])) {
                $response[$key] = $row['InfoValue'];
            }
        }
        return $response;
    }

    public function deleteAll($keys)
    {
        $query = $this->factory->newDelete();
        $query->from('info')
            ->where('InfoKey IN (:keys)');
        $sth = $this->pdo->perform($query->getStatement(), ['keys' => $keys]);
        $sth->execute($query->getBindValues());
    }
}
