<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIRegion extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Region";
		$this->sPermPrefix  = "INFO";
		$this->sFieldKeyDef = "RegionId/STRING";
		$this->sFieldDef    = "RegionLabel/STRING," .
		                      "LangIsoCode/STRING," . 
		                      "CountryIso/STRING," .
		                      "RegionOrder/INTEGER," .
		                      "RegionStatus/INTEGER," .
		                      "RegionLastUpdate/DATETIME," .
		                      "IsCRegion/INTEGER," .
		                      "IsVRegion/INTEGER";
		$this->sInfoDef     = "DBVersion/STRING," .
		                      "PeriodBeginDate/DATE," .
		                      "PeriodEndDate/DATE," .
		                      "GeoLimitMinX/DOUBLE," . 
		                      "GeoLimitMinY/DOUBLE," . 
		                      "GeoLimitMaxX/DOUBLE," . 
		                      "GeoLimitMaxY/DOUBLE," .
		                      "OptionOutOfRange/INTEGER," .
		                      "OptionLanguageList/STRING," .
		                      "OptionOldName/STRING";
		$this->sInfoTrans   = "InfoCredits/STRING," . 
		                      "InfoGeneral/STRING," .
		                      "InfoSources/STRING," .
		                      "InfoSynopsis/STRING," . 
		                      "InfoObservation/STRING," . 
		                      "InfoGeography/STRING," . 
		                      "InfoCartography/STRING," .
		                      "InfoAdminURL/STRING";

		parent::__construct($prmSession);
		if ($this->get('OptionLanguageList') == '') {
			$this->set('OptionLanguageList', $this->get('LangIsoCode'));
		}
		$this->setConnection("core");
		$this->createFields($this->sInfoDef);
		$this->createFields($this->sInfoTrans);
		$this->set('PeriodBeginDate', '1900-01-01');
		$this->set('PeriodEndDate', gmdate('Y-m-d'));
		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmRegionId = func_get_arg(1);
			if ($prmRegionId != '') {
				$this->set('RegionId', $prmRegionId);
				$iReturn = $this->q->setDBConnection($prmRegionId);
			}
			if ($iReturn > 0) {
				$iReturn = $this->load();
			}
		}
	} // __construct

	public function getTranslatableFields() {
		// 2009-07-28 (jhcaiced) Build an array with translatable fields
		$Translatable = array();
		foreach (split(',', $this->sInfoTrans) as $sItem) {
			$oItem = split('/', $sItem);
			$sFieldName = $oItem[0];
			$sFieldType = $oItem[1];
			$Translatable[$sFieldName] = $sFieldType;
		} //foreach
		return $Translatable;
	}

	public function loadInfo() {
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->q->setDBConnection($this->get('RegionId'));
		if ($iReturn > 0) {
			$this->setConnection($this->get('RegionId'));
			try {
				$sQuery = "SELECT * FROM Info WHERE LangIsoCode=''";
				foreach($this->conn->query($sQuery) as $row) {
					$InfoValue = $row['InfoValue'];
					$InfoKey   = $row['InfoKey'];
					$this->set($InfoKey, $InfoValue);
				} // foreach
			} catch (Exception $e) {
				$iReturn = ERR_NO_DATABASE;
			}
			$this->setConnection('core');
		}
		$this->loadInfoTrans($this->get('LangIsoCode'));
		return $iReturn;
	}

	public function loadInfoTrans($prmLangIsoCode) {
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->q->setDBConnection($this->get('RegionId'));
		if ($iReturn > 0) {
			$this->setConnection($this->get('RegionId'));
			try {
				$sQuery = "SELECT * FROM Info WHERE LangIsoCode='" . $prmLangIsoCode . "'";
				foreach($this->conn->query($sQuery) as $row) {
					$InfoValue = $row['InfoValue'];
					$InfoKey   = $row['InfoKey'];
					$this->set($InfoKey, $InfoValue);
				} // foreach
			} catch (Exception $e) {
				$iReturn = ERR_NO_DATABASE;
			}
			$this->setConnection('core');
		}
		return $iReturn;
	}
	
	public function	saveInfo() {
		$iReturn = ERR_NO_ERROR;
		$now = gmdate('c');
		$this->setConnection($this->get('RegionId'));
		$Translatable = $this->getTranslatableFields();
		foreach($this->oField as $InfoKey => $InfoValue) {
			if (! array_key_exists($InfoKey, $Translatable)) {
				$LangIsoCode = '';
				$sQuery = "DELETE FROM Info WHERE InfoKey='" . $InfoKey . "'";
				$this->conn->query($sQuery);
				$sQuery = "INSERT INTO Info VALUES ('" . $InfoKey . "','" . $LangIsoCode . "','" . $InfoValue . "','','" . $now . "','" . $now . "','" . $now . "')";
				$this->conn->query($sQuery);
			} //if
		} //foreach
		$this->setConnection('core');
		$this->saveInfoTrans($this->get('LangIsoCode'));
		return $iReturn;
	}
	
	public function saveInfoTrans($prmLangIsoCode) {
		$iReturn = ERR_NO_ERROR;
		$now = gmdate('c');
		$this->setConnection($this->get('RegionId'));
		$Translatable = $this->getTranslatableFields();
		foreach($this->oField as $InfoKey => $InfoValue) {
			if (array_key_exists($InfoKey, $Translatable)) {
				$LangIsoCode = $prmLangIsoCode;
				$sQuery = "DELETE FROM Info WHERE InfoKey='" . $InfoKey . "' AND LangIsoCode='" . $LangIsoCode . "'";
				$this->conn->query($sQuery);
				$sQuery = "INSERT INTO Info VALUES ('" . $InfoKey . "','" . $LangIsoCode . "','" . $InfoValue . "','','" . $now . "','" . $now . "','" . $now . "')";
				$this->conn->query($sQuery);
			}
		} //foreach
		$this->setConnection('core');
		return $iReturn;
	}

	public function load() {
		$iReturn = ERR_NO_ERROR;
		if ($iReturn > 0) {
			$iReturn = parent::load();
		}
		if ($iReturn > 0) {
			$iReturn = $this->loadInfo();
		}
		return $iReturn;
	}
	
	public function update() {
		// Call the original update() function, update core.Region table
		$iReturn = parent::update();
		if ($iReturn > 0) {
			// This should update the region.Info table
			$iReturn = $this->saveInfo();
		}
		return $iReturn;
	}
	
	public function createRegionDB($prmGeoLevelName='') {
		// Creates/Initialize the region database
		$iReturn = ERR_NO_ERROR;
		$prmRegionId = $this->get('RegionId');
		// Create Directory for New Region
		$DBDir = VAR_DIR . '/' . $prmRegionId . '/';
		$DBFile = $DBDir . '/desinventar.db';
		try {
			if (!file_exists($DBDir)) {
				mkdir($DBDir);
			}
			if (file_exists(CONST_DBREGION)) {
				// Backup previous desinventar.db if exists
				if (file_exists($DBFile)) {
					$DBFile2 = $DBFile . '.bak';
					if (file_exists($DBFile2)) {
						unlink($DBFile2);
					}
					rename($DBFile, $DBFile2);
				}
				$iReturn = copy(CONST_DBREGION, $DBFile);
				$this->q->setDBConnection($this->get('RegionId'));
				$this->session->open($prmRegionId);
			}
		} catch (Exception $e) {
			showErrorMsg("Error " . $e->getMessage());
		}
		$this->set('RegionId', $prmRegionId);
		
		if ($iReturn > 0) {
			// Copy Predefined Event/Cause Lists
			$this->copyEvents($this->get('LangIsoCode'));
			$this->copyCauses($this->get('LangIsoCode'));
		}
		
		if ($iReturn > 0) {
			// Insert Data Into core.Region, create Info Table
			$this->insert();
		}
		
		if ($iReturn > 0) {
			// Create Generic GeoLevel 0
			if ($prmGeoLevelName == '') {
				$prmGeoLevelName = 'Level 0';
			}
			$g = new DIGeoLevel($this->session, 0);
			$g->set('GeoLevelName', $prmGeoLevelName);
			if ($g->exist() > 0) {
				$g->update();
			} else {
				$g->insert();
			}
		}
		return $iReturn;
	}

	public function addRegionItem($prmRegionItemId, $prmRegionItemGeographyName, $prmRegionItemGeographyId='') {
		$iReturn = ERR_NO_ERROR;
		
		$RegionId = $this->get('RegionId');
		$RegionDir = VAR_DIR . '/' . $this->get('RegionId');
		$RegionItemDir = VAR_DIR . '/' . $prmRegionItemId;
		$RegionItemDB = $RegionItemDir . '/desinventar.db';
		
		if ($prmRegionItemGeographyId == '') {
			$prmRegionItemGeographyId = $this->getRegionItemGeographyId($prmRegionItemId);
		}
		printf("%-20s %-5s %s\n", $prmRegionItemGeographyName, $prmRegionItemGeographyId, $prmRegionItemId); 
		$this->addRegionItemSync($prmRegionItemId);

		// Create Geography element at GeographyLevel=0 for this RegionItem
		// Delete Existing Elements
		$Query = "DELETE FROM Geography WHERE GeographyCode='" . $prmRegionItemId . "'";
		$this->q->dreg->query($Query);
		$g = new DIGeography($this->session, 
		                     $prmRegionItemGeographyId,
		                     $this->get('LangIsoCode'));
		$g->set('GeographyCode', $prmRegionItemId);
		$g->set('GeographyName', $prmRegionItemGeographyName);
		$iReturn = $g->insert();
		return $iReturn;
	}

	public function addPredefinedItemSync() {
		// Sync record for Predefined Events
		$s = new DISync($this->session);
		$s->set('SyncTable', 'Event');
		$s->set('RegionId' , $this->get('RegionId'));
		$s->set('SyncURL'  , 'file:///base');
		$s->set('SyncSpec' , '');
		$s->insert();

		// Sync record for Predefined Cause
		$s = new DISync($this->session);
		$s->set('SyncTable', 'Cause');
		$s->set('RegionId' , $this->get('RegionId'));
		$s->set('SyncURL'  , 'file:///base');
		$s->set('SyncSpec' , '');
		$s->insert();
	}
	
	public function addRegionItemSync($prmRegionItemId) {
		foreach($this->getRegionTables() as $TableName) {
			$s = new DISync($this->session);
			$s->set('SyncTable', $TableName);
			$s->set('RegionId', $prmRegionItemId);
			$s->set('SyncURL', "file:///" . $prmRegionItemId);
			$s->insert();
		} //foreach
	}
	
	public function addRegionItem2($prmRegionItemId, $prmRegionItemGeographyName, $prmRegionItemGeographyId='') {
		$RegionId = $this->get('RegionId');
		$RegionDir = VAR_DIR . '/' . $this->get('RegionId');
		$RegionItemDir = VAR_DIR . '/' . $prmRegionItemId;
		$RegionItemDB = $RegionItemDir . '/desinventar.db';
		
		if ($prmRegionItemGeographyId == '') {
			$prmRegionItemGeographyId = $this->getRegionItemGeographyId($prmRegionItemId);
		}
		$iReturn = ERR_NO_ERROR;
		if ($prmRegionItemId == '') {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		if ($iReturn > 0) {
			if (!file_exists($RegionItemDB)) {
				$iReturn = ERR_FILE_NOT_FOUND;
			}	
		}
		if ($iReturn > 0) {
			// Add RegionItem record
			$iReturn = $this->addRegionItemRecord($prmRegionItemId);
		}
		if ($iReturn > 0) {
			// Add Geography to Level0
			if ($prmRegionItemGeographyId == '') {
				$prmRegionItemGeographyId = $this->getRegionItemGeographyId($prmRegionItemId);
			}
			$iReturn = $this->addRegionItemGeography($prmRegionItemId, $prmRegionItemGeographyId);
		}
		if ($iReturn > 0) {
			$iReturn = $this->addRegionItemGeoLevel($prmRegionItemId);
		}
		if ($iReturn > 0) {
			$iReturn = $this->addRegionItemGeoCarto($prmRegionItemId, $prmRegionItemGeographyId);
		}
		/*
		if ($iReturn > 0) {
			$iReturn = $this->addRegionItemDisaster($prmRegionItemId,$prmRegionItemGeographyId);
		}
		*/
		if ($iReturn > 0) {
			if ($prmRegionItemGeographyName != '') {
				$Query = "UPDATE Geography SET GeographyName='" . $prmRegionItemGeographyName . "' WHERE GeographyLevel=0 AND GeographyCode='" . $prmRegionItemId . "'";
				$this->q->dreg->query($Query);
			}
		}
		return $iReturn;
	}
	
	public static function getRegionTables() {
		$RegionTables = array('Event','Cause','GeoLevel',
	                          'GeoCarto','Geography','Disaster',
	                          'EEData','EEField','EEGroup');
		return $RegionTables;
	}

	public function clearSyncTable() {
		$this->q->dreg->query("DELETE FROM Sync;");
	}
	


	public function addRegionItemRecord($prmRegionItemId) {
		$iReturn = ERR_NO_ERROR;
		$RegionId = $this->get('RegionId');
		// Add RegionItem record
		$i = new DIRegionItem($this->session, $RegionId, $prmRegionItemId);
		$iReturn = $i->insert();
		return $iReturn;
	}

	public function getRegionItemGeographyId($prmRegionId) {
		$GeographyId = '';
		$g = DIGeography::loadByCode($this->session, $prmRegionId);
		$GeographyId = $g->get('GeographyId');
		if ($GeographyId == '') {
			$GeographyId = $g->buildGeographyId('');
		}
		return $GeographyId;
	}
	
	public function addRegionItemGeography($prmRegionItemId, $prmRegionItemGeographyId) {
		$iReturn = ERR_NO_ERROR;
		$this->setConnection($this->get('RegionId'));
		$q = $this->q->dreg;
		
		
		// Copy Geography From Database
		if ($iReturn > 0) {
			$RegionItemDir = VAR_DIR . '/' . $prmRegionItemId;
			$RegionItemDB = $RegionItemDir . '/desinventar.db';
			// Attach Database
			$Query = "ATTACH DATABASE '" . $RegionItemDB . "' AS RegItem;";
			$q->query($Query);
			$this->copyData($q, 'Geography','GeographyId', $prmRegionItemId, $prmRegionItemGeographyId, false);
			$Query = "DETACH DATABASE RegItem";
			$q->query($Query);
		}
		$this->setConnection('core');
		return $iReturn;
	}
		
	
	public function addRegionItemGeoLevel($prmRegionItemId) {
		$iReturn = ERR_NO_ERROR;
		$RegionItemDir = VAR_DIR . '/' . $prmRegionItemId;
		$RegionItemDB = $RegionItemDir . '/desinventar.db';
		$q = $this->q->dreg;
		// Attach Database
		$Query = "ATTACH DATABASE '" . $RegionItemDB . "' AS RegItem;";
		$q->query($Query);
		// GetCurrentMaxLevel
		$iMaxLevel = 0;
		foreach($q->query('SELECT MAX(GeoLevelId) AS MAXVAL FROM GeoLevel') as $row) {
			$iMaxLevel = $row['MAXVAL'];
		}
		foreach($q->query('SELECT * FROM RegItem.GeoLevel') as $row) {
			if ($iReturn > 0) {
				if (($row['GeoLevelId'] + 1) > $iMaxLevel) {
					$iMaxLevel++;
					$g = new DIGeoLevel($this->session, $iMaxLevel);
					$g->set('GeoLevelName', 'Nivel ' . $iMaxLevel);
					$iReturn = $g->insert();
				}
			}			
		} //foreach
		$Query = "DETACH DATABASE RegItem";
		$q->query($Query);
		return $iReturn;
	}
	
	public function processURL($prmURL) {
		$url = array();
		$i = strpos($prmURL, '://');
		$url['protocol'] = substr($prmURL,0,$i);
		$j = strpos($prmURL, '/', $i+3);
		$url['host'] = substr($prmURL,$i+3,$j-$i-3);
		$url['regionid'] = substr($prmURL, $j+1);
		return $url;
	}
	
	public function rebuildDataDisaster($prmRegionItemId='') {
		$Query = "SELECT * FROM Sync WHERE SyncTable='Disaster'";
		if ($prmRegionItemId != '') {
			$Query .= "AND SyncURL LIKE '%" . $prmRegionItemId . "%'";
		}
		foreach($this->q->dreg->query($Query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$RegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);
			$this->addRegionItemDisaster($RegionItemId, $RegionItemGeographyId);
		}
	}

	public function addRegionItemDisaster($prmRegionItemId,$prmRegionItemGeographyId) {
		$iReturn = ERR_NO_ERROR;
		$RegionDB = VAR_DIR . '/' . $prmRegionItemId . '/desinventar.db';
		$this->q->setDBConnection($this->get('RegionId'));
		$q = $this->q->dreg;
		$q->query("ATTACH DATABASE '" . $RegionDB . "' AS RegItem");
		// Copy Disaster Table, adjust GeographyId Field
		$this->copyData($q, 'Disaster','GeographyId', $prmRegionItemId, $prmRegionItemGeographyId, false);
		// Copy DisasterId from EEData, Other Fields are Ignored...
		$q->query("INSERT INTO EEData (DisasterId) SELECT DisasterId FROM RegItem.EEData");
		$q->query("DETACH DATABASE RegItem");
		return $iReturn;
	}
	
	public function addRegionItemGeoCarto($prmRegionItemId, $prmRegionItemGeographyId) {
		$iReturn = ERR_NO_ERROR;
		$RegionDB = VAR_DIR . '/' . $prmRegionItemId . '/desinventar.db';
		$q = $this->q->dreg;
		$q->query("ATTACH DATABASE '" . $RegionDB . "' AS RegItem");
		$Query = "DELETE FROM GeoCarto WHERE GeographyId='" . $prmRegionItemGeographyId . "'";
		$q->query($Query);
		// Copy GeoCarto Items
		$this->copyData($q, 'GeoCarto','GeographyId',$prmRegionItemId, $prmRegionItemGeographyId, false);
		// Copy SHP,SHX,DBF files from each RegionItem to Region
		$RegionDir     = VAR_DIR . '/' . $this->get('RegionId');
		$RegionItemDir = VAR_DIR . '/' . $prmRegionItemId;
		foreach($this->q->dreg->query('SELECT * FROM RegItem.GeoCarto') as $row) {
			foreach(array('dbf','shp','shx','prj') as $ext) {
				$file0 = $row['GeoLevelLayerFile'] . '.' . $ext;
				$file1 = $RegionItemDir . '/' . $file0;
				$file2 = $RegionDir . '/' . $file0;
				if (file_exists($file1)) {
					copy($file1, $file2);
				}
			}
		}
		$q->query("DETACH DATABASE RegItem");
		$g = new DIGeoCarto($this->session, 0);
		$g->set('GeographyId', $prmRegionItemGeographyId);
		$g->set('RegionId', $prmRegionItemId);
		$g->insert();
		return $iReturn;
	}
	
	public function copyData($prmConn, $prmTable, $prmField, $prmRegionItemId, $prmValue, $isNumeric) {
		$Queries = array();		
		// Create Empty Table
		$Query = "DROP TABLE IF EXISTS TmpTable";
		array_push($Queries, $Query);
		$Query = "CREATE TABLE TmpTable AS SELECT * FROM " . $prmTable . " LIMIT 0";
		array_push($Queries, $Query);
		$Query = "INSERT INTO TmpTable SELECT * FROM RegItem." . $prmTable;
		array_push($Queries, $Query);
		if ($isNumeric) {
			$Query = "UPDATE TmpTable SET " . $prmField . "=" . $prmField . "+1";
		} else {
			$Query = "UPDATE TmpTable SET " . $prmField . "='" . $prmValue . "'||" . $prmField;
		}
		array_push($Queries, $Query);
		if ($prmTable == 'Geography') {
			$Query = "UPDATE TmpTable SET GeographyLevel=GeographyLevel+1";
			array_push($Queries, $Query);
			// Rename GeographyCode to avoid duplicates ???
		}
		if ($prmTable == 'GeoCarto') {
			$Query = "UPDATE TmpTable SET GeoLevelId=GeoLevelId+1";
			array_push($Queries, $Query);
			$Query = "UPDATE TmpTable SET RegionId='" . $prmRegionItemId . "'";
			array_push($Queries, $Query);
		}
		
		if ($isNumeric) {
			$Query = "DELETE FROM " . $prmTable . " WHERE " . $prmField . "=" . ((int)$prmValue + 1);
		} else {
			$Query = "DELETE FROM " . $prmTable . " WHERE " . $prmField . " LIKE '" . $prmValue . "%'";
		}
		//array_push($Queries,$Query);
		$Query = "INSERT INTO " . $prmTable . " SELECT * FROM TmpTable";
		array_push($Queries,$Query);
		foreach ($Queries as $Query) {
			//$this->q->dreg->query($Query);
			try {
				$prmConn->query($Query);
			} catch (Exception $e) {
				showErrorMsg($e->getMessage());
			}
		}
	}
	
	public function copyEvents($prmLangIsoCode) {
		$e = new DIEvent($this->session);
		$FieldList = $e->getFieldList();
		$Queries = array();		
		$Query = "ATTACH DATABASE '" . CONST_DBBASE . "' AS base";
		array_push($Queries, $Query);
		//Copy PreDefined Event List Into Database
		$Query = "DELETE FROM Event WHERE EventPredefined=1 AND LangIsoCode='" . $prmLangIsoCode . "'";
		array_push($Queries, $Query);
		$Query = "INSERT INTO Event(" . $FieldList . ") SELECT " . $FieldList . " FROM base.Event WHERE LangIsoCode='" . $prmLangIsoCode . "'";
		array_push($Queries, $Query);
		$Query = 'DETACH DATABASE base';
		array_push($Queries, $Query);
		foreach($Queries as $Query) {
			$this->q->dreg->query($Query);
		}
	}

	public function copyCauses($prmLangIsoCode) {
		$c = new DICause($this->session);
		$FieldList = $c->getFieldList();
		$Queries = array();		
		$Query = "ATTACH DATABASE '" . CONST_DBBASE . "' AS base";
		array_push($Queries, $Query);
		//Copy PreDefined Cause List Into Database
		$Query = "DELETE FROM Cause WHERE CausePredefined=1 AND LangIsoCode='" . $prmLangIsoCode . "'";
		array_push($Queries, $Query);
		$Query = "INSERT INTO Cause (" . $FieldList . ") SELECT " . $FieldList . " FROM base.Cause WHERE LangIsoCode='" . $prmLangIsoCode . "'";
		array_push($Queries, $Query);
		$Query = 'DETACH DATABASE base';
		array_push($Queries, $Query);
		foreach($Queries as $Query) {
			$this->q->dreg->query($Query);
		}
	}

	public function createCRegion($prmGeoLevelName) {
		// Set Information about this CRegion, Creates GeoLevel=0
		$iReturn = ERR_NO_ERROR;
		$this->set('IsCRegion', 1);
		$g = new DIGeoLevel($this->session, 0, $this->get('LangIsoCode'), $prmGeoLevelName);
		// Warning : Delete All GeoLevels with this...
		$g->conn->query("DELETE FROM GeoLevel");
		$iReturn = $g->insert();
		return $iReturn;
	}
	
	public function getGeoLevelCount() {
		$iAnswer = 0;
		$g = new DIGeoLevel($this->session, 0);
		$iAnswer = $g->getMaxGeoLevel();
		return $iAnswer;
	}
	
	public function getDBInfoValue($prmInfoKey) {
		return $this->q->getDBInfoValue($prmInfoKey);
	}
	
	public function updateMapArea() {
		$iReturn = ERR_NO_ERROR;
		$IsCRegion = $this->get('IsCRegion');
		if ($IsCRegion > 0) {
			$iReturn = ERR_NO_ERROR;
			$MinX = 180; $MaxX = -180;
			$MinY =  90; $MaxY = -90;
			// Use information about each RegionItem to Calcule the Map Area
			$Query = "SELECT * FROM RegionItem WHERE RegionId='" . $this->get('RegionId') . "'";
			foreach ($this->q->core->query($Query) as $row) {
				$RegionItemId = $row['RegionItem'];
				$r = new DIRegion($this->session, $RegionItemId);
				$ItemMinX = $r->getDBInfoValue('GeoLimitMinX');
				if ($ItemMinX < $MinX) { $MinX = $ItemMinX; }
				$ItemMaxX = $r->getDBInfoValue('GeoLimitMaxX');
				if ($ItemMaxX > $MaxX) { $MaxX = $ItemMaxX; }
				$ItemMinY = $r->getDBInfoValue('GeoLimitMinY');
				if ($ItemMinY < $MinY) { $MinY = $ItemMinY; }
				$ItemMaxY = $r->getDBInfoValue('GeoLimitMaxY');
				if ($ItemMaxY > $MaxY) { $MaxY = $ItemMaxY; }
				$r = null;
			} //foreach
			$this->q->setDBConnection($this->get('RegionId'));
			if ($iReturn > 0) {
				$this->set('GeoLimitMinX', $MinX);
				$this->set('GeoLimitMaxX', $MaxX);
				$this->set('GeoLimitMinY', $MinY);
				$this->set('GeoLimitMaxY', $MaxY);
				$this->update();
			}
		} //if
	} //updateMapArea
	
	public static function buildRegionId($prmCountryIso, $prmRegionLabel) {
		$RegionId = '';
		if ($prmCountryIso == '') {
			$prmCountryIso = 'DESINV';
		}
		$Timestamp = DIObject::padNumber(time(),10);
		//$prmRegionLabel = 'Región de Prueba ññáéíóúÓÚ';
		$prmRegionLabel = strtolower($prmRegionLabel);
		$prmRegionLabel = str_replace(' - ','_',$prmRegionLabel);
		$prmRegionLabel = str_replace(' ','_',$prmRegionLabel);
		$prmRegionLabel = str_replace(' ','',$prmRegionLabel);
		$prmRegionLabel = str_replace(array('ñ','á','é','í','ó','ú','Á','É','Í','Ó','Ú'),
		                              array('n','a','e','i','o','u','a','e','i','o','u'),
		                              $prmRegionLabel);
		$prmRegionLabel = substr($prmRegionLabel, 0, 60);
		$RegionId = $prmCountryIso . '-' . $Timestamp . '-' . $prmRegionLabel;
		return $RegionId;
	} //buildRegionId
	
	public function setActive($prmValue) {
		return $this->setBit($prmValue, CONST_REGIONACTIVE);
	}
	public function setPublic($prmValue) {
		return $this->setBit($prmValue, CONST_REGIONPUBLIC);
	}
	public function setBit($prmValue, $prmBit) {
		$Value = (int)$this->get('RegionStatus');
		if ($prmValue > 0) {
			$Value = $Value | $prmBit;
		} else {
			$Value = $Value & ~$prmBit;
		}
		$this->set('RegionStatus', $Value);
	}
	
} //class

</script>
