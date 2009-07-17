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
		                      "RegionStatus/INTEGER";
		$this->sInfoDef     = "DBVersion/STRING," .
		                      "RegionOrder/INTEGER," .
		                      "RegionLastUpdate/DATETIME," .
		                      "IsCRegion/INTEGER," .
		                      "IsVRegion/INTEGER," .
		                      "I18NFirstLang/STRING," .
		                      "I18NSecondLang/STRING," .
		                      "I18NThirdLang/STRING," .
		                      "PeriodBeginDate/DATE," .
		                      "PeriodEndDate/DATE," .
		                      "PeriodOutOfRange/INTEGER," .
		                      "InfoCredits/STRING," . 
		                      "InfoGeneral/STRING," . 
		                      "InfoSources/STRING," .
		                      "InfoSynopsis/STRING," . 
		                      "InfoObservation/STRING," . 
		                      "InfoGeography/STRING," . 
		                      "InfoCartography/STRING," .
		                      "InfoAdminURL/STRING," . 
		                      "GeoLimitMinX/DOUBLE," . 
		                      "GeoLimitMinY/DOUBLE," . 
		                      "GeoLimitMaxX/DOUBLE," . 
		                      "GeoLimitMaxY/DOUBLE," . 
		                      "Sync_Info/DATETIME," . 
		                      "Sync_Event/DATETIME," . 
		                      "Sync_Cause/DATETIME," . 
		                      "Sync_GeoLevel/DATETIME," . 
		                      "Sync_GeoCarto/DATETIME," . 
		                      "Sync_Geography/DATETIME," . 
		                      "Sync_Disaster/DATETIME," .
		                      "Sync_EEField/DATETIME," . 
		                      "Sync_EEData/DATETIME," . 
		                      "Sync_EEGroup/DATETIME," . 
		                      "Sync_DatabaseLog/DATETIME";
		parent::__construct($prmSession);
		$num_args = func_num_args();
		$this->set('LangIsoCode', 'spa');
		$this->setConnection("core");
		$this->createFields($this->sInfoDef);
		if ($num_args >= 2) {
			$prmRegionId = func_get_arg(1);
			if ($prmRegionId != '') {
				$this->set('RegionId', $prmRegionId);
				$this->q->setDBConnection($prmRegionId);
			}
			$this->load();
		}
	} // __construct

	public function loadInfo() {
		$iReturn = 1;
		$iReturn = $this->q->setDBConnection($this->get('RegionId'));
		if ($iReturn > 0) {
			$this->setConnection($this->get('RegionId'));
			try {
				foreach($this->oField as $k => $v) {
					$sQuery = "SELECT * FROM Info WHERE InfoKey='" . $k . "'";
					foreach($this->conn->query($sQuery) as $row) {
						$Value = $row['InfoValue'];
						$sFieldType = $this->oFieldType[$k];
						if ($sFieldType == 'DATETIME') {
							if ($Value == '') { $Value = $v; }
						}
						$this->set($k, $Value);
					} //foreach row
				} // foreach field
			} catch (Exception $e) {
				$iReturn = ERR_NO_DATABASE;
			}
			$this->setConnection('core');
		}
		return $iReturn;
	}
	
	public function	saveInfo() {
		$iReturn = 1;
		$now = gmdate('c');
		$this->setConnection($this->get('RegionId'));
		foreach($this->oField as $k => $v) {
			$sQuery = "DELETE FROM Info WHERE InfoKey='" . $k . "'";
			$this->conn->query($sQuery);
			$sQuery = "INSERT INTO Info VALUES ('" . $k . "','" . $now . "','" . $v . "','')";
			$this->conn->query($sQuery);
		}
		$this->setConnection('core');
		return $iReturn;
	}

	public function load() {
		$iReturn = parent::load();
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
	
	public function createRegionDB($prmRegionId) {
		$iReturn = 1;
		// Create Directory for New Region
		$DBDir = VAR_DIR . '/' . $prmRegionId . '/';
		try {
			if (!file_exists($DBDir)) {
				mkdir($DBDir);
			}
			if (file_exists(CONST_DBREGION)) {
				$iReturn = copy(CONST_DBREGION, $DBDir . '/desinventar.db');
				$this->q->setDBConnection($this->get('RegionId'));
			}
		} catch (Exception $e) {
			showErrorMsg("Error " . $e->getMessage());
		}
		$this->set('RegionId', $prmRegionId);
		// Copy Predefined Event/Cause Lists
		$this->copyEvents($this->get('LangIsoCode'));
		$this->copyCauses($this->get('LangIsoCode'));
		return $iReturn;
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
		$g = new DIGeography($this->session, $prmRegionId);
		$GeographyId = $g->buildGeographyId('');
		return $GeographyId;
	}
	
	public function addRegionItemGeography($prmRegionItemId, $prmRegionItemGeographyId) {
		$iReturn = ERR_NO_ERROR;
		$this->setConnection($this->get('RegionId'));
		$q = $this->q->dreg;
		
		// Delete Existing Elements
		$Query = "DELETE FROM Geography WHERE GeographyCode='" . $prmRegionItemId . "'";
		$q->query($Query);
		
		$g = new DIGeography($this->session, 
		                     $prmRegionItemGeographyId,
		                     $this->get('LangIsoCode'));
		$g->set('GeographyCode', $prmRegionItemId);
		$g->set('GeographyName', 'Region ' . (int)$prmRegionItemGeographyId);
		$iReturn = $g->insert();
		
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
		
	public function addRegionItem($prmRegionItemId, $prmRegionItemGeographyId) {
		$RegionId = $this->get('RegionId');
		$RegionDir = VAR_DIR . '/' . $this->get('RegionId');
		$RegionItemDir = VAR_DIR . '/' . $prmRegionItemId;
		$RegionItemDB = $RegionItemDir . '/desinventar.db';
		$iReturn = 1;
		if ($prmRegionItemId == '') {
			$iReturn = -1;
		}
		if ($iReturn > 0) {
			if (!file_exists($RegionItemDB)) {
				$iReturn = -2;
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
		if ($iReturn > 0) {
			$iReturn = $this->addRegionItemDisaster($prmRegionItemId);
		}
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
	
	public function addRegionItemDisaster($prmRegionItemId,$prmRegionItemGeographyId) {
		$RegionDB = VAR_DIR . '/' . $prmRegionItemId . '/desinventar.db';
		$q = $this->q->dreg;
		$q->query("ATTACH DATABASE '" . $RegionDB . "' AS RegItem");
		// Copy Disaster Table, adjust GeographyId Field
		$this->copyData($q, 'Disaster','DisasterGeographyId', $prmRegionItemId, $prmRegionItemGeographyId, false);
		// Copy DisasterId from EEData, Other Fields are Ignored...
		$q->query("INSERT INTO EEData (DisasterId) SELECT DisasterId FROM RegItem.EEData");
		$q->query("DETACH DATABASE RegItem");
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
		$Queries = array();		
		$Query = "ATTACH DATABASE '" . CONST_DBBASE . "' AS base";
		array_push($Queries, $Query);
		//Copy PreDefined Event List Into Database
		$Query = "DELETE FROM Event WHERE EventPredefined=1 AND LangIsoCode='" . $prmLangIsoCode . "'";
		array_push($Queries, $Query);
		$Query = "INSERT INTO Event SELECT * FROM base.Event WHERE LangIsoCode='" . $prmLangIsoCode . "'";
		array_push($Queries, $Query);
		$Query = 'DETACH DATABASE base';
		array_push($Queries, $Query);
		foreach($Queries as $Query) {
			$this->q->dreg->query($Query);
		}
	}

	public function copyCauses($prmLangIsoCode) {
		$Queries = array();		
		$Query = "ATTACH DATABASE '" . CONST_DBBASE . "' AS base";
		array_push($Queries, $Query);
		//Copy PreDefined Cause List Into Database
		$Query = "DELETE FROM Cause WHERE CausePredefined=1 AND LangIsoCode='" . $prmLangIsoCode . "'";
		array_push($Queries, $Query);
		$Query = "INSERT INTO Cause SELECT * FROM base.Cause WHERE LangIsoCode='" . $prmLangIsoCode . "'";
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
		$iReturn = 0;
		$g = new DIGeoLevel($this->session, 0);
		$iReturn = $g->getMaxGeoLevel();
		return $iReturn;
	}
	
} //class

</script>
