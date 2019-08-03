<?php

namespace DesInventar\Services;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

class Service
{
    protected $pdo = null;
    protected $factory = null;

    public function __construct(ExtendedPdo $pdo)
    {
        $this->pdo = $pdo;
        $this->factory = new QueryFactory('sqlite');
    }
}
