<?php

namespace UnitTest\General;

use DesInventar\Common\Language;
use PHPUnit\Framework\TestCase;

final class LanguageTest extends TestCase
{
    public function testRemoveSpecialChars()
    {
        $language = new Language();
        $this->assertTrue($language->isValidLanguage('en'));
        $this->assertFalse($language->isValidLanguage('non-existent'));
        $this->assertEquals('es', $language->getLanguageIsoCode('spa', Language::ISO_639_1));
        $this->assertEquals('en', $language->getLanguageIsoCode('non-existent', Language::ISO_639_1));
        $this->assertEquals('fre', $language->getLanguageIsoCode('fr', Language::ISO_639_2));
        $this->assertEquals('eng', $language->getLanguageIsoCode('non-existent', Language::ISO_639_2));
    }
}
