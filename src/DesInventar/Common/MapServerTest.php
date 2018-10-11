<?php

namespace DesInventar\Common;

use PHPUnit\Framework\TestCase;

final class MapServerTest extends TestCase
{
    public function testSetRanges()
    {
        $seed = [
            'values' => ['10', '100', ''],
            'labels' => ['Label1', 'Label2', 'Label3'],
            'colors' => ['ffff99', '"ffff00', '#ffffff']
        ];
        $mapserver = new MapServer([]);
        $ranges = $mapserver->setRanges($seed['values'], $seed['labels'], $seed['colors']);
        $this->assertEquals(count($ranges), count($seed['values']) + 1);
        $this->assertEquals($ranges[0][0], 0);
        $this->assertEquals(end($ranges)[0], 10000000);
        foreach ($seed['labels'] as $index => $label) {
            $this->assertEquals($ranges[$index + 1][1], $label);
        }
    }

    public function testHex2dec()
    {
        $mapserver = new MapServer([]);
        $this->assertEquals('0 17 34', $mapserver->hex2dec('001122'));
    }
}
