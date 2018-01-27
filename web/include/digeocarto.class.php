<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

namespace DesInventar\Legacy;

class DIGeoCarto extends DIRecord
{
    protected static $def = array(
        'GeographyId' => array('type' => 'VARCHAR', 'size' => 100, 'pk' => 1),
        'GeoLevelId' => array('type' => 'INTEGER', 'pk' => 1),
        'LangIsoCode' => array('type' => 'VARCHAR', 'size' => 3, 'pk' => 1),
        'RegionId' => array('type' => 'VARCHAR', 'size' => 50),
        'GeoLevelLayerFile' => array('type' => 'VARCHAR', 'size' => 50),
        'GeoLevelLayerName' => array('type' => 'VARCHAR', 'size' => 50),
        'GeoLevelLayerCode' => array('type' => 'VARCHAR', 'size' => 50),
        'RecordCreation' => array('type' => 'DATETIME'),
        'RecordSync' => array('type' => 'DATETIME'),
        'RecordUpdate' => array('type' => 'DATETIME')
    );
    public function __construct($prmSession)
    {
        $this->sTableName   = "GeoCarto";
        $this->sPermPrefix  = "GEOLEVEL";
        parent::__construct($prmSession);

        $num_args = func_num_args();
        if ($num_args >= 2) {
            $prmGeoLevelId = func_get_arg(1);
            $this->set('GeoLevelId', $prmGeoLevelId);
            if ($num_args >= 3) {
                $prmLangIsoCode = func_get_arg(2);
                $this->set('LangIsoCode', $prmLangIsoCode);
            }
            $this->load();
        }
    }

    public function getDBFFilename()
    {
        $filename = VAR_DIR . '/database/' . $this->RegionId . '/' . $this->get('GeoLevelLayerFile') . '.dbf';
        return $filename;
    }
}
