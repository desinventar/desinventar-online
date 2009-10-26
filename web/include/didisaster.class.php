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
		$this->sFieldDef .= ',' . $this->sFieldQDef;
		parent::__construct($prmSession);
		$this->set("EventPredefined", 0);
		$this->set("EventActive", 1);
		$this->set('DisasterId', uuid());

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
		$iReturn = $this->validateNotNull($iReturn, -51, 'DisasterId');
		$iReturn = $this->validatePrimaryKey($iReturn,  -52);
		return $iReturn;
	}
	
	public function validateUpdate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -53, 'DisasterSerial');
		//$iReturn = $this->validateUnique($iReturn,  -54, 'DisasterSerial');
		$iReturn = $this->validateNotNull($iReturn, -55, 'DisasterBeginTime');
		$iReturn = $this->validateNotNull($iReturn, -56, 'DisasterSource');
		$iReturn = $this->validateNotNull($iReturn, -57, 'RecordStatus');
		$iReturn = $this->validateRef($iReturn, -58, 'GeographyId', 'Geography', 'GeographyId');
		$iReturn = $this->validateRef($iReturn, -59, 'EventId', 'Event', 'EventId');
		$iReturn = $this->validateRef($iReturn, -60, 'CauseId', 'Cause', 'CauseId');
		
		//validateEffects ??
		return $iReturn;
	}
	
	public function update($withValidate = true) {
		$iReturn = ERR_NO_ERROR;
		foreach (split(',',$this->sFieldQDef) as $sFieldQ) {
			$oItem = split('/', $sFieldQ);
			$sFieldQName = $oItem[0];
			$sFieldName  = substr($sFieldQName, 0, -1);
			$sFieldType  = $oItem[1];
			$this->set($sFieldQName, $this->get($sFieldName));
			if ($this->get($sFieldQName) < 0) { $this->set($sFieldQName, 0); }
		}
		$iReturn = parent::update($withValidate);
	} //update

	public function importFromCSV($cols, $values) {
		$oReturn = parent::importFromCSV($cols, $values);
		$iReturn = ERR_NO_ERROR;

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
		
		$g = new DIGeography($this->session);
		$DisasterGeographyCode = $this->get('GeographyId');
		$this->set('GeographyId', $g->getIdByCode($DisasterGeographyCode));
		
		$e = new DIEvent($this->session);
		$this->set('EventId', $e->getIdByName($this->get('EventId')));
		
		$c = new DICause($this->session);
		$this->set('CauseId', $c->getIdByName($this->get('CauseId')));
		
		//2009-07-25 Save fechapor/fechafec in EffectNotes
		$this->set('EffectNotes', 
			$this->get('EffectNotes') . ' ' .
			'(DI6Author : ' . $this->get('RecordAuthor') . ' ' .
			'DI6Date : ' . $this->get('RecordCreation') . ')'
			
		);
		$this->set('RecordAuthor'  , $this->session->UserId);
		$this->set('RecordCreation', gmdate('c'));
		
		/*
		$bInsert = ($this->validateCreate() > 0);
		if ($bInsert) {
			if ($doImport) { $Result = $this->insert(); }
			else { $Result = $this->validateCreate(); }
		} else {
			if ($doImport) { $Result = $this->update(); }
			else { $Result = $this->validateUpdate(); }
		}
		if ($Result > 0) {
			$e = new DIEEData($this->us);
			$e->set('DisasterId', $this->get('DisasterId'));
			if ($doImport) {
				if ($bInsert) {
					$e->insert();
				} else {
					$e->update();
				}
			}
		} else {
			// Generate Error Log
			$sErrorMsg = $rowCount . ',' . $Result . ',';
			switch($Result) {
			case -59:
				$sErrorMsg .= $this->get('DisasterSerial') . ' EventId Not Found ' . $this->get('EventId') . ',,';
				break;						
			default:
				$sErrorMsg .= 'Unknown Error' . ',,';
				break;
			} //switch
			fputs($flog, $sErrorMsg . "\n");
		}  //if
		*/
		if ( (count($oReturn['Error']) > 0) || (count($oReturn['Warning']) > 0) ) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		$oReturn['Status'] = $iReturn;
		return $oReturn;
	} //function
} //class

</script>
