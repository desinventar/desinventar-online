<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIEEField extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "EEField";
		$this->sPermPrefix  = "EEFIELD";
		$this->sFieldKeyDef = "EEFieldId/STRING";
		$this->sFieldDef    = "RegionId/STRING," .
		                      "EEGroupId/STRING," .
		                      "EEFieldLabel/STRING," .
		                      "EEFieldDesc/STRING," .
		                      "EEFieldType/STRING," .
		                      "EEFieldSize/INTEGER," .
		                      "EEFieldOrder/INTEGER," .
		                      "EEFieldStatus/INTEGER," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," .
		                      "RecordUpdate/DATETIME";
		parent::__construct($prmSession);
		$num_args = func_num_args();
		$this->set('EEFieldId', $this->getNextEEFieldId());
		$this->set('EEFieldStatus', 3);
		$this->set('EEFieldType', 'TEXT');
		$this->set('EEFieldSize', 50);
		if ($num_args >= 2) {
			$prmEEFieldId = func_get_arg(1);
			if ($prmEEFieldId != '') {
				$this->set('EEFieldId', $prmEEFieldId);
			}
			$this->load();
		}
	} // __construct

	public function getMaxEEFieldId() {
		$iMaxVal = 0;
		$sQuery = "SELECT COUNT(EEFieldId) AS MAXVAL FROM " . $this->getTableName();
		if ($result = $this->q->dreg->query($sQuery)) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$iMaxVal = $row->MAXVAL;
			}
		}
		return $iMaxVal;
	} // function
	
	public function getNextEEFieldId() {
		$v = $this->getMaxEEFieldId();
		$FieldName = "EEF" . $this->padNumber($v + 1, 3);
		return $FieldName;
	}
} //class

</script>
