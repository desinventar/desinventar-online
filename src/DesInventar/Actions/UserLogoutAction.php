<?php

namespace DesInventar\Actions;

use DesInventar\Models\Session;
use Aura\Session\Segment;

class UserLogoutAction
{
    protected $pdo = null;
    protected $session = null;
    protected $segment = null;

    public function __construct($pdo, $session)
    {
        $this->pdo = $pdo;
        $this->session = $session;
        $this->segment = $session->getSegment('');
    }

    public function execute()
    {
        $this->session->destroy();
        return true;
    }
}
