<?php

namespace Test\Helpers;

use PHPUnit\Framework\TestCase;
use DesInventar\Helpers\Dbf;

final class DbfTest extends TestCase
{
    public function testRecordCount()
    {
        $count = Dbf::getRecordCount(__DIR__ . '/level0.dbf');
        $this->assertEquals(59, $count);
        $count = Dbf::getRecordCount(__DIR__ . '/level1.dbf');
        $this->assertEquals(1055, $count);
    }

    public function testGetRecords()
    {
        $records = Dbf::getRecords(__DIR__ . '/level0.dbf', 59);
        $this->assertEquals(59, count($records));
        $records = Dbf::getRecords(__DIR__ . '/level1.dbf', 1055);
        $this->assertEquals(1055, count($records));
    }
}
