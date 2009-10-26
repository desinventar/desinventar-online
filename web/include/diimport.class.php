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
					case DI_EVENT:
						$o = new DIEvent($this->us);
						$r = $o->importFromCSV($cols, $values);
						$o->insert();
					break;
					case DI_CAUSE:
						$o = new DICause($this->us);
						$r = $o->importFromCSV($cols, $values);
						$o->insert();
					break;
					case DI_GEOGRAPHY:
						$o = new DIGeography($this->us);
						$r = $o->importFromCSV($cols, $values);
						$o->insert();
					break;
					case DI_DISASTER:
						$o = new DIDisaster($this->us);
						$r = $o->importFromCSV($cols, $values);
						//$o->insert();
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
} //class

</script>
