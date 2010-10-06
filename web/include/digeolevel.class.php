<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

class DIGeoLevel extends DIRecord {
	public function __construct($prmSession) {
		$this->sTableName   = "GeoLevel";
		$this->sPermPrefix  = "GEOLEVEL";
		$this->sFieldKeyDef = "GeoLevelId/INTEGER," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "RegionId/STRING," .
		                      "GeoLevelName/STRING," .
		                      "GeoLevelDesc/STRING," .  
		                      "GeoLevelActive/INTEGER," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," .
		                      "RecordUpdate/DATETIME";
		parent::__construct($prmSession);
		$this->set("GeoLevelActive", 1);

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmGeoLevelId = func_get_arg(1);
			$this->set('GeoLevelId', $prmGeoLevelId);
			if ($num_args >= 3) {
				$prmLangIsoCode = func_get_arg(2);
				$this->set('LangIsoCode', $prmLangIsoCode);
			}
			if ($num_args >= 4) {
				$prmGeoLevelName = func_get_arg(3);
				$this->set('GeoLevelName', $prmGeoLevelName);
			}
			$this->load();
		}
	} // __construct

	public function getMaxGeoLevel() {
		$iMaxVal = 0;
		$sQuery = "SELECT MAX(GeoLevelId) AS MAXVAL FROM GeoLevel WHERE LangIsoCode='" . $this->get('LangIsoCode') . "'";
		if ($result = $this->q->dreg->query($sQuery)) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$iMaxVal = $row->MAXVAL;
			}
		}
		return $iMaxVal;
	} // function

	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull(-31, 'GeoLevelId');
		if ($iReturn > 0) {
			$iReturn = $this->validatePrimaryKey(-32);
		}
		return $iReturn;
	}

	public function validateUpdate() {
		$oReturn = parent::validateUpdate();
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNotNull(-33, 'GeoLevelName');
		if ($iReturn > 0) {
			$iReturn = $this->validateUnique(-34, 'GeoLevelName', true);
		}
		$oReturn['Status'] = $iReturn;
		return $oReturn;
	}

	public function importFromCSV($cols, $values) {
		$oReturn = parent::importFromCSV($cols, $values);
		$iReturn = $oReturn['Status'];
		$this->set('GeoLevelId', $values[0]);
		$this->set('GeoLevelName', $values[1]);
		if ($iReturn > 0) {
			$oReturn['Status'] = $iReturn;
		}
		return $oReturn;
	} //function
} //class

</script>
