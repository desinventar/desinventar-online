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
			$this->loadInfo();
			$this->load();
		}
	} // __construct

	public function loadInfo() {
		foreach($this->oField as $k => $v) {
			$sQuery = "SELECT * FROM Info WHERE InfoKey='" . $k . "'";
			foreach($this->q->dreg->query($sQuery) as $row) {
				$Value = $row['InfoValue'];
				$sFieldType = $this->oFieldType[$k];
				if ($sFieldType == 'DATETIME') {
					if ($Value == '') { $Value = $v; }
				}
				$this->set($k, $Value);
			} //foreach row
		} // foreach field
	}
	
	public function	saveInfo() {
		$iReturn = 1;
		$now = gmdate('c');
		$this->q->setDBConnection($this->get('RegionId'));
		foreach($this->oField as $k => $v) {
			$sQuery = "DELETE FROM Info WHERE InfoKey='" . $k . "'";
			$this->q->dreg->query($sQuery);
			$sQuery = "INSERT INTO Info VALUES ('" . $k . "','" . $now . "','" . $v . "','')";
			$this->q->dreg->query($sQuery);
		}
		$this->q->setDBConnection('core');
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
	
} //class

</script>
