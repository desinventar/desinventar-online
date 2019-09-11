<?php

namespace  DesInventar\Actions;

use DesInventar\Models\Session;
use Aura\Session\Segment;

class UserLoginAction
{
    protected $pdo = null;
    protected $logger = null;
    protected $session = null;

    public function __construct($pdo, $logger, $session)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->session = $session;
    }

    public function execute($userId, $password)
    {
        $isValidLogin = (new Session($this->pdo, $this->logger))->login($userId, $password);
        if ($isValidLogin) {
            $this->session->set('userId', $userId);
            $this->session->set('isUserLoggedIn', true);
        }
        return $isValidLogin;
    }
}
