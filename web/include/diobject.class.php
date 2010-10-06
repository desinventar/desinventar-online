<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
*/
require_once(BASE . '/include/distatus.class.php');
class DIObject {
	var $session = null;
	var $RegionId  = '';
	var $sFieldKeyDef = '';
	var $sFieldDef = '';

	var $oOldField;	
	var $oField;
	var $oFieldType;
	
	public function __construct($prmSession) {
		$this->session = $prmSession;
		$this->RegionId = $this->session->RegionId;
		$num_args = func_num_args();
		if ($num_args >= 1) {
			$this->session = func_get_arg(0);
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
		
	public function existField($prmField, $section='info') {
		return array_key_exists($prmField, $this->oField[$section]);
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
