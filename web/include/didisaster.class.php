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
		                      "SectorOther/INTEGER";
		parent::__construct($prmSession);
		$this->set("EventPredefined", 0);
		$this->set("EventActive", 1);
		$this->set("LangIsoCode", $this->q->getDBInfoValue('I18NFirstLang'));

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmDisasterId = func_get_arg(1);
			$this->set('DisasterId', $prmDisasterId);
		}
	} //__construct
	
	public function getDeleteQuery() {
		$sQuery = "UPDATE " . $this->getTableName() . " SET RecordStatus='DELETED'" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}
}
