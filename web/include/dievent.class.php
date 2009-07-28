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
		$this->sFieldDef    = "SyncRecord/DATETIME," .
		                      "EventName/STRING," .
		                      "EventDesc/STRING," .
		                      "EventActive/BOOLEAN," .  
		                      "EventPredefined/BOOLEAN," .
		                      "EventRGBColor/STRING," .
		                      "EventKeyWords/STRING," .
		                      "EventCreationDate/DATETIME," .
		                      "EventLastUpdate/DATETIME";
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
			$this->set('EventCreationDate', $row['EventCreationDate']);
		} // foreach
		
		if ($EventId == '') {
			// Search Predefined Event
			$sQuery = "SELECT * FROM DI_Event WHERE " . 
					  " (EventName LIKE '%" . $prmEventName . "%'" .
					  "  OR EventKeywords LIKE '%" . $prmEventName . "%')";
			foreach ($this->q->base->query($sQuery) as $row) {
				$EventId = $row['EventId'];
				$this->set('EventId'          , $EventId);
				$this->set('EventName'        , $row['EventName']);
				$this->set('EventDesc'        , $row['EventDesc']);
				$this->set('EventPredefined'  , 1);
				$this->set('EventCreationDate', $row['EventCreationDate']);
			} //foreach
		} //if
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

	public function validateUpdate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -13, 'EventName');
		$iReturn = $this->validateUnique($iReturn, -14, 'EventName', true);
		return $iReturn;
	}
}

</script>
