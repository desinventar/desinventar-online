<?php

namespace Test\Model;

use PHPUnit\Framework\TestCase;

use Test\Helpers\Database;
use Test\Helpers\Logger;

use DesInventar\Models\Geography;
use DesInventar\Legacy\GeographyOperations;

final class GeographyTest extends TestCase
{
    protected $db = null;
    protected $conn = null;

    protected function setUp(): void
    {
        $this->db = new Database(Database::REGION, Logger::logger());
        $this->db->copyDatabase();
        $this->conn = $this->db->getConnection();
        $service = new GeographyOperations($this->conn, Logger::logger());
        GeographyOperations::deleteByLevelId($this->conn, 0);
        $service->importFromArray(0, [
            ['code' => '10', 'name' => 'Sector 1'],
            ['code' => '20', 'name' => 'Sector 2']
        ]);
        $service->importFromArray(1, [
            ['code' => '1001', 'name' => 'District 1-1', 'parentCode' => '10'],
            ['code' => '1002', 'name' => 'District 1-2', 'parentCode' => '10']
        ]);
    }

    protected function tearDown(): void
    {
        $this->db->removeDatabase();
    }

    public function testFindNextChildId()
    {
        $this->assertEquals(3, (new Geography($this->conn, Logger::logger()))->findNextChildId('00001'));
    }
}
