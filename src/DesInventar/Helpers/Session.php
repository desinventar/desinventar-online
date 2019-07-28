<?php

namespace DesInventar\Helpers;

use Aura\Session\SessionFactory;

class Session
{
    protected $session = null;
    public function __construct()
    {
        $sessionFactory = new SessionFactory();
        $this->session = $sessionFactory->newInstance($_COOKIE)->getSegment('');
    }

    public function setUser($userId)
    {
        $this->session->set('userId', $userId);
    }
}
