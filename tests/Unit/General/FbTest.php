<?php
namespace Unit\General;

use PHPUnit\Framework\TestCase;

final class FbTest extends TestCase
{
    public function testFbExists()
    {
        $this->assertTrue(function_exists('fb'));
    }
}
