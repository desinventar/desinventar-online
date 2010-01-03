<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
*/

class DIGeography extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Geography";
		$this->sPermPrefix  = "GEOGRAPHY";
		$this->sFieldKeyDef = "GeographyId/STRING," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "RegionId/STRING," .
		                      "GeographyCode/STRING," .
		                      "GeographyName/STRING," .
		                      "GeographyFQName/STRING," . 
		                      "GeographyLevel/INTEGER," .
		                      "GeographyActive/BOOLEAN," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," . 
		                      "RecordUpdate/DATETIME";
		parent::__construct($prmSession);
		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmGeographyId = func_get_arg(1);
			$this->set('GeographyId', $prmGeographyId);
			$this->setGeographyLevel();
			if ($num_args >= 3) {
				$prmLangIsoCode = func_get_arg(2);
				$this->set('LangIsoCode', $prmLangIsoCode);
			} //if
			$this->load();
		} //if
	} // __construct

	public static function existId($prmSession, $prmGeographyId) {
		$bFound = 0;
		$LangIsoCode = $prmSession->q->getDBInfoValue('LangIsoCode');
		$Query= "SELECT * FROM Geography WHERE GeographyId='" . $prmGeographyId . "' " . 
		        " AND LangIsoCode='" . $LangIsoCode . "'";
		foreach($prmSession->q->dreg->query($Query) as $row) {
			$bFound = 1;
		}
		return $bFound;
	}
	
	public static function getNameById($prmSession, $prmGeographyId) {
		$GeographyName = '';
		$LangIsoCode = $prmSession->q->getDBInfoValue('LangIsoCode');
		$Query= "SELECT * FROM Geography WHERE GeographyId='" . $prmGeographyId . "' " . 
		        " AND LangIsoCode='" . $LangIsoCode . "'";
		foreach($prmSession->q->dreg->query($Query) as $row) {
			$GeographyName = $row['GeographyName'];
		}
		return $GeographyName;
	}
		
	public static function getIdByCode($prmSession, $prmGeographyCode) {
		$GeographyId = '';
		$LangIsoCode = $prmSession->q->getDBInfoValue('LangIsoCode');
		$Query= "SELECT * FROM Geography WHERE GeographyCode='" . $prmGeographyCode . "' " . 
		        " AND LangIsoCode='" . $LangIsoCode . "'";
		foreach($prmSession->q->dreg->query($Query) as $row) {
			$GeographyId = $row['GeographyId'];
		}
		return $GeographyId;
	}
	
	public static function loadByCode($prmSession, $prmGeographyCode) {
		$g = null;
		$GeographyId = self::getIdByCode($prmSession, $prmGeographyCode);
		if ($GeographyId != '') {
			$g = new self($prmSession, $GeographyId);
		}
		return $g;
	}
	
	public static function getIdByName($prmSession, $prmGeographyName, $prmParentId) {
		$GeographyId = '';
		$LangIsoCode = $prmSession->q->getDBInfoValue('LangIsoCode');
		$Query= "SELECT * FROM Geography WHERE GeographyName='" . $prmGeographyName . "' " . 
		        " AND LangIsoCode='" . $LangIsoCode . "'";
		if ($prmParentId != '') {
			$MinGeographyLevel = strlen($prmParentId)/5 - 1;
			$Query .= " AND GeographyId LIKE '" . $prmParentId . "%' AND GeographyLevel > " . $MinGeographyLevel;
		}
		$Query .= ' ORDER BY GeographyLevel DESC';
		foreach($prmSession->q->dreg->query($Query) as $row) {
			$GeographyId = $row['GeographyId'];
		}
		return $GeographyId;
	}
	
	public static function loadByName($prmSession, $prmGeographyName, $prmParentId) {
		$g = null;
		$GeographyId = self::getIdByName($prmSession, $prmGeographyName, $prmParentId);
		if ($GeographyId != '') {
			$g = new self($prmSession, $GeographyId);
		}
		return $g;
	}

	public function buildGeographyId($prmMyParentId) {
		$iGeographyLevel = strlen($prmMyParentId)/5;
		$sQuery = "SELECT * FROM Geography WHERE GeographyId LIKE '" . $prmMyParentId . "%' AND LENGTH(GeographyId)=" . ($iGeographyLevel + 1) * 5;
		$TmpStr = '';
		foreach($this->q->dreg->query($sQuery) as $row) {
			$TmpStr = substr($row['GeographyId'], $iGeographyLevel * 5, 5);
		}
		$TmpStr = $this->padNumber((int)$TmpStr + 1, 5);
		$GeographyId = $prmMyParentId . $TmpStr;
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
	
	public function setGeographyLevel() {
		$iGeographyLevel = (strlen($this->get('GeographyId'))/5) - 1;
		$this->set('GeographyLevel', $iGeographyLevel);
	}

	public function buildGeographyFQName() {
		$FQName = $this->get('GeographyName');
		$GeographyLevel = $this->get('GeographyLevel');
		if ($GeographyLevel > 0) {
			$ParentId = substr($this->get('GeographyId'), 0, $GeographyLevel*5);
			$g = new DIGeography($this->session, $ParentId);
			$FQName = $g->get('GeographyFQName') . '/' . $FQName;
		}
		return $FQName;
	}
	
	public function setGeographyFQName() {
		$FQName = $this->buildGeographyFQName();
		$this->set('GeographyFQName', $FQName);
	}
	
	public function saveGeographyFQName() {
		$this->set('GeographyFQName', $this->buildGeographyFQName());
		$query = "UPDATE Geography SET GeographyFQName=" . '"' . $this->get('GeographyFQName') . '"' . " WHERE GeographyId='" . $this->get('GeographyId') . "'";
		$this->q->dreg->query($query);
		$query = "SELECT * FROM Geography WHERE GeographyId LIKE '" . $this->get('GeographyId') . "%' AND GeographyLevel =" . ((int)$this->get('GeographyLevel') + 1) . " ORDER BY GeographyLevel,GeographyId;";
		foreach($this->q->dreg->query($query) as $row) {
			$g = new DIGeography($this->session, $row['GeographyId']);
			$g->saveGeographyFQName();
		} //foreach
	} //function

	public function update($withValidate = true) {
		$iReturn = ERR_NO_ERROR;
		if ($iReturn > 0) {
			// Update goegraphy children data if needed...
			if ($this->oOldField['info']['GeographyName'] != $this->oField['info']['GeographyName']) {
				$this->saveGeographyFQName();
			} //if
		} //if
		$iReturn = parent::update($withValidate);
		return $iReturn;
	}

	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull(-41, 'GeographyId');
		if ($iReturn > 0) {
			$iReturn = $this->validatePrimaryKey(-42);
		}
		return $iReturn;
	}

	public function validateNoDatacards($ErrCode) {
		$iReturn = ERR_NO_ERROR;
		$Count = 0;
		$Query = "SELECT COUNT(DisasterId) AS COUNT FROM Disaster WHERE GeographyId LIKE '" . $this->get('GeographyId') . "%'";
		foreach($this->q->dreg->query($Query) as $row) {
			$Count = $row['COUNT'];
		}
		if ($Count > 0) {
			$iReturn = $ErrCode;
		}
		return $iReturn;
	}

	public function validateUpdate() {
		$oReturn = parent::validateUpdate();
		$iReturn = $this->validateNotNull(-43, 'GeographyCode');
		if ($iReturn > 0) {
			$iReturn = $this->validateUnique(-44, 'GeographyCode');
			if ($iReturn > 0) {
				$iReturn = $this->validateNotNull(-45, 'GeographyName');
				if ($iReturn > 0) {
					$iReturn = $this->validateUnique(-46, 'GeographyFQName');
					if ($iReturn > 0) {
						$iReturn = $this->validateNotNull(-47, 'GeographyLevel');
						if ($iReturn > 0) {
							if ($this->get('GeographyActive') == 0) {
								$iReturn = $this->validateNoDatacards(-48);
							}
						}
					}
				}
			}
		}
		$oReturn['Status'] = $iReturn;
		return $oReturn;
	}
	
	public function validateDelete() {
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNoDatacards(-48);
		return $iReturn;
	}
	
	public function importFromCSV($cols, $values) {
		$oReturn = parent::importFromCSV($cols, $values);
		$iReturn = ERR_NO_ERROR;
		
		$this->set('GeographyLevel', $values[0]);
		$this->set('GeographyCode',  $values[1]);
		$this->set('GeographyName',  $values[2]);
		$ParentCode = $values[3];
		$p = self::loadByCode($this->session, $this->get('GeographyCode'));
		if ($p->get('GeographyId') != '') {
			// This Geography Code Already Exists, return error
			$oReturn['Error'][] = -1;
		} else {
			// Try to locate a parent for this item
			$p = self::loadByCode($this->session, $ParentCode);
			$this->setGeographyId($p->get('GeographyId'));
			$this->set('GeographyFQName', $this->buildGeographyFQName());
		}
		if ( (count($oReturn['Error']) > 0) || (count($oReturn['Warning']) > 0) ) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		$oReturn['Status'] = $iReturn;
		return $oReturn;
	}
	
	public static function moveNodeTo($prmSession,$prmGeographyIdPrefix,$prmNewGeographyIdPrefix,$prmGeographyCodePrefix,$prmNewGeographyCodePrefix) {
		/* Move geography to a different parent node, updates 
		   GeographyId and associated Disaster records
		*/		
		$iReturn = ERR_NO_ERROR;

		$prmNewGeographyName = self::getNameById($prmSession, $prmNewGeographyIdPrefix);
		if ($prmNewGeographyName == '') {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		if ($iReturn > 0) {
			$Query = "SELECT * FROM Geography WHERE GeographyId LIKE '" . $prmGeographyIdPrefix . "%'";
			foreach($prmSession->q->dreg->query($Query) as $row) {
				$GeographyId = $row['GeographyId'];
				$newGeographyId = $prmNewGeographyIdPrefix . substr($GeographyId,strlen($prmNewGeographyIdPrefix));
				
				// New Id must not exist in database...
				$bExist = self::existId($prmSession, $newGeographyId);
				if ($bExist) {
					$iReturn = ERR_UNKNOWN_ERROR;
				}

				if ($iReturn > 0) {
					$g = new DIGeography($prmSession, $GeographyId);
					$Query = "UPDATE Geography SET GeographyId='" . $newGeographyId . "' WHERE GeographyId='" . $GeographyId . "'";
					$prmSession->q->dreg->query($Query);
					$Query = "UPDATE Disaster SET GeographyId='" . $newGeographyId . "' WHERE GeographyId='" . $GeographyId . "'";
					$prmSession->q->dreg->query($Query);
					$g->set('GeographyId', $newGeographyId);
					$g->setGeographyFQName();					
					// Update GeographyCode
					$GeographyCode = $g->get('GeographyCode');
					$newGeographyCode = $prmNewGeographyCodePrefix . substr($GeographyCode, strlen($prmGeographyCodePrefix));
					$g->set('GeographyCode', $newGeographyCode);
					$g->update();
				}
			}
		}
		return $iReturn;
	}
} //class

</script>
