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
				$this->set('GeographyId', $prmGeographyId);
			}
		} //if
	} // __construct

	public function buildGeographyId($sMyParentId) {
		$iGeographyLevel = strlen($sMyParentId)/5;
		$sQuery = "SELECT * FROM Geography WHERE GeographyId LIKE '" . $sMyParentId . "%' AND LENGTH(GeographyId)=" . ($iGeographyLevel + 1) * 5;
		foreach($this->q->dreg->query($sQuery) as $row) {
			$TmpStr = substr($row['GeographyId'], $iGeographyLevel * 5, 5);
		}
		$TmpStr = $this->padNumber((int)$TmpStr + 1, 5);
		$sGeographyId = $sMyParentId . $TmpStr;
		$this->set('GeographyId', $sGeographyId);
		$this->setGeographyLevel();
		return $sGeographyId;
	}

	public function setGeographyLevel() {
		$iGeographyLevel = (strlen($this->get('GeographyId'))/5) - 1;
		$this->set('GeographyLevel', $iGeographyLevel);
	}
	public function padNumber($iNumber, $iLen) {
		$sNumber = "" . $iNumber;
		while (strlen($sNumber) < $iLen) {
			$sNumber = "0" . $sNumber;
		}
		return $sNumber;
	} // function
} //class
