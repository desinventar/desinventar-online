<?php

namespace DesInventar\Actions;

use Exception;
use DesInventar\Models\Geography;

class AdminGeographyUpdateFQNameByCodeAction
{
    protected $pdo = null;
    protected $logger= null;

    public function __construct($pdo, $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function execute($code)
    {
        $geography = (new Geography($this->pdo, $this->logger))->updateFQNameByCode($code, '');
        if (!$geography) {
            throw new Exception('Cannot find geography item with code: ' . $code);
        }
        return $geography;
    }
}
