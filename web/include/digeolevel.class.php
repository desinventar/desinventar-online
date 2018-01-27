<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
namespace DesInventar\Legacy;

use \Pdo;
use \DIRecord;

class DIGeoLevel extends DIRecord
{
    protected static $def = array(
        'GeoLevelId' => array('type' => 'INTEGER', 'pk' => 1),
        'LangIsoCode' => array('type' => 'VARCHAR', 'size' => 3, 'pk' => 1),
        'RegionId' => array('type' => 'VARCHAR', 'size' => 50),
        'GeoLevelName' => array('type' => 'VARCHAR', 'size' => 50, 'default' => '---'),
        'GeoLevelDesc' => array('type' => 'TEXT'),
        'GeoLevelActive' => array('type' => 'INTEGER', 'default' => 0),
        'RecordCreation' => array('type' => 'DATETIME'),
        'RecordSync' => array('type' => 'DATETIME'),
        'RecordUpdate' => array('type' => 'DATETIME')
    );
    public function __construct($prmSession)
    {
        $this->sTableName   = "GeoLevel";
        $this->sPermPrefix  = "GEOLEVEL";
        parent::__construct($prmSession);
        $this->set('GeoLevelActive', 1);

        $num_args = func_num_args();
        if ($num_args >= 2) {
            $prmGeoLevelId = func_get_arg(1);
            $this->set('GeoLevelId', $prmGeoLevelId);
            if ($num_args >= 3) {
                $prmLangIsoCode = func_get_arg(2);
                $this->set('LangIsoCode', $prmLangIsoCode);
            }
            if ($num_args >= 4) {
                $prmGeoLevelName = func_get_arg(3);
                $this->set('GeoLevelName', $prmGeoLevelName);
            }
            $this->load();
        }
    }

    public function getMaxGeoLevel()
    {
        $iMaxVal = -1;
        $sQuery = trim('
            SELECT GeoLevelId
            FROM GeoLevel
            WHERE LangIsoCode="' . $this->get('LangIsoCode') . '"
            ORDER BY GeoLevelId
        ');
        foreach ($this->q->dreg->query($sQuery, PDO::FETCH_ASSOC) as $row) {
            $iMaxVal = $row['GeoLevelId'];
        }
        return $iMaxVal;
    }

    public function validateCreate($bStrict)
    {
        $iReturn = 1;
        $iReturn = $this->validateNotNull(-31, 'GeoLevelId');
        if ($iReturn > 0) {
            $iReturn = $this->validatePrimaryKey(-32);
        }
        return $iReturn;
    }

    public function validateUpdate($bStrict)
    {
        $iReturn = parent::validateUpdate($bStrict);
        $iReturn = $this->validateNotNull(-33, 'GeoLevelName');
        if ($iReturn > 0) {
            $iReturn = $this->validateUnique(-34, 'GeoLevelName', true);
        }
        $this->status->status = $iReturn;
        return $iReturn;
    }

    public function importFromCSV($cols, $values)
    {
        $iReturn = parent::importFromCSV($cols, $values);
        $this->set('GeoLevelId', $values[0]);
        $this->set('GeoLevelName', $values[1]);
        if ($iReturn > 0) {
            $this->status->status = $iReturn;
        }
        return $iReturn;
    }
}
