<?php

namespace DesInventar\Helpers;

class Dbf
{
    public static function getRecordCount($fileName)
    {
        $dbf = dbase_open($fileName, 0); // read only
        $count = dbase_numrecords($dbf);
        dbase_close($dbf);
        return $count;
    }

    public static function getRecords($fileName, $count)
    {
        $records = [];
        $dbf = dbase_open($fileName, 0); // read only
        for ($i = 1; $i <= $count; $i++) {
            $row = dbase_get_record_with_names($dbf, $i);
            $records[] = $row;
        }
        return $records;
    }
}
