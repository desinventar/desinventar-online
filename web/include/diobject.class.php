<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2009 Corporacion OSSO
*/

class DIObject {
	var $oSession   = '';
	var $sRegionId  = '';
	// Dynamic Objects Variables
	var $sTableName = 'MyTable';
	var $sPermPrefix = 'OBJECT';
	var $sFieldKeyDef = '';
	var $sFieldDef = '';
	
	var $oField;
	var $oFieldType;
	var $q;
	
	public function __construct($prmSession) {
		$this->q = new Query($prmSession->sRegionId);
		$num_args = func_num_args();
		if ($num_args >= 1) {
			$this->oSession = func_get_arg(0);
			if ($num_args >= 3) {
				$this->sFieldKeyDef = func_get_arg(1);
				$this->sFieldDef    = func_get_arg(2);
			}
		}
		$this->oField = array();
		$this->oFieldType=array();
		$this->createFields($this->sFieldKeyDef, $this->sFieldDef);
	} // constructor
	
	public function createFields($prmKeyDef, $prmFieldDef) {
		$sAllFields = $prmKeyDef;
		if ($prmFieldDef != '') {
			$sAllFields .= "," . $prmFieldDef;
		}
		$sFields = split(',', $sAllFields);
		foreach ($sFields as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			$this->oFieldType[$sFieldName] = $sFieldType;
			if ($sFieldType == "STRING")   { $this->oField[$sFieldName] = "";        }
			if ($sFieldType == "TEXT")     { $this->oField[$sFieldName] = "";        }
			if ($sFieldType == "DATETIME") { $this->oField[$sFieldName] = gmdate('c'); }
			if ($sFieldType == "INTEGER")  { $this->oField[$sFieldName] = -1;        }
			if ($sFieldType == "DOUBLE")   { $this->oField[$sFieldName] = 0.0;       }
			if ($sFieldType == "BOOLEAN")  { $this->oField[$sFieldName] = true;      }
		}
	} // function
	
	public function get($prmKey) {
		return $this->oField[$prmKey];
	}
	
	public function set($prmKey, $prmValue) {
		$iReturn = 0;
		if (isset($this->oField[$prmKey])) {
			$sValue = $prmValue;
			$sFieldType = $this->oFieldType[$prmKey];
			if ($sFieldType == 'STRING') {
				$sValue = trim($sValue);
			}
			if ($sFieldType == 'BOOLEAN') {
				if ($sValue . "" == "on")  { $sValue = 1; }
				if ($sValue . "" == "off") { $sValue = 0; }
			}
			if (($sFieldType == 'INTEGER') ||
			    ($sFieldType == 'DOUBLE') ) {
				if ($sValue == "") { $sValue = 0; }
			}
			$this->oField[$prmKey] = $sValue;
			$iReturn = 1;
		}
		return $iReturn;
	}

	public function setFromArray($prmArray) {
		foreach($prmArray as $sKey => $sValue) {
			$this->set($sKey, $sValue);
		}
	}
		
	public function getTableName() {
		return $this->sTableName;
	}
	
	public function getWhereSubQuery() {
		$i = 0;
		$sQuery = "(";
		foreach (split(',', $this->sFieldKeyDef) as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			if ($i > 0) { $sQuery .= " AND "; }
			$sQuery .= $sFieldName . "=";
			if (($sFieldType == "STRING"  ) || 
			    ($sFieldType == "TEXT"    ) ||
			    ($sFieldType == "DATETIME") ) {
			    $sQuery .= "'" . $this->oField[$sFieldName] . "'";
			}
			if (($sFieldType == "INTEGER") ||
			    ($sFieldType == "DOUBLE" ) ||
			    ($sFieldType == "BOOLEAN" ) ) {
			    $sQuery .= $this->oField[$sFieldName];
			}
			$i++;
		}
		$sQuery .= ")";
		return $sQuery;
	} // function

	public function getSelectQuery() {
		$sQuery = "SELECT * FROM " . $this->getTableName();
		$sQuery .= " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	} // function

	public function getDeleteQuery() {
		$sQuery = "DELETE FROM " . $this->getTableName();
		$sQuery .= " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	} // function
	
	public function getInsertQuery() {
		$i = 0;
		$sQueryFields = "";
		$sQueryValues = "";
		foreach (split(',', $this->sFieldKeyDef) as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			if ($i > 0) {
				$sQueryFields .= ",";
				$sQueryValues .= ",";
			}
			$sQueryFields .= $sFieldName;
			if (($sFieldType == "STRING"  ) || 
			    ($sFieldType == "TEXT"    ) ||
			    ($sFieldType == "DATETIME") ) {
			    $sQueryValues .= "'" . $this->oField[$sFieldName] . "'";
			}
			if (($sFieldType == "INTEGER") ||
			    ($sFieldType == "DOUBLE" ) ||
			    ($sFieldType == "BOOLEAN" ) ) {
			    $sQueryValues .= $this->oField[$sFieldName];
			}
			$i++;
		}
		$sQuery = "INSERT INTO " . $this->getTableName() . " (" . $sQueryFields . ") VALUES (" . $sQueryValues . ")";
		return $sQuery;
	} // function

	public function getUpdateQuery() {
		$i = 0;
		$sQueryFields = "";
		$sQueryValues = "";
		$sQuery = "UPDATE " . $this->getTableName() . " SET ";
		foreach (split(',', $this->sFieldDef) as $sKey => $sValue) {
			$oItem = split('/', $sValue);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			if ($i > 0) {
				$sQuery .= ",";
			}
			$sQueryItem = $sFieldName . "=";
			if (($sFieldType == "STRING"  ) || 
			    ($sFieldType == "TEXT"    ) ||
			    ($sFieldType == "DATETIME") ) {
			    $sQueryItem .= "'" . $this->oField[$sFieldName] . "'";
			} elseif (($sFieldType == "INTEGER") ||
			          ($sFieldType == "DOUBLE" ) ||
			          ($sFieldType == "BOOLEAN" ) ) {
			    $sQueryItem .= $this->oField[$sFieldName];
			} else {
				print "Unknown EventType : $sFieldType ($sFieldName)<br>";
			}
			$sQuery .= $sQueryItem;
			$i++;
		}
		$sQuery .= " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	} // function
	
	public function exist() {
		$iReturn = 0;
		$sQuery = $this->getSelectQuery();
		if ($result = $this->q->dreg->query($sQuery)) {
			if ($result->num_rows() > 0) {
				$bReturn = 1;
			}
		}
		return $iReturn;
	} // function
	
	public function load() {
		$iReturn = 0;
		$sQuery = $this->getSelectQuery();
		if ($result = $this->q->dreg->query($sQuery)) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$sAllFields = $this->sFieldKeyDef . "," . $this->sFieldDef;
				$sFields = split(',', $sAllFields);
				foreach ($sFields as $sKey => $sValue) {
					$oItem = split('/', $sValue);
					$sFieldName = $oItem[0];
					$sFieldType = $oItem[1];
					$this->oField[$sFieldName] = $row->$sFieldName;
				}
				$iReturn = 1;
			} // while
		} // if
		return $iReturn;
	} // function load
	
	
	public function insert() {
		$this->create();
		$this->update();
	}

	public function delete() {
		$iReturn = 0;
		$sQuery = $this->getDeleteQuery();
		if ($result = $this->q->dreg->query($sQuery)) {
			$iReturn = 1;		
		}
		return $iReturn;
	} // function

	public function create() {
		$iReturn = 0;
		$sQuery = $this->getInsertQuery();
		try {
			if ($result = $this->q->dreg->query($sQuery)) {
				$iReturn = 1;		
			}
		} catch (PDOException $e) {
			print "Error " . $e->getMessage() . "<br>";
		}
		return $iReturn;
	} // function

	public function update() {
		$iReturn = 0;
		if (!empty($this->SyncRecord)) {
			$this->SyncRecord = gmdate('c');
		}
		$iReturn = $this->validateUpdate();
		if ($iReturn > 0) {
			$sQuery = $this->getUpdateQuery();
			try {
				if ($result = $this->q->dreg->query($sQuery)) {
					$iReturn = 1;
				}
			} catch (PDOException $e) {
				print "Error " . $e->getMessage() . "<br>";
			}
		}
		return $iReturn;
	} // function
	
	public function validateUpdate() {
		return 1;
	}
} // class

</script>
