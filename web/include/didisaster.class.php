<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIDisaster extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Disaster";
		$this->sPermPrefix  = "DISASTER";
		$this->sFieldKeyDef = "DisasterId/STRING";
		$this->sFieldDef    = "SyncRecord/DATETIME," .
		                      "DisasterSerial/STRING," .
		                      "DisasterBeginTime/STRING," .
		                      "DisasterGeographyId/STRING," .
		                      "DisasterSiteNotes/STRING," .
		                      "DisasterLatitude/DOUBLE," .
		                      "DisasterLongitude/DOUBLE," .
		                      "DisasterSource/STRING," .
		                      
		                      "RecordStatus/STRING," .
		                      "RecordAuthor/STRING," .
		                      "RecordCreation/DATETIME," .
		                      "RecordLastUpdate/DATETIME," .
		                      
		                      "EventId/STRING," .
		                      "EventNotes/STRING," .
		                      "EventDuration/INTEGER," .
		                      "EventMagnitude/STRING," .
		                      
		                      "CauseId/STRING," .
		                      "CauseNotes/STRING," .
		                      
		                      "EffectPeopleDead/INTEGER," .
		                      "EffectPeopleMissing/INTEGER," .
		                      "EffectPeopleInjured/INTEGER," .
		                      "EffectPeopleHarmed/INTEGER," .
		                      "EffectPeopleAffected/INTEGER," .
		                      "EffectPeopleEvacuated/INTEGER," .
		                      "EffectPeopleRelocated/INTEGER," .		                      
		                      "EffectHousesDestroyed/INTEGER," .
		                      "EffectHousesAffected/INTEGER," .

		                      "EffectPeopleDeadQ/INTEGER," .
		                      "EffectPeopleMissingQ/INTEGER," .
		                      "EffectPeopleInjuredQ/INTEGER," .
		                      "EffectPeopleHarmedQ/INTEGER," .
		                      "EffectPeopleAffectedQ/INTEGER," .
		                      "EffectPeopleEvacuatedQ/INTEGER," .
		                      "EffectPeopleRelocatedQ/INTEGER," .		                      
		                      "EffectHousesDestroyedQ/INTEGER," .
		                      "EffectHousesAffectedQ/INTEGER," .
		                      
		                      "EffectLossesValueLocal/DOUBLE," .
		                      "EffectLossesValueUSD/DOUBLE," .
		                      "EffectRoads/DOUBLE," .
		                      "EffectFarmingAndForest/DOUBLE," .
		                      "EffectLiveStock/INTEGER," .
		                      "EffectEducationCenters/INTEGER," .
		                      "EffectMedicalCenters/INTEGER," .
		                      "EffectOtherLosses/STRING," .
		                      "EffectNotes/STRING," .
		                      
		                      "SectorTransport/INTEGER," .
		                      "SectorCommunications/INTEGER," .
		                      "SectorRelief/INTEGER," .
		                      "SectorAgricultural/INTEGER," .
		                      "SectorWaterSupply/INTEGER," .
		                      "SectorSewerage/INTEGER," .
		                      "SectorEducation/INTEGER," .
		                      "SectorPower/INTEGER," .
		                      "SectorIndustry/INTEGER," .
		                      "SectorHealth/INTEGER," .
		                      "SectorOther/INTEGER"
		parent::__construct($prmSession);
		$this->set("EventPredefined", 0);
		$this->set("EventActive", 1);
		$this->set("LangIsoCode", $this->q->getDBInfoValue('I18NFirstLang'));

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$this->set('EventId', func_get_arg(1));
			if ($num_args >= 3) {
				$this->set('EventName', func_get_arg(1));
				$this->set('EventDesc', func_get_arg(2));
				$this->setIdByName($this->get('EventName'));
			}
		}
	}
	
	public function setIdByName($prmEventName) {
		$iReturn = 0;
		$sQuery = "SELECT * FROM " . $this->getTableName() .
		  " WHERE EventName='" . $prmEventName . "'";
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
				  "   AND (EventLocalName='" . $this->get('EventName') . "'" .
				  "        OR EventDI6Name='" . $this->get('EventName') . "')";
				if ($result = $q->query($sQuery)) {
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
		$sQuery = "UPDATE " . $this->getTableName() . " SET RecordStatus='DELETED'" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}
}
