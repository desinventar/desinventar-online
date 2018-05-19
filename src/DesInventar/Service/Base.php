<?php

namespace DesInventar\Service;

class Base extends Service
{
    public function getLanguagesList()
    {
        $query = $this->factory->newSelect();
        $query->from('Language')->cols(['*']);
        $list = [];
        $sth = $this->pdo->perform($query->getStatement(), $query->getBindValues());
        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $list[$row['LangIsoCode']] = [
                'iso' => $row['LangIsoName'],
                'local' => $row['LangLocalName']
            ];
        }
        return $list;
    }
}
