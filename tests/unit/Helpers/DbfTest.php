<?php

namespace Test\Helpers;

use PHPUnit\Framework\TestCase;
use DesInventar\Helpers\Dbf;

final class DbfTest extends TestCase
{
    public function testFieldsCount()
    {
        $count = (new Dbf(__DIR__ . '/level0.dbf'))->getFieldsCount();
        $this->assertEquals(2, $count);
    }

    public function testHeaders()
    {
        $headers = (new Dbf(__DIR__ . '/level0.dbf'))->getHeaders();
        $this->assertEquals(2, count($headers));
        $this->assertEquals('SECTOR', $headers[0]['name']);
        $this->assertEquals('CODIGO', $headers[1]['name']);
    }

    public function testRecordCount()
    {
        $count = (new Dbf(__DIR__ . '/level0.dbf'))->getRecordCount();
        $this->assertEquals(59, $count);
        $count = (new Dbf(__DIR__ . '/level1.dbf'))->getRecordCount();
        $this->assertEquals(1055, $count);
    }

    public function testGetRecords()
    {
        $records = (new Dbf(__DIR__ . '/level0.dbf'))->getRecords(59);
        $this->assertEquals(59, count($records));
        $records = (new Dbf(__DIR__ . '/level1.dbf'))->getRecords(1055);
        $this->assertEquals(1055, count($records));
    }
}
