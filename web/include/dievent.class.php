<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
*/

class DIEvent extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Event";
		$this->sPermPrefix  = "EVENT";
		$this->sFieldKeyDef = "EventId/STRING," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "RegionId/STRING," . 
		                      "EventName/STRING," .
		                      "EventDesc/STRING," .
		                      "EventActive/BOOLEAN," .  
		                      "EventPredefined/BOOLEAN," .
		                      "EventRGBColor/STRING," .
		                      "EventKeyWords/STRING," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," .
		                      "RecordUpdate/DATETIME";
		parent::__construct($prmSession);
		$this->set("EventPredefined", 0);
		$this->set("EventActive", 1);
		$this->set("EventId", uuid());
		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmEventId = func_get_arg(1);
			$this->set('EventId', $prmEventId);
			$this->load();
		}
	} // __construct
	
	public static function getIdByName($session, $prmEventName) {
		$EventId = '';
		$sQuery = "SELECT * FROM Event " .
		  " WHERE (EventName LIKE '" . $prmEventName . "' OR " .
		  "        EventKeyWords LIKE '%" . $prmEventName . ";%')";
		foreach($session->q->dreg->query($sQuery) as $row) {
			$EventId = $row['EventId'];
		} // foreach
		return $EventId;
	} // function

	public static function loadByName($session, $prmEventName) {
		$EventId = self::getIdByName($session, $prmEventName);
		$e = new self($session, $prmEventName);
		return $e;
	} //function
	
	public function getDeleteQuery() {
		$sQuery = "UPDATE " . $this->getTableName() . " SET EventActive=0" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}

	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull(-11, 'EventId');
		if ($iReturn > 0) {
			$iReturn = $this->validatePrimaryKey(-12);
		}
		return $iReturn;
	}

	public function validateUpdate() {
		$oReturn = parent::validateUpdate();
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNotNull(-13, 'EventName');
		if ($iReturn > 0) {
			$iReturn = $this->validateUnique(-14, 'EventName', true);
			if ($iReturn > 0) {
				if ($this->get('EventActive') == 0) {
					$iReturn = $this->validateNoDatacards(-15);
				}
			}
		}
		$oReturn['Status'] = $iReturn;
		return $oReturn;
	}
	
	public function validateNoDatacards($ErrCode) {
		$iReturn = ERR_NO_ERROR;
		$Count = 0;
		$Query = "SELECT COUNT(DisasterId) AS COUNT FROM Disaster WHERE EventId='" . $this->get('EventId') . "'";
		foreach($this->q->dreg->query($Query) as $row) {
			$Count = $row['COUNT'];
		}
		if ($Count > 0) {
			$iReturn = $ErrCode;
		}
		return $iReturn;
	}

	public function validateDelete() {
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNoDatacards(-15);
		return $iReturn;
	}

	public function importFromCSV($cols, $values) {
		$oReturn = parent::importFromCSV($cols, $values);
		$iReturn = $oReturn['Status'];
		if ($iReturn > 0) {
			$this->set('EventName',  $values[1]);
			$this->set('EventDesc',  $values[2]);
			$this->set('EventId', self::getIdByName($this->session, $this->get('EventName')));
			if ( (count($oReturn['Error']) > 0) || (count($oReturn['Warning']) > 0) ) {
				$iReturn = ERR_UNKNOWN_ERROR;
			}
			$oReturn['Status'] = $iReturn;
		}
		return $oReturn;
	} //function
}

</script>
