<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

namespace DesInventar\Legacy;

class DIDate
{
    // Return 1 if its a Leap Year, 0 if it's not and -1 if an error occurs
    public static function isLeapYear($prmYear)
    {
        $bReturn = -1; // Error
        if (is_int($prmYear) && $prmYear > 0) {
            // In the Gregorian calendar there is a leap year every year divisible by four
            // except for years which are both divisible by 100 and not divisible by 400.
            if ($prmYear % 4 != 0) {
                $iReturn = 0; // Not Leap Year
            } else {
                $iReturn = 1; // Leap Year
                if ($prmYear % 100 == 0) {
                    $iReturn = 1; // Leap Year
                }
                if ($prmYear % 400 == 0) {
                    $iReturn = 0; // Not Leap Year
                }
            }
        }
        return $iReturn;
    } //isLeapYear()

    public static function getDaysOfMonth($Year, $Month)
    {
        $MDays = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
        $Day = $MDays[(int)$Month];
        // On February, check if Year is leap and add one more day
        if ($Month == 2) {
            if (self::isLeapYear($Year)) {
                $Day++;
            }
        } //if
        return $Day;
    } //getDaysOfMonth()

    public static function getYear($prmDate)
    {
        $Year = trim(substr($prmDate, 0, 4));
        return $Year;
    } //getYear()

    public static function getMonth($prmDate)
    {
        $Month = trim(substr($prmDate, 5, 2));
        return $Month;
    } //getMonth()

    public static function getDay($prmDate)
    {
        $Day = trim(substr($prmDate, 8, 2));
        return $Day;
    } //getDay()

    public static function getWeekOfYear($prmDate)
    {
        $iWeek = date("W", mktime(
            5,
            0,
            0,
            self::getMonth($prmDate),
            self::getDay($prmDate),
            self::getyear($prmDate)
        ));
        return $iWeek;
    } //getWeekOfYear()

    public static function getWeeksOfYear($Year)
    {
        $iWeeks = self::getWeekOfYear($Year . '-12-31');
        if ($iWeeks < 53) {
            $iWeeks = 52;
        }
        return $iWeeks;
    } //getWeeksOfYear()

    public static function padNumber($prmValue, $prmLength)
    {
        $value = $prmValue;
        while (strlen($value) < $prmLength) {
            $value = '0' . $value;
        }
        return $value;
    } //padNumber()

    public static function doCeil($prmValue)
    {
        $ceilDate = $prmValue;
        $aYear = self::getYear($prmValue);
        if ($aYear != '') {
            $ceilDate = sprintf('%04d', $aYear);
            $aMonth = self::getMonth($prmValue);
            if ($aMonth == '') {
                $aMonth = 12;
            }
            $ceilDate .= sprintf('-%02d', $aMonth);

            $aDay = self::getDay($prmValue);
            if ($aDay == '') {
                $aDay = self::getDaysOfMonth($aYear, $aMonth);
            }
            $ceilDate .= sprintf('-%02d', $aDay);
        }
        return $ceilDate;
    }

    public static function now()
    {
         $now = gmdate('c');
         return $now;
    } //now()
} //class
