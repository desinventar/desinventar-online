<?php

namespace Test\Helpers;

use PHPUnit\Framework\TestCase;
use DesInventar\Helpers\Dbf;

final class DbfTest extends TestCase
{
    public function testRecordCount()
    {
        $count = Dbf::getRecordCount(__DIR__ . '/sample.dbf');
        $this->assertEquals(1055, $count);
    }

    public function testGetRecords()
    {
        $records = Dbf::getRecords(__DIR__ . '/sample.dbf', 1055);
        $this->assertEquals(1055, count($records));
    }
}
