<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
*/
require_once(BASE . '/include/distatus.class.php');
class DIObject {
	var $session = null;
	var $RegionId  = '';
	// Dynamic Objects Variables
	var $sTableName = 'MyTable';
	var $sPermPrefix = 'OBJECT';
	var $sFieldKeyDef = '';
	var $sFieldDef = '';

	var $oOldField;	
	var $oField;
	var $oFieldType;
	var $conn = null;
	var $status = null;
	
	public function __construct($prmSession) {
		$this->session = $prmSession;
		$this->q = $prmSession->q;
		$prmSession->RegionId = $this->q->RegionId;
		//$this->q = new Query($prmSession->RegionId);
		$this->setConnection($prmSession->RegionId);
		$num_args = func_num_args();
		if ($num_args >= 1) {
			$this->oSession = func_get_arg(0);
			if ($num_args >= 3) {
				$this->sFieldKeyDef = func_get_arg(1);
				$this->sFieldDef    = func_get_arg(2);
			}
		}
		$this->oField = array();
		$this->oField['info'] = array();
		$this->oFieldType=array();
		$this->createFields($this->sFieldKeyDef);
		$this->createFields($this->sFieldDef);
		$this->set('RegionId', $this->session->RegionId);
		$LangIsoCode = 'eng';
		if ($this->q->RegionId != 'core') {
			//$LangIsoCode = $this->getDBInfoValue('LangIsoCode');
		}
		$this->set('LangIsoCode', $LangIsoCode);
		$this->set('RecordUpdate', gmdate('c'));
		
		// Status object
		$this->status = new DIStatus();
	} // constructor
	
	public function setConnection($prmDB) {
		if ($prmDB == 'core') {
			$this->conn = $this->q->core;
		} else {
			$this->conn = $this->q->dreg;
		}
	}
	
	public function createFields($prmFieldDef, $LangIsoCode='') {
		if ($LangIsoCode == '') {
			$obj = &$this->oField['info'];
		} else {
			$obj = &$this->oField[$LangIsoCode];
		}
		$sFields = split(',', $prmFieldDef);
		foreach ($sFields as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			$this->oFieldType[$sFieldName] = $sFieldType;
			if ($sFieldType == 'STRING')   { $obj[$sFieldName] = '';              }
			if ($sFieldType == 'TEXT')     { $obj[$sFieldName] = '';              }
			if ($sFieldType == 'DATETIME') { $obj[$sFieldName] = gmdate('c');     }
			if ($sFieldType == 'DATE')     { $obj[$sFieldName] = gmdate('Y-m-d'); }
			if ($sFieldType == 'INTEGER')  { $obj[$sFieldName] = 0;               }
			if ($sFieldType == 'FLOAT')    { $obj[$sFieldName] = 0.0;             }
			if ($sFieldType == 'DOUBLE')   { $obj[$sFieldName] = 0.0;             }
			if ($sFieldType == 'CURRENCY') { $obj[$sFieldName] = 0.0;             }
			if ($sFieldType == 'BOOLEAN')  { $obj[$sFieldName] = 1;               }
		}
	} // function
	
	public function get($prmKey, $LangIsoCode='') {
		$Value = '';
		try {
			if ($LangIsoCode == '') {
				$LangIsoCode = 'info';
			}
			if (array_key_exists($prmKey, $this->oField[$LangIsoCode])) {
				$Value = $this->oField[$LangIsoCode][$prmKey];
			}
		} catch (Exception $e) {
			showErrorMsg('Error ' . $e->getMessage());
		}
		return $Value;
	}
	
	public function getType($prmKey) {
		try {
			return $this->oFieldType[$prmKey];
		} catch (Exception $e) {
			showErrorMsg('Error ' . $e->getMessage());
		}		
	}
	
	public function set($prmKey, $prmValue, $LangIsoCode='') {
		if ($LangIsoCode == '') {
			$obj = &$this->oField['info'];
		} else {
			$obj = &$this->oField[$LangIsoCode];
		}
		$iReturn = ERR_DEFAULT_ERROR;
		if (isset($obj[$prmKey])) {
			$sValue = $prmValue;

			$sFieldType = $this->oFieldType[$prmKey];
			if ($sFieldType == 'STRING') {
				// Remove special chars...
				$sValue = trim($sValue);
				// Remove Double Quotes to prevent failures in SQL Queries
				$sValue = preg_replace('/"/', '', $sValue);
			}
			if ($sFieldType == 'BOOLEAN') {
				if ($sValue . '' == 'on')    { $sValue = 1; }
				if ($sValue . '' == 'true')  { $sValue = 1; }
				if ($sValue . '' == 'off')   { $sValue = 0; }
				if ($sValue . '' == 'false') { $sValue = 0; }
				if ($sValue . '' == '')      { $sValue = 0; }
			}
			if (($sFieldType == 'INTEGER') ||
			    ($sFieldType == 'DOUBLE' ) ||
			    ($sFieldType == 'FLOAT'  ) ||
			    ($sFieldType == 'CURRENCY')) {
				if ($sValue == '') { $sValue = 0; }
			}
			$obj[$prmKey] = $sValue;
			$iReturn = ERR_NO_ERROR;
		}
		return $iReturn;
	}

	public function setFromArray($prmArray) {
		$iReturn = ERR_NO_ERROR;
		foreach($prmArray as $sKey => $sValue) {
			$this->set($sKey, $sValue);
		}
		return $iReturn;
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
			showErrorMsg('exist : ' . $e->getMessage);
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
			showErrorMsg('load : ' . $e->getMessage);
		}
		$this->oOldField = $this->oField;
		return $iReturn;
	} // function load
	
	public function insert($withValidate = true) {
		$iReturn = ERR_NO_ERROR;
		$bValidate = $withValidate;
		if ($withValidate) {
			$iReturn = $this->validateCreate();
			if ($iReturn > 0 ) { 
				$iReturn = $this->validateUpdate();
				$bValidate = false;
			}
		}
		if ($iReturn > 0) {
			$iReturn = $this->create($bValidate);
			if ($iReturn > 0) {
				$iReturn = $this->update($bValidate);
			}
		}
		return $iReturn;
	}

	public function delete($withValidate = true) {
		$iReturn = ERR_NO_ERROR;
		if ($withValidate) {
			$iReturn = validateDelete();
		}
		if ($iReturn > 0) {
			$sQuery = $this->getDeleteQuery();
			try {
				if ($result = $this->conn->query($sQuery)) {
					$iReturn = ERR_NO_ERROR;
				}
			} catch (PDOException $e) {
				showErrorMsg('delete : ' . $e->getMessage);
				$iReturn = ERR_TABLE_LOCKED;
			}
		}
		return $iReturn;
	} // function

	public function create($withValidate = true) {
		$iReturn = ERR_NO_ERROR;
		if ($withValidate) {
			$iReturn = $this->validateCreate();
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

	public function update($withValidate = true, $withInsert = false) {
		$iReturn = ERR_NO_ERROR;
		if ($withValidate) {
			$iReturn = $this->validateUpdate();
		}
		if ($iReturn > 0) {
			$sQuery = $this->getUpdateQuery();
			
			if ($withInsert == true) {
				if ($this->exists() < 0) {
					$iReturn = $this->create($withValidate);
				}
			}
			
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

	public function existField($prmField, $section='info') {
		return array_key_exists($prmField, $this->oField[$section]);
	}

	public function validateCreate($bStrict=true) {
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

	public function validateUpdate($bStrict=true) {
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
	
	public function validateDelete() {
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
			showErrorMsg('validatePrimaryKey : ' . $e->getMessage);
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
			showErrorMsg('validateUnique : ' . $e->getMessage);
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
			showErrorMsg('validateRef : ' . $e->getMessage);
		}
		if ($iReturn < 0) {
			$this->status->addMsg($ErrCode, $prmFieldName . ' reference to table ' . $TableName . ' is invalid (' . $Value . ')');
		}
		return $iReturn;
	}

	public static function padNumber($iNumber, $iLen) {
		$sNumber = '' . $iNumber;
		while (strlen($sNumber) < $iLen) {
			$sNumber = '0' . $sNumber;
		}
		return $sNumber;
	} // function
	
	public function getFieldList() {
		$i = 0;
		$Value = '';
		foreach(array_keys($this->oField['info']) as $Field) {
			if ($i>0) { $Value .= ','; }
			$Value .= $Field;
			$i++;
		}
		return $Value;
	}

	public function importFromCSV($cols, $values) {
		return ERR_NO_ERROR;
	}
} // class

</script>
