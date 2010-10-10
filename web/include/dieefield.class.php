<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

class DIEEField extends DIRecord {
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
		$sQuery = "SELECT EEFieldId FROM " . $this->getTableName() . " ORDER BY EEFieldId DESC LIMIT 1";
		$EEFieldId = 'EEF000';
		foreach($this->q->dreg->query($sQuery) as $row) {
			$EEFieldId = $row['EEFieldId'];
		}
		$iMaxVal = (int)substr($EEFieldId, 3);
		return $iMaxVal;
	} // function
	
	public function getNextEEFieldId() {
		$v = $this->getMaxEEFieldId();
		$FieldName = "EEF" . $this->padNumber($v + 1, 3);
		return $FieldName;
	}
	
	public function insert($withValidate = true) {
		$iReturn = ERR_NO_ERROR;
		if ($iReturn > 0) {
			$iReturn = parent::insert($withValidate);
		}
		if ($iReturn > 0) {
			// Create column in EEData table
			$EEFieldType = $this->get('EEFieldType');
			$EEFieldSize = $this->get('EEFieldSize');
			switch ($EEFieldType) {
				case 'INT':
				case 'INTEGER':
				case 'NUMERIC':
				case 'BOOLEAN':
					$EEFieldType = 'NUMERIC';
				break;
				case 'FLOAT':
				case 'DOUBLE':
					$EEFieldType = 'REAL';
				break;
				case 'CURRENCY':
					$EEFieldType = 'NUMERIC(20,5)';
				break;
				case 'DATE':
					$EEFieldType = 'VARCHAR(20)';
				break;
				default:
					$EEFieldType = 'TEXT';
				break;
			}
			$Query = "ALTER TABLE EEData ADD COLUMN " . $this->get('EEFieldId') . ' ' . $EEFieldType;
			$this->q->dreg->query($Query);
		}
		return $iReturn;
	} //function

	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull(-81, 'EEFieldId');
		if ($iReturn > 0) {
			$iReturn = $this->validatePrimaryKey(-82);
		}
		
		return $iReturn;
	} //function

	public function validateUpdate() {
		$oReturn = parent::validateUpdate();
		$iReturn = ERR_NO_ERROR;
		if ($iReturn > 0) {
			$iReturn = $this->validateUnique(-83, 'EEFieldLabel', true);
		}
		//$oReturn['Status'] = $iReturn;
		$oReturn = $iReturn;
		return $oReturn;
	} //function
} //class

</script>
