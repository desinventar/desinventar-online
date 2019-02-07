<?php

namespace Test\Legacy\Model;

use PHPUnit\Framework\TestCase;
use DesInventar\Legacy\Model\DisasterImport;

final class DisasterImportTest extends TestCase
{
    public function testStringFunctions()
    {
        $import = new DisasterImport(null, '', '');
        $this->assertEquals('ABC', $import->stringToDIField('AB"$C'));
        $this->assertEquals('3.45', $import->stringToDIField('$3,45'));
        $this->assertEquals(-1, $import->sectorToDIField('hubo'));
        $this->assertEquals(0, $import->sectorToDIField('no hubo'));
        $this->assertEquals(10.4, $import->valueToDIField('$10,4'));
        $this->assertEquals(100300000, $import->currencyToDIField('$100.300.000'));
        $this->assertEquals(1234.86, $import->currencyToDIField('$1,234.86'));
    }

    public function testImportValues()
    {
        $import = new DisasterImport(null, '', '');
        $this->assertEquals(-1, $import->importValueFromArray('EffectPeopleAffected', ['Hubo', ''], [0, 1]));
        $this->assertEquals(-1, $import->importValueFromArray('EffectPeopleAffected', ['', 'hubo'], [0, 1]));
    }
}
