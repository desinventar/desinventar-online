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
		$this->set("LangIsoCode", $this->q->getDBInfoValue('I18NFirstLang'));
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
				$this->setIdByName($this->get('EventName'));
			}
		}
	} // __construct
	
	public function setIdByName($prmEventName) {
		$iReturn = 0;
		$sQuery = "SELECT * FROM " . $this->getTableName() .
		  " WHERE EventName='" . $prmEventName . "'";
		if ($result = $this->q->dreg->query($sQuery)) {
			if ($result->rowCount()>0) {
				// Local Event Found
				while ($row = $result->fetch_object()) {
					$this->set('EventId', $row->EventId);
					$this->set('EventPreDefined', $row->EventPreDefined);
					$this->set('EventCreationDate', $row->EventCreationDate);
				} // while
			} else {
				// Search PreDefined Event
				$sQuery = "SELECT * FROM DI_Event " . 
				  " WHERE EventLangCode='" . $this->oSession->sRegionLangCode . "'" .
				  "   AND (EventLocalName='" . $this->get('EventName') . "'" .
				  "        OR EventDI6Name='" . $this->get('EventName') . "')";
				if ($result = $this->q->base->query($sQuery)) {
					while ($row = $result->fetch_object()) {
						$this->set('EventId', $row->EventId);
						$this->set('EventName', $row->EventName);
						$this->set('EventDesc', $row->EventDesc);
						$this->set('EventPreDefined', 1);
						$this->set('EventCreationDate', $row->EventCreationDate);
					}
				}
			}
		}
		return $iReturn;
	} // function
	
	public function getDeleteQuery() {
		$sQuery = "UPDATE " . $this->getTableName() . " SET EventActive=0" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}

	public function validateUpdate() {
		$iReturn = 1;
		if (! $this->q->isvalidObjectName($this->get('EventId'), $this->get('EventName'), DI_EVENT)) {
			$iReturn = -11; // Invalid/Duplicated Event Name
		}
		return $iReturn;
	}
}
