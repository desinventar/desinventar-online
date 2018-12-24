<?php

namespace Test\Common;

use PHPUnit\Framework\TestCase;
use DesInventar\Common\Version;

final class VersionTest extends TestCase
{
    public function testVersion()
    {
        $version = new Version('production');
        $this->assertTrue(is_numeric($version->getMajorVersion()));
        $this->assertTrue(is_array($version->getVersionArray()));
        $this->assertTrue(preg_match('/^\d{4}-\d{2}-\d{2}/', $version->getReleaseDate()) > 0);
        $this->assertTrue(preg_match('/^\d{2}\.\d{2}\.\d{3}/', $version->getVersion()) > 0);
    }
}
