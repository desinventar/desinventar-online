<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

class DIDisaster extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Disaster";
		$this->sPermPrefix  = "DISASTER";
		$this->sFieldKeyDef = "DisasterId/STRING";
		$this->sFieldDef    = "RegionId/STRING," .
		                      "DisasterSerial/STRING," .
		                      "DisasterBeginTime/STRING," .
		                      "GeographyId/STRING," .
		                      "DisasterSiteNotes/STRING," .
		                      "DisasterLatitude/DOUBLE," .
		                      "DisasterLongitude/DOUBLE," .
		                      "DisasterSource/STRING," .
		                      
		                      "RecordStatus/STRING," .
		                      "RecordAuthor/STRING," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," .
		                      "RecordUpdate/DATETIME," .
		                      
		                      "EventId/STRING," .
		                      "EventNotes/STRING," .
		                      "EventDuration/INTEGER," .
		                      "EventMagnitude/STRING," .
		                      
		                      "CauseId/STRING," .
		                      "CauseNotes/STRING";
		$this->sEffectDef    ="EffectPeopleDead/INTEGER," .
		                      "EffectPeopleMissing/INTEGER," .
		                      "EffectPeopleInjured/INTEGER," .
		                      "EffectPeopleHarmed/INTEGER," .
		                      "EffectPeopleAffected/INTEGER," .
		                      "EffectPeopleEvacuated/INTEGER," .
		                      "EffectPeopleRelocated/INTEGER," .		                      
		                      "EffectHousesDestroyed/INTEGER," .
		                      "EffectHousesAffected/INTEGER," .
		                      
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
		$this->sFieldQDef =   "EffectPeopleDeadQ/INTEGER," .
		                      "EffectPeopleMissingQ/INTEGER," .
		                      "EffectPeopleInjuredQ/INTEGER," .
		                      "EffectPeopleHarmedQ/INTEGER," .
		                      "EffectPeopleAffectedQ/INTEGER," .
		                      "EffectPeopleEvacuatedQ/INTEGER," .
		                      "EffectPeopleRelocatedQ/INTEGER," .		                      
		                      "EffectHousesDestroyedQ/INTEGER," .
		                      "EffectHousesAffectedQ/INTEGER";
		$this->sFieldDef .= ',' . $this->sEffectDef;	
		$this->sFieldDef .= ',' . $this->sFieldQDef;
		parent::__construct($prmSession);
		$this->set("EventPredefined", 0);
		$this->set("EventActive", 1);
		$this->set('DisasterId', uuid());
		$this->set('RecordStatus', 'PUBLISHED');

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmDisasterId = func_get_arg(1);
			$this->set('DisasterId', $prmDisasterId);
			$this->load();
		}
	} //__construct

	public function getDeleteQuery() {
		$sQuery = "UPDATE " . $this->getTableName() . " SET RecordStatus='DELETED'" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}
	
	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull(-51, 'DisasterId');
		$iReturn = $this->validatePrimaryKey(-52);
		return $iReturn;
	}
	
	public function validateUpdate() {
		$iReturn = parent::validateUpdate();
		$iReturn = $this->validateNotNull(-53, 'DisasterSerial');
		$iReturn = $this->validateUnique(-54, 'DisasterSerial', WARNING);
		$iReturn = $this->validateNotNull(-55, 'DisasterBeginTime');
		// Warning
		$iReturn = $this->validateNotNull(-56, 'DisasterSource',WARNING);
		$iReturn = $this->validateNotNull(-57, 'RecordStatus');
		$iReturn = $this->validateRef(-58, 'GeographyId', 'Geography', 'GeographyId');
		$iReturn = $this->validateRef(-59, 'EventId', 'Event', 'EventId');
		$iReturn = $this->validateRef(-60, 'CauseId', 'Cause', 'CauseId');
		// Warning
		$iReturn = $this->validateEffects(WARNING);
		
		$iReturn = ERR_NO_ERROR;
		if ($this->status->hasError()) {
			$iReturn = ERR_UNKNOWN_ERROR;
		} elseif ($this->status->hasWarning()) {
			$iReturn = 0;
		}
		return $iReturn;
	}
	
	public function validateEffects($isWarning=false) {
		$bFound = false;
		$iReturn = ERR_NO_ERROR;
		foreach (split(',',$this->sEffectDef) as $sField) {
			$oItem = split('/', $sField);
			$sFieldName  = $oItem[0];
			$sFieldType  = $oItem[1];
			switch($sFieldType) {
				case 'STRING':
					$bFound = true;
				break;
				case 'INTEGER':
					if ( ($this->get($sFieldName) > 0) || ($this->get($sFieldName) == -1) ) {
						$bFound = true;
					}
				break;
				case 'DOUBLE':
					if ($this->get($sFieldName) > 0) {
						$bFound = true;
					}
				break;
			} //switch
		} //foreach
		if ($bFound == false) {
			$iReturn = -61;
			$this->status->addMsg($iReturn, ' Datacard without effects', $isWarning);
		}
		return $iReturn;
	}
	
	public function update($withValidate = true) {
		$iReturn = ERR_NO_ERROR;
		// Calculate Values of Q Fields...
		foreach (split(',',$this->sFieldQDef) as $sFieldQ) {
			$oItem = split('/', $sFieldQ);
			$sFieldQName = $oItem[0];
			$sFieldName  = substr($sFieldQName, 0, -1);
			$sFieldType  = $oItem[1];
			$this->set($sFieldQName, $this->get($sFieldName));
			if ($this->get($sFieldQName) < 0) { $this->set($sFieldQName, 0); }
		}
		// Update Record
		$iReturn = parent::update($withValidate);
		return $iReturn;
	} //update

	public function importFromCSV($cols, $values) {
		$iReturn = parent::importFromCSV($cols, $values);

		$DisasterImport = array(0 => 'DisasterId', 
		                        1 => 'DisasterSerial',
		                        2 => 'DisasterBeginTime',
		                        3 => 'GeographyId',
			                    4 => 'DisasterSiteNotes',
			                    5 => 'DisasterSource',
			                    6 => 'DisasterLongitude',
			                    7 => 'DisasterLatitude',
			                    8 => 'RecordAuthor',
			                    9 => 'RecordCreation',
			                    10 => 'RecordStatus',
			                    11 => 'EventId',
			                    12 => 'EventDuration',
			                    13 => 'EventMagnitude',
			                    14 => 'EventNotes',
			                    15 => 'CauseId',
			                    16 => 'CauseNotes',
			                    // Effects on People
			                    17 => 'EffectPeopleDead',
			                    18 => 'EffectPeopleMissing',
			                    19 => 'EffectPeopleInjured',
			                    20 => 'EffectPeopleHarmed',
			                    21 => 'EffectPeopleAffected',
			                    22 => 'EffectPeopleEvacuated',
			                    23 => 'EffectPeopleRelocated',
			                    // Effects on Houses
			                    24 => 'EffectHousesDestroyed',
			                    25 => 'EffectHousesAffected',
			                    // Effects General
			                    26 => 'EffectLossesValueLocal',
			                    27 => 'EffectLossesValueUSD',
			                    28 => 'EffectRoads',
			                    29 => 'EffectFarmingAndForest',
			                    30 => 'EffectLiveStock',
			                    31 => 'EffectEducationCenters',
			                    32 => 'EffectMedicalCenters',
			                    // Other Losses
			                    33 => 'EffectOtherLosses',
			                    34 => 'EffectNotes',
			                    // Sectors Affected
			                    35 => 'SectorTransport',
			                    36 => 'SectorCommunications',
			                    37 => 'SectorRelief',
			                    38 => 'SectorAgricultural',
			                    39 => 'SectorWaterSupply',
			                    40 => 'SectorSewerage',
			                    41 => 'SectorEducation',
			                    42 => 'SectorPower',
			                    43 => 'SectorIndustry',
			                    44 => 'SectorHealth',
			                    45 => 'SectorOther'
						   );
		$this->set('EventId', 'OTHER');
		$this->set('CauseId', 'UNKNOWN');
		foreach($DisasterImport as $Index => $Field) {
			$this->set($Field, $values[$Index]);
		}
		
		// Validation Settings
		$this->set('RecordStatus', 'PUBLISHED');
		if ($this->get('DisasterSource') == '') {
			$this->set('RecordStatus', 'DRAFT');
		}
		
		$DI6GeographyCode = $this->get('GeographyId');
		$GeographyId = DIGeography::getIdByCode($this->session, $DI6GeographyCode);
		if ($GeographyId == '') {
			$GeographyId = $DI6GeographyCode;
		}
		$this->set('GeographyId', $GeographyId);
		
		$DI6EventId = $this->get('EventId');
		$EventId = DIEvent::getIdByName($this->session, $DI6EventId);
		if ($EventId == '') {
			$EventId = $DI6EventId;
		}
		$this->set('EventId', $EventId);
		
		$DI6CauseId = $this->get('CauseId');
		$CauseId = DICause::getIdByName($this->session, $DI6CauseId);
		if ($CauseId == '') {
			$CauseId = $DI6CauseId;
		}
		$this->set('CauseId', $CauseId);
		
		//2009-07-25 Save fechapor/fechafec in EffectNotes
		$this->set('EffectNotes', 
			$this->get('EffectNotes') . ' ' .
			'(DI6Author : ' . $this->get('RecordAuthor') . ' ' .
			'DI6Date : ' . $this->get('RecordCreation') . ')'
			
		);
		$this->set('RecordAuthor'  , $this->session->UserId);
		$this->set('RecordCreation', gmdate('c'));
		
		return $iReturn;
	} //function

	public static function existId($prmSession, $prmDisasterId) {
		$iReturn = ERR_UNKNOWN_ERROR;
		$bFound = 0;
		$Query= "SELECT * FROM Disaster WHERE DisasterId='" . $prmDisasterId . "'";
		foreach($prmSession->q->dreg->query($Query) as $row) {
			$iReturn = ERR_NO_ERROR;
		}
		return $iReturn;
	}
} //class

</script>
