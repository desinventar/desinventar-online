<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
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
			if ($num_args >= 3) {
				$prmEventName = func_get_arg(1);
				$prmEventDesc = func_get_arg(2);
				$this->set('EventName', $prmEventName);
				$this->set('EventDesc', $prmEventDesc);
				$this->getIdByName($this->get('EventName'));
			}
			$this->load();
		}
	} // __construct
	
	public function getIdByName($prmEventName) {
		$EventId = '';
		$sQuery = "SELECT * FROM " . $this->getTableName() .
		  " WHERE EventName LIKE '" . $prmEventName . "'";
		foreach($this->q->dreg->query($sQuery) as $row) {
			// Local Event Found
			$EventId = $row['EventId'];
			$this->set('EventId'          , $EventId);
			$this->set('EventPredefined'  , $row['EventPredefined']);
		} // foreach
		
		if ($EventId == '') {
			$EventId = $prmEventName;
		}
		return $EventId;
	} // function
	
	public function getDeleteQuery() {
		$sQuery = "UPDATE " . $this->getTableName() . " SET EventActive=0" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}

	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -11, 'EventId');
		$iReturn = $this->validatePrimaryKey($iReturn,  -12);
		return $iReturn;
	}

	public function validateNoDatacards($curReturn, $ErrCode) {
		$iReturn = $curReturn;
		if ($iReturn > 0) {
			$Count = 0;
			$Query = "SELECT COUNT(DisasterId) AS COUNT FROM Disaster WHERE EventId='" . $this->get('EventId') . "'";
			foreach($this->q->dreg->query($Query) as $row) {
				$Count = $row['COUNT'];
			}
			if ($Count > 0) {
				$iReturn = $ErrCode;
			}
		}
		return $iReturn;
	}

	public function validateUpdate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -13, 'EventName');
		$iReturn = $this->validateUnique($iReturn, -14, 'EventName', true);
		if ($this->get('EventActive') == 0) {
			$iReturn = $this->validateNoDatacards($iReturn, -15);
		}
		return $iReturn;
	}
	
	public function validateDelete() {
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNoDatacards($iReturn, -15);
		return $iReturn;
	}

	public function importFromCSV($cols, $values) {
		$oReturn = parent::importFromCSV($cols, $values);
		$iReturn = ERR_NO_ERROR;
		$this->set('EventName',  $values[1]);
		$this->set('EventDesc',  $values[2]);
		
		$this->getIdByName($this->get('EventName'));
		if ( (count($oReturn['Error']) > 0) || (count($oReturn['Warning']) > 0) ) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		$oReturn['Status'] = $iReturn;
		return $oReturn;
	} //function
}

</script>
