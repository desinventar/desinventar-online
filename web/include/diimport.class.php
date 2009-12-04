<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
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
					case DI_GEOGRAPHY:
						$o = new DIGeography($this->us);
						$r = $o->importFromCSV($cols, $values);
						if ($r['Status'] > 0) {
							$o->insert();
						}
					break;
					case DI_DISASTER:
						$o = new DIDisaster($this->us);
						$Result = $o->importFromCSV($cols, $values);
						if ($Result > 0) {
							if ($doImport) {
								$oResult = array();
								$oResult['Status'] = ERR_NO_ERROR;
								$oResult['Error'] = array();
								$oResult['Warning'] = array();
								$Result = $o->validateCreate($oResult);
								print_r($oResult);
								$Result = $o->insert();
							} else {
								$Result = $o->validateCreate();
							}
						} else {
							if ($doImport) {
								$Result = $o->update();
							} else {
								$Result = $o->validateUpdate();
							}
						}
						if ($Result > 0) {
							$e = new DIEEData($this->us);
							$e->set('DisasterId', $o->get('DisasterId'));
							if ($doImport) {
								if ($Result > 0) {
									$Result = $e->insert();
								} else {
									$Result = $e->update();
								}
							}
						}
					break;				
				}
			}
		} //while
		fclose($fh);
		fclose($flog);
		return array('Status' => 1,
		             'FileName' => $FLogName);
	} //function
} //class

</script>
