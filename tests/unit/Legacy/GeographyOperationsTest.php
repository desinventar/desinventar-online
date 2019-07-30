<?php

namespace Test\Legacy;

use Exception;
use PHPUnit\Framework\TestCase;

use Test\Helpers\Database;
use Test\Helpers\Logger;
use DesInventar\Legacy\GeographyOperations;

final class GeographyOperationsTest extends TestCase
{
    protected $db = null;

    protected function setUp(): void
    {
        $this->db = new Database(Database::REGION);
        $this->db->copyDatabase();
    }

    protected function tearDown(): void
    {
        $this->db->removeDatabase();
    }

    public function testRecordsFromDbfWithCorrectColumns()
    {
        $records = GeographyOperations::getRecordsFromDbf(
            __DIR__ . '/../Helpers/level0.dbf',
            ['code' => 'CODIGO', 'name' => 'SECTOR']
        );
        $this->assertEquals(59, count($records));
        $this->assertEquals(['name' => 'SECTOR 29', 'code' => '29', 'deleted' => 0], $records['29']);
    }

    public function testRecordsFromDbfWithWrongColumns()
    {
        $this->expectException(Exception::class);
        GeographyOperations::getRecordsFromDbf(
            __DIR__ . '/../Helpers/level0.dbf',
            ['code' => 'NO-CODE', 'name' => 'NO-NAME']
        );
    }

    public function testRecordsFromDbfWithParent()
    {
        $records = GeographyOperations::getRecordsFromDbf(
            __DIR__ . '/../Helpers/level1.dbf',
            ['code' => 'COD_COL_1', 'name' => 'NOM_COL', 'parentCode' => 'SECTOR']
        );
        $this->assertEquals(944, count($records));
        $this->assertEquals(
            ['name' => 'RESIDENCIAL PINO VERDE', 'code' => '3643', 'parentCode' => '36', 'deleted' => 0],
            $records['3643']
        );
    }

    public function testFilterDeletedRecords()
    {
        $records = GeographyOperations::filterDeletedRecords([
            ['name' => 'test1', 'deleted' => 1],
            ['name' => 'test2', 'deleted' => 0],
            ['name' => 'test3']]);
        $this->assertEquals(2, count($records));
    }

    public function testImportGeographyFromCsv()
    {
        $conn = $this->db->getConnection();
        $logger = Logger::logger();
        GeographyOperations::deleteByLevelId($conn, 0);
        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 0));

        $service = new GeographyOperations($conn, $logger);
        $service->importFromCsv(
            0,
            __DIR__ . '/../Helpers/level0.csv',
            ['code' => 'Codigo', 'name' => 'Nombre']
        );
        $this->assertEquals(8, GeographyOperations::countByLevelId($conn, 0));
    }

    public function testImportGeographyFromDbf()
    {
        $conn = $this->db->getConnection();
        $logger = Logger::logger();

        GeographyOperations::deleteByLevelId($conn, 0);
        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 0));

        $service = new GeographyOperations($conn, $logger);
        $service->importFromDbf(
            0,
            __DIR__ . '/../Helpers/level0.dbf',
            ['code' => 'CODIGO', 'name' => 'SECTOR']
        );
        $this->assertEquals(59, GeographyOperations::countByLevelId($conn, 0));

        GeographyOperations::deleteByLevelId($conn, 1);
        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 1));

        $columns = ['code' => 'COD_COL_1', 'name' => 'NOM_COL', 'parentCode' => 'SECTOR'];
        $records = GeographyOperations::filterDeletedRecords(
            GeographyOperations::getRecordsFromDbf(
                __DIR__ . '/../Helpers/level1.dbf',
                $columns
            )
        );
        $records = array_slice($records, 0, 10);
        $service->importFromArray(
            1,
            $records
        );
        $this->assertEquals(10, GeographyOperations::countByLevelId($conn, 1));
    }

    public function testImportFromArray()
    {
        $conn = $this->db->getConnection();
        $service = new GeographyOperations($conn, Logger::logger());
        GeographyOperations::deleteByLevelId($conn, 0);

        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 0));
        $service->importFromArray(0, [
            ['code' => '10', 'name' => 'Sector 10'],
            ['code' => '20', 'name' => 'Sector 20A']
        ]);
        $this->assertEquals(2, GeographyOperations::countByLevelId($conn, 0));
        $service->importFromArray(0, [
            ['code' => '20', 'name' => 'Sector 20']
        ]);
        $this->assertEquals(2, GeographyOperations::countByLevelId($conn, 0));

        GeographyOperations::deleteByLevelId($conn, 1);
        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 1));
        $service->importFromArray(1, [
            ['code' => '2011', 'name' => 'Colonia 2011A', 'parentCode' => '20'],
            ['code' => '2012', 'name' => 'Colonia 2012A', 'parentCode' => '20']
        ]);
        $this->assertEquals(2, GeographyOperations::countByLevelId($conn, 1));

        $service->importFromArray(1, [
            ['code' => '2011', 'name' => 'Colonia 2011', 'parentCode' => '20'],
            ['code' => '2012', 'name' => 'Colonia 2012', 'parentCode' => '20']
        ]);
        $this->assertEquals(2, GeographyOperations::countByLevelId($conn, 1));
    }

    public function testImportWithDuplicateName()
    {
        $conn = $this->db->getConnection();
        $service = new GeographyOperations($conn, Logger::logger());
        GeographyOperations::deleteByLevelId($conn, 0);
        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 0));
        $service->importFromArray(0, [
            ['code' => '10', 'name' => 'Trantor 10'],
            ['code' => '20', 'name' => 'Trantor 20']
        ]);
        $this->assertEquals(2, GeographyOperations::countByLevelId($conn, 0));

        GeographyOperations::deleteByLevelId($conn, 1);
        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 1));
        $service->importFromArray(0, [
            ['code' => '1010', 'name' => 'Dahl', 'parentCode' => '10' ],
            ['code' => '1020', 'name' => 'Dahl', 'parentCode' => '10' ],
            ['code' => '1030', 'name' => 'Ery', 'parentCode' => '10' ],
            ['code' => '2010', 'name' => 'Dahl', 'parentCode' => '20' ],
            ['code' => '2020', 'name' => 'Ery', 'parentCode' => '20' ],
            ['code' => '2030', 'name' => 'Ery', 'parentCode' => '20' ]
        ]);
        $this->assertEquals(6, GeographyOperations::countByLevelId($conn, 1));
    }

    public function testImportWithParentNotFound()
    {
        $conn = $this->db->getConnection();
        $service = new GeographyOperations($conn, Logger::logger());
        GeographyOperations::deleteByLevelId($conn, 0);
        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 0));
        $service->importFromArray(0, [
            ['code' => '10', 'name' => 'Trantor 10'],
            ['code' => '20', 'name' => 'Trantor 20']
        ]);
        $this->assertEquals(2, GeographyOperations::countByLevelId($conn, 0));

        GeographyOperations::deleteByLevelId($conn, 1);
        $this->assertEquals(0, GeographyOperations::countByLevelId($conn, 1));
        $service->importFromArray(0, [
            ['code' => '1010', 'name' => 'Dahl', 'parentCode' => '15' ],
            ['code' => '2020', 'name' => 'Ery', 'parentCode' => '20' ],
        ]);
        $this->assertEquals(2, GeographyOperations::countByLevelId($conn, 0));
        $this->assertEquals(1, GeographyOperations::countByLevelId($conn, 1));
    }
}
