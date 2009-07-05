<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/
require_once('query.class.php');
require_once('didisaster.class.php');

class DIImport {
	public function __construct($prmSessionId, $prmRegionId) {
		$this->RegionId = $prmRegionId;
		$this->us = $prmSessionId;
		$this->q = new Query($prmRegionId);
	}
	
	public function importFromCSV($FileName) {
		$DisasterImport = array(0 => 'DisasterId', 
		                        1 => 'DisasterSerial',
		                        2 => 'DisasterBeginTime',
		                        3 => 'DisasterGeographyId',
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
		$fh = fopen($FileName, 'r');
		// Version Line
		$a = fgetcsv($fh, 1000, ',');
		// Column Header Line
		$a = fgetcsv($fh, 1000, ',');
		print "<pre>";
		$i = 0;
		while (! feof($fh) ) {
			$a = fgetcsv($fh, 1000, ',');
			if (count($a) > 1) {
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
				//print $o->getInsertQuery() . "<br />\n";
				//print $o->getUpdateQuery() . "<br />\n";
				$i++;
				$g = new DIGeography($this->us);
				$o->set('DisasterGeographyId', $g->getIdByCode($o->get('DisasterGeographyId')));
				
				$e = new DIEvent($this->us);
				$o->set('EventId', $e->getIdByName($o->get('EventId')));
				
				$c = new DICause($this->us);
				$o->set('CauseId', $c->getIdByName($o->get('CauseId')));
				
				print $i . " " . count($a) . " " . $o->get('DisasterId') . " " . 
				    $o->validateCreate() . " " . $o->validateUpdate() . " " . 
				    $o->get('EventId') . "<br />";
			}
		}
		fclose($fh);
		print "</pre>";
	}
} //class

</script>

