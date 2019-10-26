<?php

namespace Test\Helpers;

use PHPUnit\Framework\TestCase;
use DesInventar\Helpers\Dictionary;

final class DictionaryTest extends TestCase
{
    public function testFindById()
    {
        $dictionary = [
            ['id' => 'testId', 'name' => 'testName']
        ];
        $test = new Dictionary();
        $this->assertEquals(false, $test->findById($dictionary, 'non-existent'));
        $this->assertEquals($dictionary[0], $test->findById($dictionary, 'testId'));
    }
}
