<?php

namespace Test\Legacy;

use Exception;
use PHPUnit\Framework\TestCase;

use DesInventar\Legacy\PersistQuery;

final class PersistQueryTest extends TestCase
{
    public function testQueryFromXml()
    {
        $xml = file_get_contents(__DIR__ . '/__samples__/query.xml');

        $this->assertEquals(true, is_array(PersistQuery::getQueryFromXml($xml)));
    }
}
