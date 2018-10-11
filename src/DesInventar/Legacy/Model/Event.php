<?php
/*
 * DesInventar - http://www.desinventar.org
 * (c) 1998-2015 Corporacion OSSO
 */

namespace DesInventar\Legacy\Model;

use DesInventar\Common\Util;

use \PDO;

class Event extends Record
{
    const ERR_NO_ERROR = 1;
    const ERR_UNKNOWN_ERROR = -1;

    protected static $def = array(
        'EventId' => array('type' => 'VARCHAR', 'size' => 50, 'pk' => 1),
        'LangIsoCode' => array('type' => 'VARCHAR', 'size' => 3, 'pk' => 1),
        'RegionId' => array('type' => 'VARCHAR', 'size' => 50),
        'EventName' => array('type' => 'VARCHAR', 'size' => 50),
        'EventDesc' => array('type' => 'TEXT'),
        'EventActive' => array('type' => 'INTEGER', 'default' => 1),
        'EventPredefined' => array('type' => 'INTEGER', 'default' => 0),
        'EventRGBColor' => array('type' => 'VARCHAR', 'size' => 10),
        'EventKeyWords' => array('type' => 'TEXT'),
        'RecordCreation' => array('type' => 'DATETIME'),
        'RecordSync' => array('type' => 'DATETIME'),
        'RecordUpdate' => array('type' => 'DATETIME')
    );
    public function __construct($prmSession)
    {
        $this->sTableName   = "Event";
        $this->sPermPrefix  = "EVENT";
        parent::__construct($prmSession);
        $this->set("EventPredefined", 0);
        $this->set("EventActive", 1);
        $util = new Util();
        $this->set("EventId", $util->uuid4());
        $num_args = func_num_args();
        if ($num_args >= 2) {
            $prmEventId = func_get_arg(1);
            $this->set('EventId', $prmEventId);
            $this->load();
            if ($num_args >= 3) {
                $prmEventName = func_get_arg(2);
                $this->set('EventName', $prmEventName);
            }
        }
    } // __construct

    public static function existByName($session, $prmEventName)
    {
        $answer = false;
        $sQuery = 'SELECT * FROM Event WHERE UPPER(EventName)=UPPER(:event_name) OR UPPER(EventId)=UPPER(:event_name)';
        $sth = $session->q->dreg->prepare($sQuery);
        $sth->bindParam(':event_name', $prmEventName, PDO::PARAM_STR);
        $sth->execute();
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $answer = $row['EventId'];
        }
        return $answer;
    }

    public static function getIdByName($session, $prmEventName)
    {
        $EventId = '';
        $sQuery = "SELECT * FROM Event " .
          " WHERE (EventId       LIKE '"  . $prmEventName . "' OR " .
          "        EventName     LIKE '"  . $prmEventName . "' OR " .
          "        EventKeyWords LIKE '%" . $prmEventName . ";%')";
        foreach ($session->q->dreg->query($sQuery) as $row) {
            $EventId = $row['EventId'];
        } // foreach
        return $EventId;
    } // function

    public static function loadByName($session, $prmEventName)
    {
        $EventId = self::getIdByName($session, $prmEventName);
        $e = new self($session, $prmEventName);
        return $e;
    }

    public function getDeleteQuery()
    {
        $sQuery = "UPDATE " . $this->getTableName() . " SET EventActive=0" .
          " WHERE " . $this->getWhereSubQuery();
        return $sQuery;
    }

    public function validateCreate($bStrict)
    {
        $iReturn = 1;
        $iReturn = $this->validateNotNull(-11, 'EventId');
        if ($iReturn > 0) {
            $iReturn = $this->validatePrimaryKey(-12);
        }
        return $iReturn;
    }

    public function validateUpdate($bStrict)
    {
        $iReturn = self::ERR_NO_ERROR;
        $iReturn = $this->validateNotNull(-13, 'EventName');
        if ($iReturn > 0) {
            $iReturn = $this->validateUnique(-14, 'EventName', true);
            if ($iReturn > 0) {
                if ($this->get('EventActive') == 0) {
                    $iReturn = $this->validateNoDatacards(-15);
                }
            }
        }
        return $iReturn;
    }

    public function validateNoDatacards($ErrCode)
    {
        $iReturn = self::ERR_NO_ERROR;
        $Count = 0;
        $Query = "SELECT COUNT(DisasterId) AS COUNT FROM Disaster WHERE EventId='" . $this->get('EventId') . "'";
        foreach ($this->q->dreg->query($Query) as $row) {
            $Count = $row['COUNT'];
        }
        if ($Count > 0) {
            $iReturn = $ErrCode;
        }
        return $iReturn;
    }

    public function validateDelete($bStrict)
    {
        $iReturn = self::ERR_NO_ERROR;
        $iReturn = $this->validateNoDatacards(-15);
        return $iReturn;
    }
}
