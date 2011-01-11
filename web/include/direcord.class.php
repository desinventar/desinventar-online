<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
class DIRecord extends DIObject {
	// Dynamic Objects Variables
	var $sTableName = 'MyTable';
	var $sPermPrefix = 'OBJECT';
	var $conn = null;
	var $q = null;

	public function __construct($prmSession) {
		parent::__construct($prmSession);
		$this->q = $prmSession->q;
		$this->setConnection($prmSession->RegionId);
	}

	public function setConnection($prmDB) {
		if ($prmDB == 'core') {
			$this->conn = $this->q->core;
		} else {
			$this->conn = $this->q->dreg;
		}
	}

	public function getTableName() {
		return $this->sTableName;
	}
	
	public function getWhereSubQuery() {
		$i = 0;
		$sQuery = '(';
		foreach (split(',', $this->sFieldKeyDef) as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			if ($i > 0) { $sQuery .= ' AND '; }
			$sQuery .= $sFieldName . '=';
			if (($sFieldType == 'STRING'  ) || 
			    ($sFieldType == 'TEXT'    ) ||
			    ($sFieldType == 'DATETIME') ) {
			    $sQuery .= '"' . $this->get($sFieldName) . '"';
			}
			if (($sFieldType == 'INTEGER') ||
			    ($sFieldType == 'DOUBLE' ) ||
			    ($sFieldType == 'BOOLEAN' ) ) {
			    $sQuery .= $this->get($sFieldName);
			}
			$i++;
		}
		$sQuery .= ')';
		return $sQuery;
	} // function

	public function getSelectQuery() {
		$sQuery = 'SELECT * FROM ' . $this->getTableName();
		$sQuery .= ' WHERE ' . $this->getWhereSubQuery();
		return $sQuery;
	} // function

	public function getDeleteQuery() {
		$sQuery = 'DELETE FROM ' . $this->getTableName();
		$sQuery .= ' WHERE ' . $this->getWhereSubQuery();
		return $sQuery;
	} // function
	
	public function getInsertQuery() {
		$i = 0;
		$sQueryFields = '';
		$sQueryValues = '';
		foreach (split(',', $this->sFieldKeyDef) as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			if ($i > 0) {
				$sQueryFields .= ',';
				$sQueryValues .= ',';
			}
			$sQueryFields .= $sFieldName;
			if (($sFieldType == 'STRING'  ) || 
			    ($sFieldType == 'TEXT'    ) ||
			    ($sFieldType == 'DATETIME') ) {
			    $sQueryValues .= '"' . $this->get($sFieldName) . '"';
			}
			if (($sFieldType == 'INTEGER')  ||
				($sFieldType == 'FLOAT'   ) ||
			    ($sFieldType == 'DOUBLE'  ) ||
			    ($sFieldType == 'CURRENCY') ||
			    ($sFieldType == 'BOOLEAN' ) ) {
			    $sQueryValues .= $this->get($sFieldName);
			}
			$i++;
		}
		$sQuery = 'INSERT INTO ' . $this->getTableName() . ' (' . $sQueryFields . ') VALUES (' . $sQueryValues . ')';
		return $sQuery;
	} // function

	public function getUpdateQuery() {
		$i = 0;
		//2009-07-29 (jhcaiced) Update value in RecordUpdate
		if ($this->existField('RecordUpdate')) {
			$this->set('RecordUpdate', gmdate('c'));
		}
		
		$sQueryFields = '';
		$sQueryValues = '';
		$sQuery = 'UPDATE ' . $this->getTableName() . ' SET ';
		foreach (split(',', $this->sFieldDef) as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			if ($i > 0) {
				$sQuery .= ',';
			}
			$sQueryItem = $sFieldName . '=';
			if (($sFieldType == 'STRING'  ) || 
			    ($sFieldType == 'TEXT'    ) ||
			    ($sFieldType == 'DATETIME') ||
			    ($sFieldType == 'DATE'    ) ) {
			    $sQueryItem .= '"' . $this->get($sFieldName) . '"';
			} elseif (($sFieldType == 'INTEGER' ) ||
			          ($sFieldType == 'FLOAT'   ) ||
			          ($sFieldType == 'DOUBLE'  ) ||
			          ($sFieldType == 'BOOLEAN' ) ||
			          ($sFieldType == 'CURRENCY') ) {
			    $sQueryItem .= $this->get($sFieldName);
			} else {
				print 'Unknown Type : $sFieldType ($sFieldName)<br>';
			}
			$sQuery .= $sQueryItem;
			$i++;
		}
		$sQuery .= ' WHERE ' . $this->getWhereSubQuery();
		return $sQuery;
	} // function
	
	public function exist() {
		$iReturn = ERR_DEFAULT_ERROR;
		$query = $this->getSelectQuery();
		try {
			foreach($this->conn->query($query) as $row) {
				$iReturn = ERR_NO_ERROR;
			} //foreach
		} catch (Exception $e) {
			showErrorMsg('exist : ' . $e->getMessage());
			$iReturn = ERR_TABLE_LOCKED;
		}
		return $iReturn;
	} // function
	
	public function load() {
		$iReturn = ERR_OBJECT_NOT_FOUND;
		$sQuery = $this->getSelectQuery();
		try {
			foreach ($this->conn->query($sQuery) as $row) {
				$sAllFields = $this->sFieldKeyDef . ',' . $this->sFieldDef;
				$sFields = split(',', $sAllFields);
				foreach ($sFields as $sKey => $sValue) {
					$oItem = split('/', $sValue);
					$sFieldName = $oItem[0];
					$sFieldType = $oItem[1];
					if (array_key_exists($sFieldName, $row)) {
						$this->set($sFieldName, $row[$sFieldName]);
					} else {
						$this->set($sFieldName, '');
					}
				}
				$iReturn = ERR_NO_ERROR;
			} // foreach
		} catch (PDOException $e) {
			showErrorMsg('load : ' . $e->getMessage());
		}
		$this->oOldField = $this->oField;
		return $iReturn;
	} // function load
	
	public function insert($withValidate = true, $bStrict = true) {
		$iReturn = ERR_NO_ERROR;
		$bValidate = $withValidate;
		if ($withValidate) {
			$iReturn = $this->validateCreate($bStrict);
			if ($iReturn > 0 ) { 
				$iReturn = $this->validateUpdate($bStrict);
				$bValidate = false;
			}
		}
		if ($iReturn > 0) {
			$iReturn = $this->create($bValidate, $bStrict);
			if ($iReturn > 0) {
				$iReturn = $this->update($bValidate, $bStrict);
			}
		}
		return $iReturn;
	}

	public function delete($withValidate = true, $bStrict = true) {
		$iReturn = ERR_NO_ERROR;
		if ($withValidate) {
			$iReturn = validateDelete($bStrict);
		}
		if ($iReturn > 0) {
			$sQuery = $this->getDeleteQuery();
			try {
				if ($result = $this->conn->query($sQuery)) {
					$iReturn = ERR_NO_ERROR;
				}
			} catch (PDOException $e) {
				showErrorMsg('delete : ' . $e->getMessage());
				$iReturn = ERR_TABLE_LOCKED;
			}
		}
		return $iReturn;
	} // function

	public function create($withValidate = true, $bStrict = true) {
		$iReturn = ERR_NO_ERROR;
		if ($withValidate) {
			$iReturn = $this->validateCreate($bStrict);
		}
		if ($iReturn > 0) {
			$sQuery = $this->getInsertQuery();
			try {
				if ($result = $this->conn->query($sQuery)) {
					$iReturn = ERR_NO_ERROR;
				}
			} catch (PDOException $e) {
				showErrorMsg('create : ' . $e->getMessage());
				$iReturn = ERR_TABLE_LOCKED;
			}
		}
		return $iReturn;
	} // function

	public function update($withValidate = true, $bStrict = true) {
		$iReturn = ERR_NO_ERROR;
		if ($withValidate) {
			$iReturn = $this->validateUpdate($bStrict);
		}
		if ($iReturn > 0) {
			$sQuery = $this->getUpdateQuery();
			try {
				if (! $result = $this->conn->query($sQuery)) {
					$iReturn = ERR_UNKNOWN_ERROR;
				}
			} catch (PDOException $e) {
				showErrorMsg('update : ' . $e->getCode() . ' '. $e->getMessage());
				$iReturn = ERR_TABLE_LOCKED;
			}
		}
		if ($iReturn > 0) {
			$this->oOldField = $this->oField;
		}
		return $iReturn;
	} // function

	public function getIdWhereQuery() {
		$sQuery = '(';
		$i = 0;
		$sFields = split(',', $this->sFieldKeyDef);
		foreach ($sFields as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			$quote2 = '"';
			if ($sFieldType == 'INTEGER')  { $quote2 = ''; }
			if ($i > 0) { 
				$sQuery .= ' AND ';
			}
			$sQuery .= $sFieldName . '=' . $quote2 . $this->get($sFieldName) . $quote2;
			$i++;
		} // foreach
		$sQuery .= ')';
		return $sQuery;
	}

	public function validateCreate($bStrict) {
		$iReturn = ERR_NO_ERROR;
		if ($this->status->hasError()) {
			$iReturn = reset(array_keys($this->status->error));
		} elseif ($this->status->hasWarning()) {
			$iReturn = ERR_NO_ERROR;
			if ($bStrict) {
				$iReturn = reset(array_keys($this->status->warning));
			}
		}
		return $iReturn;
	}	

	public function validateUpdate($bStrict) {
		$iReturn = ERR_NO_ERROR;
		if ($this->status->hasError()) {
			$iReturn = reset(array_keys($this->status->error));
		} elseif ($this->status->hasWarning()) {
			$iReturn = ERR_NO_ERROR;
		}
		return $iReturn;
	}
	
	public function validateDelete($bStrict) {
		return 1;
	}

	public function validateNotNull($ErrCode, $FieldName, $isWarning=false) {
		$iReturn = ERR_NO_ERROR;
		$Value = $this->get($FieldName);
		$FieldType = $this->getType($FieldName);
		if ($FieldType == 'INTEGER') {
			$Value = (int)$Value;
		} else {
			if ($Value == '') {
				$iReturn = $ErrCode;
			}
		}
		if ($iReturn < 0) {
			$this->status->addMsg($ErrCode, $FieldName . ' is null', $isWarning);
		}
		return $iReturn;	
	}

	public function validatePrimaryKey($ErrCode) {
		$iReturn = ERR_NO_ERROR;
		$quote1 = '"';
		$sQuery = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ' . $this->getIdWhereQuery();
		try {
			foreach($this->conn->query($sQuery) as $row) {
				$iReturn = $ErrCode;
			} //foreach
		} catch (Exception $e) {
			showErrorMsg('validatePrimaryKey : ' . $e->getMessage());
		}
		
		if ($iReturn < 0) {
			$this->status->addMsg($ErrCode, ' Primary key is not unique');
		}
		return $iReturn;	
	}
	
	public function validateUnique($ErrCode, $prmFieldName, $isWarning=false) {
		$iReturn = ERR_NO_ERROR;
		$quote1 = '"';
		if ($this->getType($prmFieldName) == 'INTEGER') { $quote1 = ''; }
		$sQuery = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ' . 
			$prmFieldName . '=' . $quote1 . $this->get($prmFieldName) . $quote1;
		// Validate is LangIsoCode is defined and Modify the query when needed
		if ($this->existField('LangIsoCode')) {
			$sQuery .= ' AND LangIsoCode="' . $this->get('LangIsoCode') . '"';
		}
		try {
			foreach($this->conn->query($sQuery) as $row) {
				// Check if it's me !!
				$bFound = true;
				$i = 0;
				$sFields = split(',', $this->sFieldKeyDef);
				foreach ($sFields as $sKey => $sValue) {
					if ($bFound) {
						$oItem = split('/', $sValue);
						$sFieldName = $oItem[0];
						$sFieldType = $oItem[1];
						$bFound = $row[$sFieldName] == $this->get($sFieldName);
						$i++;
					}
				} // foreach
				if ($bFound) {
					$iReturn = ERR_NO_ERROR;
				} else {
					$iReturn = $ErrCode;
				}
			} //foreach
		} catch (Exception $e) {
			showErrorMsg('validateUnique : ' . $e->getMessage());
		}
		if ($iReturn < 0) {
			$this->status->addMsg($ErrCode, $prmFieldName . ' value is not unique.', $isWarning);
		}
		return $iReturn;	
	}
	
	public function validateRef($ErrCode, $prmFieldName, $TableName, $FieldDst) {
		$quote = '"';
		if ($this->getType($prmFieldName) == 'INTEGER') {
			$quote = '';
		}
		$Value = $this->get($prmFieldName);
		$sQuery = 'SELECT ' . $FieldDst . ' FROM ' . $TableName . ' WHERE ' . $FieldDst . '=' . $quote . $Value .  $quote;
		$iReturn = $ErrCode;
		try {
			foreach($this->conn->query($sQuery) as $row) {
				$iReturn = ERR_NO_ERROR;
			}
		} catch (Exception $e) {
			showErrorMsg('validateRef : ' . $e->getMessage());
		}
		if ($iReturn < 0) {
			$this->status->addMsg($ErrCode, $prmFieldName . ' reference to table ' . $TableName . ' is invalid (' . $Value . ')');
		}
		return $iReturn;
	}

	
} //class
</script>
