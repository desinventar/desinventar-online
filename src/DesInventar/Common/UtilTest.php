<?php

namespace DesInventar\Common;

use PHPUnit\Framework\TestCase;

final class UtilTest extends TestCase
{
    public function testRemoveSpecialChars()
    {
        $util = new Util();
        $this->assertEquals('2018-00001', $util->removeSpecialChars('2018-"00001'));
        $this->assertEquals('test line', $util->removeSpecialChars('test' . "\n" . 'line'));
        $this->assertEquals('ab', $util->removeSpecialChars("a\\n\\b"));
        $this->assertEquals('x y z', $util->removeSpecialChars('x    y   z'));
    }
}
