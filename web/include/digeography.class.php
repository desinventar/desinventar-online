<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
class DIGeography extends DIRecord
{
	protected static $def = array(
		'GeographyId' => array('type' => 'VARCHAR', 'size' => 100, 'pk' =>1),
		'LangIsoCode' => array('type' => 'VARCHAR', 'size' => 3, 'pk' => 1),
		'RegionId' => array('type' => 'VARCHAR', 'size' => 50),
		'GeographyCode' => array('type' => 'VARCHAR', 'size' => 100, 'default' => '---'),
		'GeographyName' => array('type' => 'VARCHAR', 'size' => 200, 'default' => '---'),
		'GeographyFQName' => array('type' => 'VARCHAR', 'size' => 250, 'default' => '---'),
		'GeographyLevel' => array('type' => 'INTEGER', 'default' => -1),
		'GeographyActive' => array('type' => 'INTEGER', 'default' => -1),
		'RecordCreation' => array('type' => 'DATETIME'),
		'RecordSync' => array('type' => 'DATETIME'),
		'RecordUpdate' => array('type' => 'DATETIME')
	);
	public function __construct($prmSession)
	{
		$this->sTableName   = "Geography";
		$this->sPermPrefix  = "GEOGRAPHY";
		parent::__construct($prmSession);
		$num_args = func_num_args();
		if ($num_args >= 2)
		{
			$prmGeographyId = func_get_arg(1);
			$this->set('GeographyId', $prmGeographyId);
			$this->setGeographyLevel();
			if ($num_args >= 3)
			{
				$prmLangIsoCode = func_get_arg(2);
				$this->set('LangIsoCode', $prmLangIsoCode);
			} //if
			$this->load();
		} //if
	} // __construct

	public static function existId($prmSession, $prmGeographyId)
	{
		$bFound = 0;
		$LangIsoCode = $prmSession->q->getDBInfoValue('LangIsoCode');
		$Query= "SELECT * FROM Geography WHERE GeographyId='" . $prmGeographyId . "' " . 
		        " AND LangIsoCode='" . $LangIsoCode . "'";
		foreach($prmSession->q->dreg->query($Query) as $row)
		{
			$bFound = 1;
		}
		return $bFound;
	}
	
	public static function getNameById($prmSession, $prmGeographyId)
	{
		$GeographyName = '';
		$LangIsoCode = $prmSession->getDBInfoValue('LangIsoCode');
		$Query= "SELECT * FROM Geography WHERE GeographyId='" . $prmGeographyId . "' " . 
		        " AND LangIsoCode='" . $LangIsoCode . "'";
		foreach($prmSession->q->dreg->query($Query) as $row)
		{
			$GeographyName = $row['GeographyName'];
		}
		return $GeographyName;
	}
		
	public static function getIdByCode($prmSession, $prmGeographyCode)
	{
		$GeographyId = '';
		$LangIsoCode = $prmSession->getDBInfoValue('LangIsoCode');
		$sQuery= "SELECT * FROM Geography WHERE GeographyCode='" . $prmGeographyCode . "' " . 
		         " AND LangIsoCode='" . $LangIsoCode . "'";
		foreach($prmSession->q->dreg->query($sQuery) as $row)
		{
			$GeographyId = $row['GeographyId'];
		}
		return $GeographyId;
	}
	
	public static function loadByCode($prmSession, $prmGeographyCode)
	{
		$g = null;
		$GeographyId = self::getIdByCode($prmSession, $prmGeographyCode);
		$g = new self($prmSession, $GeographyId);
		return $g;
	}
	
	public static function getIdByName($prmSession, $prmGeographyName, $prmParentId)
	{
		$GeographyId = '';
		$LangIsoCode = $prmSession->getDBInfoValue('LangIsoCode');
		$Query= 'SELECT * FROM Geography WHERE GeographyName LIKE "' . $prmGeographyName . '" ' . 
		        ' AND LangIsoCode="' . $LangIsoCode . '"';
		if ($prmParentId != '')
		{
			$MinGeographyLevel = strlen($prmParentId)/5 - 1;
			$Query .= ' AND GeographyId LIKE "' . $prmParentId . '%" AND GeographyLevel > ' . $MinGeographyLevel;
		}
		$Query .= ' ORDER BY GeographyLevel DESC';
		foreach($prmSession->q->dreg->query($Query) as $row)
		{
			$GeographyId = $row['GeographyId'];
		}
		return $GeographyId;
	}
	
	public static function loadByName($prmSession, $prmGeographyName, $prmParentId)
	{
		$g = null;
		$GeographyId = self::getIdByName($prmSession, $prmGeographyName, $prmParentId);
		if ($GeographyId != '')
		{
			$g = new self($prmSession, $GeographyId);
		}
		return $g;
	}

	public function buildGeographyId($prmMyParentId)
	{
		$iGeographyLevel = strlen($prmMyParentId)/5;
		$sQuery = "SELECT * FROM Geography WHERE GeographyId LIKE '" . $prmMyParentId . "%' AND LENGTH(GeographyId)=" . ($iGeographyLevel + 1) * 5;
		$TmpStr = '';
		foreach($this->q->dreg->query($sQuery) as $row)
		{
			$TmpStr = substr($row['GeographyId'], $iGeographyLevel * 5, 5);
		}
		$TmpStr = $this->padNumber((int)$TmpStr + 1, 5);
		$GeographyId = $prmMyParentId . $TmpStr;
		return $GeographyId;
	}
	
	public function setGeographyId($prmMyParentId)
	{
		$answer = ERR_NO_ERROR;
		$geography_id = $this->buildGeographyId($prmMyParentId);
		if ($geography_id == '')
		{
			$answer = ERR_DEFAULT_ERROR;
		}
		if ($answer > 0)
		{
			$this->set('GeographyId', $geography_id);
			$this->setGeographyLevel();
		}
		return $answer;
	}
	
	public function setGeographyLevel()
	{
		$iGeographyLevel = (strlen($this->get('GeographyId'))/5) - 1;
		$this->set('GeographyLevel', $iGeographyLevel);
	}

	public function buildGeographyFQName()
	{
		$FQName = $this->get('GeographyName');
		$GeographyLevel = $this->get('GeographyLevel');
		if ($GeographyLevel > 0)
		{
			$ParentId = substr($this->get('GeographyId'), 0, $GeographyLevel*5);
			$g = new DIGeography($this->session, $ParentId);
			$FQName = $g->get('GeographyFQName') . '/' . $FQName;
		}
		return $FQName;
	}
	
	public function setGeographyFQName()
	{
		$FQName = $this->buildGeographyFQName();
		$this->set('GeographyFQName', $FQName);
	}
	
	public function saveGeographyFQName()
	{
		$this->set('GeographyFQName', $this->buildGeographyFQName());
		$query = "UPDATE Geography SET GeographyFQName=" . '"' . $this->get('GeographyFQName') . '"' . " WHERE GeographyId='" . $this->get('GeographyId') . "'";
		$this->q->dreg->query($query);
		$query = "SELECT * FROM Geography WHERE GeographyId LIKE '" . $this->get('GeographyId') . "%' AND GeographyLevel =" . ((int)$this->get('GeographyLevel') + 1) . " ORDER BY GeographyLevel,GeographyId;";
		foreach($this->q->dreg->query($query) as $row)
		{
			$g = new DIGeography($this->session, $row['GeographyId']);
			$g->saveGeographyFQName();
		} //foreach
	} //function

	public function update($withValidate=1, $bStrict=1)
	{
		$iReturn = ERR_NO_ERROR;
		if ($iReturn > 0)
		{
			// Update goegraphy children data if needed...
			if ($this->oOldField['info']['GeographyName'] != $this->oField['info']['GeographyName'])
			{
				$this->saveGeographyFQName();
			} //if
		} //if
		$iReturn = parent::update($withValidate,$bStrict);
		return $iReturn;
	}

	public function validateCreate($bStrict)
	{
		$iReturn = 1;
		$iReturn = $this->validateNotNull(-41, 'GeographyId');
		if ($iReturn > 0)
		{
			$iReturn = $this->validatePrimaryKey(-42);
		}
		return $iReturn;
	}

	public function validateNoDatacards($ErrCode)
	{
		$iReturn = ERR_NO_ERROR;
		$Count = 0;
		$Query = "SELECT COUNT(DisasterId) AS COUNT FROM Disaster WHERE GeographyId LIKE '" . $this->get('GeographyId') . "%'";
		foreach($this->q->dreg->query($Query) as $row)
		{
			$Count = $row['COUNT'];
		}
		if ($Count > 0)
		{
			$iReturn = $ErrCode;
		}
		return $iReturn;
	}

	public function validateUpdate($bStrict)
	{
		$oReturn = parent::validateUpdate($bStrict);
		$iReturn = $this->validateNotNull(-43, 'GeographyCode');
		if ($iReturn > 0)
		{
			$iReturn = $this->validateUnique(-44, 'GeographyCode');
			if ($iReturn > 0)
			{
				$iReturn = $this->validateNotNull(-45, 'GeographyName');
				if ($iReturn > 0)
				{
					$iReturn = $this->validateUnique(-46, 'GeographyFQName');
					if ($iReturn > 0)
					{
						$iReturn = $this->validateNotNull(-47, 'GeographyLevel');
						if ($iReturn > 0)
						{
							if ($this->get('GeographyActive') == 0)
							{
								$iReturn = $this->validateNoDatacards(-48);
							}
						}
					}
				}
			}
		}
		return $iReturn;
	}
	
	public function validateDelete($bStrict)
	{
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNoDatacards(-48);
		return $iReturn;
	}
	
	public function importFromCSV($cols, $values)
	{
		$iReturn = parent::importFromCSV($cols, $values);
		$this->set('GeographyLevel', $values[0]);
		$this->set('GeographyCode',  $values[1]);
		$this->set('GeographyName',  $values[2]);
		if (array_key_exists(4, $values))
		{
			if ($values[4] != '')
			{
				$this->set('GeographyId', $values[4]);
			}
		}
		else
		{
			$ParentCode = $values[3];
			$GeographyId = '';
			$p = self::loadByCode($this->session, $this->get('GeographyCode'));
			if (! is_null($p))
			{
				$GeographyId = $p->get('GeographyId');
			}
			if ($GeographyId != '')
			{
				// This Geography Code Already Exists, return error
				$oReturn['Error'][] = -1;
			}
			else
			{
				// Try to locate a parent for this item
				$ParentGeographyId = '';
				$p = self::loadByCode($this->session, $ParentCode);
				if (! is_null($p))
				{
					$ParentGeographyId = $p->get('GeographyId');
				}
				$this->setGeographyId($ParentGeographyId);
			}
		}
		$this->set('GeographyFQName', $this->buildGeographyFQName());
		return $iReturn;
	}
	
	public static function moveNodeTo($prmSession,$prmGeographyIdPrefix,
	                                  $prmNewGeographyIdPrefix,$prmGeographyCodePrefix,
	                                  $prmNewGeographyCodePrefix,$withChildren)
	{
		/* Move geography to a different parent node, updates 
		   GeographyId and associated Disaster records
		*/		
		$iReturn = ERR_NO_ERROR;
		if ($iReturn > 0)
		{
			if ($withChildren)
			{
				$Query = "SELECT * FROM Geography WHERE GeographyId LIKE '" . $prmGeographyIdPrefix . "%'";
			}
			else
			{
				$Query = "SELECT * FROM Geography WHERE GeographyId='" . $prmGeographyIdPrefix . "'";
			}
			foreach($prmSession->q->dreg->query($Query) as $row)
			{
				$GeographyId = $row['GeographyId'];
				$newGeographyId = $GeographyId;
				
				if ($prmNewGeographyIdPrefix != '')
				{
					$newGeographyId = $prmNewGeographyIdPrefix . substr($GeographyId,strlen($prmNewGeographyIdPrefix));
				
					// New Id must not exist in database...
					$bExist = self::existId($prmSession, $newGeographyId);
					if ($bExist)
					{
						$iReturn = ERR_UNKNOWN_ERROR;
					}
				}
				if ($iReturn > 0)
				{
					$g = new DIGeography($prmSession, $GeographyId);
					if ($GeographyId != $newGeographyId)
					{
						$Query = "UPDATE Geography SET GeographyId='" . $newGeographyId . "' WHERE GeographyId='" . $GeographyId . "'";
						$prmSession->q->dreg->query($Query);
						$Query = "UPDATE Disaster SET GeographyId='" . $newGeographyId . "' WHERE GeographyId='" . $GeographyId . "'";
						$prmSession->q->dreg->query($Query);
						$g->set('GeographyId', $newGeographyId);
						$g->setGeographyFQName();
					}
					// Update GeographyCode
					$GeographyCode = $g->get('GeographyCode');
					$newGeographyCode = $prmNewGeographyCodePrefix;
					$g->set('GeographyCode', $newGeographyCode);
					$r = $g->update();
				}
			}
		}
		return $iReturn;
	} //moveNodeTo()

} //class

</script>
