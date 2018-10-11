<?php

namespace DesInventar\Common;

use PHPUnit\Framework\TestCase;

final class DateTest extends TestCase
{
    public function testDateFunctions()
    {
        $this->assertEquals(Date::isLeapYear(-500), Date::IS_NOT_LEAP_YEAR);
        $this->assertEquals(Date::isLeapYear(2017), Date::IS_NOT_LEAP_YEAR);
        $this->assertEquals(Date::isLeapYear(2018), Date::IS_NOT_LEAP_YEAR);
        $this->assertEquals(Date::isLeapYear(2000), Date::IS_NOT_LEAP_YEAR);
        $this->assertEquals(Date::isLeapYear(2100), Date::IS_NOT_LEAP_YEAR);
        $this->assertEquals(Date::isLeapYear(2004), Date::IS_LEAP_YEAR);
        $this->assertEquals(Date::isLeapYear(2020), Date::IS_LEAP_YEAR);
    }
}
