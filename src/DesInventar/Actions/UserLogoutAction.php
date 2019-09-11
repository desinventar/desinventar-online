<?php

namespace DesInventar\Actions;

use DesInventar\Models\Session;
use Aura\Session\Segment;

class UserLogoutAction
{
    protected $pdo = null;
    protected $logger = null;
    protected $session = null;
    protected $segment = null;

    public function __construct($pdo, $logger, $session)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->session = $session;
        $this->segment = $session->getSegment('');
    }

    public function execute()
    {
        $this->session->destroy();
        return true;
    }
}
