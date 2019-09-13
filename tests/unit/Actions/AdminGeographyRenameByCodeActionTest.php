<?php

namespace Test\Actions;

use PHPUnit\Framework\TestCase;

use Test\Helpers\Database;
use Test\Helpers\Logger;

use DesInventar\Actions\AdminGeographyRenameByCodeAction;

class AdminGeographyRenameByCodeActionTest extends TestCase
{
    protected $db = null;

    protected function setUp(): void
    {
        $this->db = new Database(Database::REGION);
        $this->db->copyDatabase();
        $this->db->seedFromArray('Geography', [
            [ 'GeographyId' => '00009', 'GeographyCode' => '05' ],
            ['GeographyId' => '0000900005', 'GeographyCode' => '0509' ],
            ['GeographyId' => '000090000500003', 'GeographyCode' => '050903' ]
        ]);
    }

    protected function tearDown(): void
    {
        $this->db->removeDatabase();
    }

    public function testFindParentId()
    {
        $action = new AdminGeographyRenameByCodeAction($this->db->getConnection(), Logger::logger());
        $this->assertEquals('0509', $action->getParentCodeById('000090000500003', 2));
        $this->assertEquals('05', $action->getParentCodeById('0000900005', 1));
        $this->assertEquals('', $action->getParentCodeById('00009', 0));
    }
}
