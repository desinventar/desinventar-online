<?php

namespace Test\Model;

use PHPUnit\Framework\TestCase;

use Test\Helpers\Database;

use DesInventar\Database\Session;

final class SessionTest extends TestCase
{
    protected $db = null;
    protected $conn = null;

    protected function setUp(): void
    {
        $this->db = new Database(Database::CORE);
        $this->db->copyDatabase();
        $this->conn = $this->db->getConnection();
        $this->db->seedFromArray('User', [
            ['UserId' => 'root', 'UserPasswd' => md5('desinventar')]
        ]);
    }

    protected function tearDown(): void
    {
        $this->db->removeDatabase();
    }

    public function testUserLogin()
    {
        $session = new Session($this->conn);
        $this->assertEquals(true, $session->login('root', md5('desinventar')));
        $this->assertEquals(false, $session->login('root', md5('wrongpasswd')));
    }
}
