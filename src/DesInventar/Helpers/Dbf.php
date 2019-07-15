<?php

namespace DesInventar\Helpers;

class Dbf
{
    protected $dbf = null;

    public function __construct($fileName)
    {
        $this->dbf = dbase_open($fileName, 0); // read only
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close()
    {
        if (is_null($this->dbf)) {
            return;
        }
        dbase_close($this->dbf);
        $this->dbf = null;
    }

    public function getRecordCount()
    {
        if (!$this->dbf) {
            return 0;
        }
        return dbase_numrecords($this->dbf);
    }

    public function getFieldsCount()
    {
        if (!$this->dbf) {
            return 0;
        }
        return dbase_numfields($this->dbf);
    }

    public function getHeaders()
    {
        return dbase_get_header_info($this->dbf);
    }

    public function getRecords($count)
    {
        if (!$this->dbf) {
            return [];
        }
        $records = [];
        for ($i = 1; $i <= $count; $i++) {
            $row = dbase_get_record_with_names($this->dbf, $i);
            $records[] = $row;
        }
        return $records;
    }
}
