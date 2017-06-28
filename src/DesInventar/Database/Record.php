<?php

namespace DesInventar\Database;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

class Record
{
    protected $pdo = null;
    protected $factory = null;

    public function __construct(ExtendedPdo $pdo, $adapter = 'sqlite')
    {
        $this->pdo = $pdo;
        $this->factory = new QueryFactory($adapter);
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
