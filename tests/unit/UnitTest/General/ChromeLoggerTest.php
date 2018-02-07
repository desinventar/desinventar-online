<?php

namespace UnitTest\General;

use PHPUnit\Framework\TestCase;

final class ChromeLoggerTest extends TestCase
{
    public function testChromeLoggerExists()
    {
        $this->assertTrue(method_exists('ChromePhp', 'log'));
    }
}
