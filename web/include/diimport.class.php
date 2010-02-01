<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
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
		$values = fgetcsv($fh, 1000, ',');
		// Column Header Line
		$values = fgetcsv($fh, 1000, ',');
		$rowCount = 2;
		while ( (! feof($fh) ) && ($rowCount < $maxLines) ) {
			$values = fgetcsv($fh, 1000, ',');
			if (count($values) > 1) {
				switch($ObjectType) {
					case DI_EVENT:
						$o = new DIEvent($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ( ($r['Status'] > 0) && ($o->get('EventPreDefined')==0) ) {
							$o->insert();
						}
					break;
					case DI_CAUSE:
						$o = new DICause($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ( ($r['Status'] > 0) && ($o->get('CausePreDefined')==0) ) {
							$o->insert();
						}
					break;
					case DI_GEOLEVEL:
						$o = new DIGeoLevel($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ($r['Status'] > 0) {
							$o->insert();
						}
					break;
					case DI_GEOGRAPHY:
						$o = new DIGeography($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ($r['Status'] > 0) {
							$answer = $o->insert();
						}
					break;
					case DI_DISASTER:
						$o = new DIDisaster($this->us);
						$iReturn = $o->importFromCSV($cols, $values);
						if ($iReturn > 0) {
							$iReturn = DIDisaster::existId($this->us, $o->get('DisasterId'));
							if ($iReturn > 0) {
								if ($doImport) {
									$iReturn = $o->validateCreate();
									if ($iReturn < 0) {
										fb('insert ' . $rowCount . ' ' . $iReturn);
										print_r($o->status->error);
									}
									$iReturn = $o->insert();
								} else {
									$iReturn = $o->validateCreate();
								}
							} else {
								if ($doImport) {
									$iReturn = $o->update();
									if ($iReturn < 0) {
										fb('update ' . $rowCount . ' ' . $iReturn);
										print_r($o->status->error);
									}
								} else {
									$iReturn = $o->validateUpdate();
								}
							}
						}
						if ($iReturn > 0) {
							$e = new DIEEData($this->us);
							$e->set('DisasterId', $o->get('DisasterId'));
							if ($doImport) {
								if ($iReturn > 0) {
									$iReturn = $e->insert();
								} else {
									$iReturn = $e->update();
								}
							}
						}
					break;				
				} // switch
			} // if
			$rowCount++;
		} //while
		fclose($fh);
		fclose($flog);
		return array('Status' => 1,
		             'FileName' => $FLogName);
	} //function
} //class

</script>
