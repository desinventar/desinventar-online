<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/
require_once('query.class.php');
require_once('didisaster.class.php');

class DIImport {
	public function __construct($prmSessionId) {
		$this->us = $prmSessionId;
		$this->q = new Query($this->us->sRegionId);
	}
	
	public function validateFromCSV($FileName, $ObjectType) {
		return $this->importFromCSV($FileName, $ObjectType, false);
	}
		
	public function importFromCSV($FileName, $ObjectType, $doImport = true) {
		$FLogName = '/tmp/di8import_' . $this->us->sSessionId . '.csv';
		$FLogName = '/tmp/di8import.csv';
		$cols = array();
		$flog = fopen($FLogName,'w');
		$fh = fopen($FileName, 'r');
		// Version Line
		$values = fgetcsv($fh, 1000, ',');
		// Column Header Line
		$values = fgetcsv($fh, 1000, ',');
		$rowCount = 2;
		while (! feof($fh) ) {
			$values = fgetcsv($fh, 1000, ',');
			if (count($values) > 1) {
				switch($ObjectType) {
					case DI_GEOGRAPHY:
						$o = new DIGeography($this->us);
						$r = $o->importFromCSV($cols, $values);
						$o->insert();
					break;
					case DI_EVENT:
						$o = new DIEvent($this->us);
						$r = $o->importFromCSV($cols, $values);
						//$o->insert();
					case DI_DISASTER:
					break;				
				}
				//print count($values) . "\n";
				//$this->importDisasterRecord($values, $doImport);
			}
		} //while
		fclose($fh);
		fclose($flog);
		return array('Status' => 1,
		             'FileName' => $FLogName);
	} //function

	public function importDisasterRecord($a, $doImport = true) {
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
		$o = new DIDisaster($this->us);
		$o->set('EventId', 'OTHER');
		$o->set('CauseId', 'UNKNOWN');
		foreach($DisasterImport as $Index => $Field) {
			$o->set($Field, $a[$Index]);
		}
		
		// Validation Settings
		$o->set('RecordStatus', 'PUBLISHED');
		if ($o->get('DisasterSource') == '') {
			$o->set('RecordStatus', 'DRAFT');
		}
		$rowCount++;
		
		$g = new DIGeography($this->us);
		$DisasterGeographyCode = $o->get('GeographyId');
		$o->set('GeographyId', $g->getIdByCode($DisasterGeographyCode));
		
		$e = new DIEvent($this->us);
		$o->set('EventId', $e->getIdByName($o->get('EventId')));
		
		$c = new DICause($this->us);
		$o->set('CauseId', $c->getIdByName($o->get('CauseId')));
		
		//2009-07-25 Save fechapor/fechafec in EffectNotes
		$o->set('EffectNotes', 
			$o->get('EffectNotes') . ' ' .
			'(DI6Author : ' . $this->get('RecordAuthor') . ' ' .
			'DI6Date : ' . $this->get('RecordCreation') . ')'
			
		);
		$this->set('RecordAuthor'  , $us->UserId);
		$this->set('RecordCreation', gmdate('c'));
		
		$bInsert = ($o->validateCreate() > 0);
		if ($bInsert) {
			if ($doImport) { $Result = $o->insert(); }
			else { $Result = $o->validateCreate(); }
		} else {
			if ($doImport) { $Result = $o->update(); }
			else { $Result = $o->validateUpdate(); }
		}
		if ($Result > 0) {
			$e = new DIEEData($this->us);
			$e->set('DisasterId', $o->get('DisasterId'));
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
				$sErrorMsg .= $o->get('DisasterSerial') . ' EventId Not Found ' . $o->get('EventId') . ',,';
				break;						
			default:
				$sErrorMsg .= 'Unknown Error' . ',,';
				break;
			} //switch
			fputs($flog, $sErrorMsg . "\n");
		}  //if
	}
} //class

</script>
