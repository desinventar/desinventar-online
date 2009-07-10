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
			}
			$this->load();
		}
	} // __construct

	public function loadInfo() {
		$iReturn = 1;
		$this->setConnection($this->get('RegionId'));
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
		$this->setConnection('core');
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
			}
		} catch (Exception $e) {
			print "Error " . $e->getMessage() . "<br />";
		}
		$this->set('RegionId', $prmRegionId);
		return $iReturn;
	}
	
	public function addRegionItem($prmRegionItemId) {
		$RegionId = $this->get('RegionId');
		$RegionItemDB = VAR_DIR . '/' . $prmRegionItemId . '/desinventar.db';
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
			$i = new DIRegionItem($this->session, $RegionId, $prmRegionItemId);
			//$iReturn = $i->insert();
		}
		if ($iReturn > 0) {
			// Add Geography to Level0
			$g = new DIGeography($this->session);
			$g->setGeographyId('');
			$GeographyId = $g->get('GeographyId');
			$g->set('GeographyCode', $prmRegionItemId);
			$g->set('GeographyName', $prmRegionItemId);
			$g->insert();
			//$iReturn = $g->insert();
			//print $g->getInsertQuery() . "<br />";
			//print $g->getUpdateQuery() . "<br />";
		}
		if ($iReturn > 0) {
			// Attach Database
			$Query = "ATTACH DATABASE '" . $RegionItemDB . "' AS RegItem;";
			$this->q->dreg->query($Query);
			// Copy Geography Items
			//$this->copyData('GeoLevel','GeoLevelId', '0', true);
			$this->copyData('Geography','GeographyId', $GeographyId, false);
			//$g->insert();
			$this->copyData('Disaster','DisasterGeographyId', $GeographyId, false);
			$Query = "DETACH DATABASE RegItem";
			$this->q->dreg->query($Query);
		}
		return $iReturn;
	}
	
	public function copyData($prmTable, $prmField, $prmValue, $isNumeric) {
		// Create Empty Table
		$Query = "DROP TABLE IF EXISTS TmpTable";
		$this->q->dreg->query($Query);
		$Query = "CREATE TABLE TmpTable AS SELECT * FROM " . $prmTable . " LIMIT 0";
		$this->q->dreg->query($Query);
		$Query = "INSERT INTO TmpTable SELECT * FROM RegItem." . $prmTable;
		$this->q->dreg->query($Query);
		if ($isNumeric) {
			$Query = "UPDATE TmpTable SET " . $prmField . "=" . $prmField . "+1";
		} else {
			$Query = "UPDATE TmpTable SET " . $prmField . "='" . $prmValue . "'||" . $prmField;
		}
		$this->q->dreg->query($Query);
		if ($prmTable == 'Geography') {
			$Query = "UPDATE TmpTable SET GeographyLevel=GeographyLevel+1";
			$this->q->dreg->query($Query);
		}
		if ($isNumeric) {
			$Query = "DELETE FROM " . $prmTable . " WHERE " . $prmField . "=" . ((int)$prmValue + 1);
		} else {
			$Query = "DELETE FROM " . $prmTable . " WHERE " . $prmField . " LIKE '" . $prmValue . "%'";
		}
		//$this->q->dreg->query($Query);
		$Query = "INSERT INTO " . $prmTable . " SELECT * FROM TmpTable";
		$this->q->dreg->query($Query);
	}
} //class

</script>
