<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIGeography extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Geography";
		$this->sPermPrefix  = "GEOGRAPHY";
		$this->sFieldKeyDef = "GeographyId/STRING," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "SyncRecord/DATETIME," .
		                      "GeographyCode/STRING," .
		                      "GeographyName/STRING," .  
		                      "GeographyLevel/INTEGER," .
		                      "GeographyActive/BOOLEAN";
		parent::__construct($prmSession);
		$this->set("LangIsoCode", $this->q->getDBInfoValue('I18NFirstLang'));
		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmGeographyId = func_get_arg(1);
			$this->set('GeographyId', $prmGeographyId);
			if ($num_args >= 3) {
				$prmLangIsoCode = func_get_arg(2);
				$this->set('LangIsoCode', $prmLangIsoCode);
			} //if
			$this->load();
			if ($num_args >= 4) {
				$prmParentId = func_get_arg(3);
				$prmGeographyId = $this->buildGeographyId($prmParentId);
				$this->setGeographyId($prmGeographyId);
			}
		} //if
	} // __construct

	public function buildGeographyId($prmMyParentId) {
		$iGeographyLevel = strlen($prmMyParentId)/5;
		$sQuery = "SELECT * FROM Geography WHERE GeographyId LIKE '" . $prmMyParentId . "%' AND LENGTH(GeographyId)=" . ($iGeographyLevel + 1) * 5;
		$TmpStr = '';
		foreach($this->q->dreg->query($sQuery) as $row) {
			$TmpStr = substr($row['GeographyId'], $iGeographyLevel * 5, 5);
		}
		if ($TmpStr == '') {
			$GeographyId = '';
		} else {
			$TmpStr = $this->padNumber((int)$TmpStr + 1, 5);
			$GeographyId = $prmMyParentId . $TmpStr;
		}
		return $GeographyId;
	}
	
	public function setGeographyId($prmMyParentId) {
		$iReturn = 1;
		$GeographyId = $this->buildGeographyId($prmMyParentId);
		if ($GeographyId == '') { $iReturn = -1; }
		if ($iReturn > 0) {
			$this->set('GeographyId', $GeographyId);
			$this->setGeographyLevel();
		}
		return $iReturn;
	}
	
	public function getIdByCode($prmGeographyCode) {
		$GeographyId = '';
		$Query = "SELECT * FROM Geography Where GeographyCode='" . $prmGeographyCode . "'";
		foreach($this->q->dreg->query($Query) as $row) {
			$GeographyId = $row['GeographyId'];
		}
		return $GeographyId;
	}

	public function setGeographyLevel() {
		$iGeographyLevel = (strlen($this->get('GeographyId'))/5) - 1;
		$this->set('GeographyLevel', $iGeographyLevel);
	}

	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -41, 'GeographyId');
		$iReturn = $this->validatePrimaryKey($iReturn,  -42);
		return $iReturn;
	}
	public function validateUpdate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -43, 'GeographyCode');
		$iReturn = $this->validateUnique($iReturn,  -44, 'GeographyCode');
		$iReturn = $this->validateNotNull($iReturn, -45, 'GeographyName');
		$iReturn = $this->validateUnique($iReturn,  -46, 'GeographyName');
		$iReturn = $this->validateNotNull($iReturn, -47, 'GeographyLevel');
		return $iReturn;
	}
	
} //class

</script>
