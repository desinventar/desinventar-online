<?php

namespace UnitTest\General;

use DesInventar\Common\Version;
use PHPUnit\Framework\TestCase;

final class VersionTest extends TestCase
{
    public function testVersion()
    {
        $version = new Version('production');
        $this->assertTrue(is_numeric($version->getMajorVersion()));
        $this->assertTrue(is_array($version->getVersionArray()));
        $this->assertTrue(preg_match('/\d{4}-\d{2}-\d{2}/', $version->getReleaseDate()) > 0);
    }
}