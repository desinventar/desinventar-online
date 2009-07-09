<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIGeoLevel extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "GeoLevel";
		$this->sPermPrefix  = "GEOLEVEL";
		$this->sFieldKeyDef = "GeoLevelId/INTEGER," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "SyncRecord/DATETIME," .
		                      "GeoLevelName/STRING," .
		                      "GeoLevelDesc/STRING," .  
		                      "GeoLevelActive/INTEGER";
		parent::__construct($prmSession);
		$this->set("GeoLevelActive", 1);
		$this->set("LangIsoCode", $this->q->getDBInfoValue('I18NFirstLang'));

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
		$sQuery = "SELECT MAX(GeoLevelId) AS MAXVAL FROM GeoLevel";
		if ($result = $this->q->dreg->query($sQuery)) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$iMaxVal = $row->MAXVAL;
			}
		}
		return $iMaxVal;
	} // function

	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -31, 'GeoLevelId');
		$iReturn = $this->validatePrimaryKey($iReturn,  -32);
		return $iReturn;
	}

	public function validateUpdate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -33, 'GeoLevelName');
		$iReturn = $this->validateUnique($iReturn,  -34, 'GeoLevelName', true);
		return $iReturn;
	}
} //class

</script>
