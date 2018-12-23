<?php
namespace DesInventar\Legacy\Model;

use DesInventar\Common\Util;

class Cause extends Record
{
    const ERR_NO_ERROR = 1;
    const ERR_UNKNOWN_ERROR = -1;

    protected static $def = array(
        'CauseId' => array('type' => 'VARCHAR', 'size' => 50, 'pk' => 1),
        'LangIsoCode' => array('type' => 'VARCHAR', 'size' => 3, 'pk' => 1),
        'RegionId' => array('type' => 'VARCHAR', 'size' => 50),
        'CauseName' => array('type' => 'VARCHAR', 'size' => 50),
        'CauseDesc' => array('type' => 'TEXT'),
        'CauseActive' => array('type' => 'INTEGER', 'default' => 1),
        'CausePredefined' => array('type' => 'INTEGER', 'default' => 0),
        'CauseRGBColor' => array('type' => 'VARCHAR', 'size' => 10),
        'CauseKeyWords' => array('type' => 'TEXT'),
        'RecordCreation' => array('type' => 'DATETIME'),
        'RecordSync' => array('type' => 'DATETIME'),
        'RecordUpdate' => array('type' => 'DATETIME')
    );
    public function __construct($prmSession)
    {
        $this->sTableName   = "Cause";
        $this->sPermPrefix  = "CAUSE";
        parent::__construct($prmSession);
        $this->set("CausePredefined", 0);
        $this->set("CauseActive", 1);
        $util = new Util();
        $this->set("CauseId", $util->uuid4());

        $num_args = func_num_args();
        if ($num_args >= 2) {
            $prmCauseId = func_get_arg(1);
            $this->set('CauseId', $prmCauseId);
            $this->load();
            if ($num_args >= 3) {
                $prmCauseName = func_get_arg(2);
                $this->set('CauseName', $prmCauseName);
            }
        }
    }

    public static function getIdByName($session, $prmCauseName)
    {
        $CauseId = '';
        $sQuery = "SELECT * FROM Cause " .
          " WHERE (CauseId       LIKE '" . $prmCauseName . "' OR " .
          "        CauseName     LIKE '" . $prmCauseName . "' OR " .
          "        CauseKeyWords LIKE '%" . $prmCauseName . ";%')";
        foreach ($session->q->dreg->query($sQuery) as $row) {
            $CauseId = $row['CauseId'];
        }
        return $CauseId;
    }

    public static function loadByName($session, $prmCauseName)
    {
        $c = new self($session, $prmCauseName);
        return $c;
    }

    public function getDeleteQuery()
    {
        $sQuery = "UPDATE " . $this->getTableName() . " SET CauseActive=0" .
          " WHERE " . $this->getWhereSubQuery();
        return $sQuery;
    }

    public function validateCreate($bStrict)
    {
        $iReturn = self::ERR_NO_ERROR;
        $iReturn = $this->validateNotNull(-21, 'CauseId');
        if ($iReturn > 0) {
            $iReturn = $this->validatePrimaryKey(-22);
        }
        return $iReturn;
    }

    public function validateNoDatacards($ErrCode)
    {
        $iReturn = self::ERR_NO_ERROR;
        $Count = 0;
        $Query = "SELECT COUNT(DisasterId) AS COUNT FROM Disaster WHERE CauseId='" . $this->get('CauseId') . "'";
        foreach ($this->q->dreg->query($Query) as $row) {
            $Count = $row['COUNT'];
        }
        if ($Count > 0) {
            $iReturn = $ErrCode;
        }
        return $iReturn;
    }

    public function validateUpdate($bStrict)
    {
        $iReturn = parent::validateUpdate($bStrict);
        $iReturn = $this->validateNotNull(-23, 'CauseName');
        if ($iReturn > 0) {
            $iReturn = $this->validateUnique(-24, 'CauseName', true);
            if ($iReturn > 0) {
                if ($this->get('CauseActive') == 0) {
                    $iReturn = $this->validateNoDatacards(-25);
                }
            }
        }
        return $iReturn;
    }

    public function validateDelete($bStrict)
    {
        $iReturn = self::ERR_NO_ERROR;
        $iReturn = $this->validateNoDatacards(-25);
        return $iReturn;
    }
}
