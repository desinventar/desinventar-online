<?php

namespace  DesInventar\Actions;

use DesInventar\Models\Session;
use Aura\Session\Segment;

class UserLoginAction
{
    protected $pdo = null;
    protected $session = null;
    protected $logger = null;

    public function __construct($pdo, $session, $logger)
    {
        $this->pdo = $pdo;
        $this->session = $session;
        $this->logger = $logger;
    }

    public function execute($userId, $password)
    {
        $isValidLogin = (new Session($this->pdo))->login($userId, $password);
        if ($isValidLogin) {
            $this->session->set('userId', $userId);
            $this->session->set('isUserLoggedIn', true);
        }
        return $isValidLogin;
    }
}
