<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIEEData extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "EEData";
		$this->sPermPrefix  = "DISASTER";
		$this->sFieldKeyDef = "DisasterId/STRING";
		$this->sFieldDef    = "SyncRecord/DATETIME";
		parent::__construct($prmSession);
		$sNewFields = $this->buildFieldDef();
		if ($sNewFields != '') {
			$this->sFieldDef .= ',' . $sNewFields;
			$this->createFields($this->sFieldKeyDef, $this->sFieldDef);
		}
		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmDisasterId = func_get_arg(1);
			$this->set('DisasterId', $prmDisasterId);
			$this->load();
		}
	} // __construct

	public function buildFieldDef() {
		$sFieldDef = '';
		$sQuery = "SELECT * FROM EEField ORDER BY EEFieldOrder";
		$i = 0;
		foreach ($result = $this->q->dreg->query($sQuery) as $row) {
			if ($i > 0) {
				$sFieldDef .= ',';
			}
			$sFieldDef .= $row['EEFieldId'] . '/' . $row['EEFieldType'];
			$i++;
		}
		return $sFieldDef;
	} // function
} //class
