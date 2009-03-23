<script language="php">
/*
 ***********************************************
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
 **********************************************
*/

class DIEvent extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Event";
		$this->sPermPrefix  = "EVENT";
		$this->sFieldKeyDef = "EventId/STRING";
		$this->sFieldDef    = "EventLocalName/STRING," .
		                      "EventLocalDesc/STRING," .
		                      "EventActive/BOOLEAN," .  
		                      "EventPreDefined/BOOLEAN," .
		                      "EventCreationDate/DATETIME";
		parent::__construct($prmSession);
		$this->set("EventPreDefined", 0);

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$this->set('EventId', func_get_arg(1));
			if ($num_args >= 3) {
				$this->set('EventLocalName', func_get_arg(1));
				$this->set('EventLocalDesc', func_get_arg(2));
				$this->setIdByLocalName($this->get('EventLocalName'));
			}
		}
	}
	
	public function setIdByLocalName($prmEventLocalName) {
		$iReturn = 0;
		$sQuery = "SELECT * FROM " . $this->getTableName() .
		  " WHERE EventLocalName='" . $prmEventLocalName . "'";
		$q = new Query();
		if ($result = $q->query($sQuery)) {
			if ($result->num_rows>0) {
				// Local Event Found
				while ($row = $result->fetch_object()) {
					$this->set('EventId', $row->EventId);
					$this->set('EventPreDefined', $row->EventPreDefined);
					$this->set('EventCreationDate', $row->EventCreationDate);
				} // while
			} else {
				// Search PreDefined Event
				$sQuery = "SELECT * FROM DIEvent " . 
				  " WHERE EventLangCode='" . $this->oSession->sRegionLangCode . "'" .
				  "   AND (EventLocalName='" . $this->get('EventLocalName') . "'" .
				  "        OR EventDI6Name='" . $this->get('EventLocalName') . "')";
				if ($result = $q->query($sQuery)) {
					while ($row = $result->fetch_object()) {
						$this->set('EventId', $row->EventId);
						$this->set('EventLocalName', $row->EventLocalName);
						$this->set('EventLocalDesc', $row->EventLocalDesc);
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
}
