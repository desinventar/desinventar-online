<script language="php">
/*
  DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
require_once('query.class.php');
require_once('didisaster.class.php');

class DIImport {
	public function __construct($prmSessionId) {
		$this->us = $prmSessionId;
		$this->q = new Query($this->us->RegionId);
	}
	
	public function validateFromCSV($FileName, $ObjectType) {
		return $this->importFromCSV($FileName, $ObjectType, false);
	}
		
	public function importFromCSV($FileName, $ObjectType, $doImport = true, $prmMaxLines) {
		$maxLines = 1000000;
		if ($prmMaxLines > 0) {
			$maxLines = $prmMaxLines;
		}
		$FLogName = '/tmp/di8import_' . $this->us->sSessionId . '.csv';
		$FLogName = '/tmp/di8import.csv';
		$cols = array();
		$flog = fopen($FLogName,'w');
		$fh = fopen($FileName, 'r');
		// Version Line
		$values = fgetcsv($fh, 0, ',');
		// Column Header Line
		$values = fgetcsv($fh, 0, ',');
		$rowCount = 2;
		while ( (! feof($fh) ) && ($rowCount < $maxLines) ) {
			$values = fgetcsv($fh, 0, ',');
			$rowCount++;
			if (count($values) > 1) {
				switch($ObjectType) {
					case DI_EVENT:
						$o = new DIEvent($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ( ($r > 0) && ($o->get('EventPredefined')==0) ) {
							$o->insert();
						}
					break;
					case DI_CAUSE:
						$o = new DICause($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ( ($r > 0) && ($o->get('CausePredefined')==0) ) {
							$o->insert();
						}
					break;
					case DI_GEOLEVEL:
						$o = new DIGeoLevel($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ($r > 0) {
							$o->insert();
						}
					break;
					case DI_GEOGRAPHY:
						$o = new DIGeography($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ($r > 0) {
							$answer = $o->insert();
							if ($answer < 0) {
								if ($o->status->hasError()) {
									print $o->get('GeographyCode') . ' ' .
									      reset($o->status->error) . "\n";
								}
							}
						}
					break;
					case DI_DISASTER:
						if ($rowCount % 100 == 0) {
							fb($rowCount . ' ' . count($values));
						}
						/*
						if ( ($rowCount >= 2000) && ($rowCount < 2100) ) {
							fb($rowCount . ' ' . count($values));
						}
						*/
						$o = new DIDisaster($this->us);
						$iReturn = $o->importFromCSV($cols, $values);
						if ($iReturn > 0) {
							$bExistId = DIDisaster::existId($this->us, $o->get('DisasterId'));
							if ($bExistId < 0) {
								// Id doesn't exist, insert record
								$iReturn = $o->validateCreate(false);
							}
							$iReturn = $o->validateUpdate(false);
							if ($iReturn <= 0) {
								$o->status->getMsgList($rowCount, $o->get('DisasterSerial'), ERROR);
								$o->status->getMsgList($rowCount, $o->get('DisasterSerial'), WARNING);
							}
							// DisasterSerial is duplicated but we insert/update anyway
							if ($iReturn == 0) {
								if ( ($o->status->hasWarning(-54)) ||
								     ($o->status->hasWarning(-56)) ||
								     ($o->status->hasWarning(-61))
								   ) {
									// With warnings, insert/update as DRAFT
									$o->set('RecordStatus','DRAFT');
								}
							}
							if ($iReturn >= 0) {
								// insert/update datacard
								if ($doImport) {
									$e = new DIEEData($this->us, $o->get('DisasterId'));
									$e->set('DisasterId', $o->get('DisasterId'));
									if ($bExistId > 0) {
										$o->update(false);
										$e->update();
									} else {
										$o->insert(false);
										$e->insert();
									}
								}
							}
						}
					break;				
				} // switch
			} // if
		} //while
		fclose($fh);
		fclose($flog);
		return array('Status' => 1,
		             'FileName' => $FLogName);
	} //function
} //class

</script>
