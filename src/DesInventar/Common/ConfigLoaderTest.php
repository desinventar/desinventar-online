<?php

namespace DesInventar\Common;

use PHPUnit\Framework\TestCase;

final class ConfigLoaderTest extends TestCase
{
    public function testLoadConfig()
    {
        $configDir = __DIR__  . '/seed';
        putenv('TEST_WEB_DATABASE=SAMPLE');
        $config = new ConfigLoader($configDir);
        $this->assertTrue(!empty($config));
        $this->assertEquals($config->get('test')['database'], 'SAMPLE');
    }
}
