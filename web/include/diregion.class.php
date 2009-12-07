<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
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
		$this->setConnection("core");
		$this->createFields($this->sInfoDef);		
		$this->addLanguageInfo('eng');
		$this->set('PeriodBeginDate', '1900-01-01');
		$this->set('PeriodEndDate', gmdate('Y-m-d'));

		$prmRegionId = '';
		$num_args = func_num_args();
		if ($num_args >= 2) {
			// Load region if parameter was specified
			$prmRegionId = func_get_arg(1);
		} else {
			// Try to load region from Current Session if no parameter was specified
			if ( ($prmSession->sRegionId != '') && ($prmSession->sRegionId != 'core')) {
				$prmRegionId = $prmSession->sRegionId;
			}
		}
		$iReturn = ERR_NO_ERROR;
		if ($prmRegionId != '') {
			$this->set('RegionId', $prmRegionId);
			$iReturn = $this->q->setDBConnection($prmRegionId);
		}
		if ($iReturn > 0) {
			$iReturn = $this->load();
			// Load eng Info
			$iReturn = $this->loadInfoTrans('eng');
			$LangIsoCode = $this->get('LangIsoCode');
			if ($LangIsoCode != 'eng') {
				$this->addLanguageInfo($LangIsoCode);
				$iReturn = $this->loadInfoTrans($LangIsoCode);
			}
		}
		if ($this->get('OptionLanguageList') == '') {
			$this->set('OptionLanguageList', $this->get('LangIsoCode'));
		}
	} // __construct
	
	public function setLanguage($LangIsoCode) {
		$this->set('LangIsoCode', $LangIsoCode);
		$this->addLanguageInfo($LangIsoCode);
	}

	public function addLanguageInfo($LangIsoCode) {
		$this->createFields($this->sInfoTrans, $LangIsoCode);
	}
	
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
				$sQuery = "SELECT * FROM Info WHERE LangIsoCode='' AND Length(InfoKey) > 3";
				foreach($this->conn->query($sQuery) as $row) {
					$InfoValue = $row['InfoValue'];
					$InfoKey   = $row['InfoKey'];
					$this->set($InfoKey, $InfoValue);
				} // foreach
			} catch (Exception $e) {
				$iReturn = ERR_NO_DATABASE;
			}
			$this->setConnection('core');
			$this->loadInfoTrans($this->get('LangIsoCode'));
		}
		return $iReturn;
	}

	public function loadInfoTrans($prmLangIsoCode) {
		$iReturn = ERR_NO_DATABASE;
		$RegionId = $this->get('RegionId');
		if ($RegionId != 'core') {
			$iReturn = ERR_NO_ERROR;
			$iReturn = $this->q->setDBConnection($this->get('RegionId'));
			if ($iReturn > 0) {
				$this->setConnection($this->get('RegionId'));
				try {
					$sQuery = "SELECT * FROM Info WHERE LangIsoCode='" . $prmLangIsoCode . "'";
					foreach($this->conn->query($sQuery) as $row) {
						$InfoValue = $row['InfoValue'];
						$InfoKey   = $row['InfoKey'];
						$this->set($InfoKey, $InfoValue,$prmLangIsoCode);
					} // foreach
				} catch (Exception $e) {
					$iReturn = ERR_NO_DATABASE;
				}
				$this->setConnection('core');
			}
		}
		return $iReturn;
	}
	
	public function	saveInfo() {
		$iReturn = ERR_NO_ERROR;
		$now = gmdate('c');
		$this->setConnection($this->get('RegionId'));
		$Translatable = $this->getTranslatableFields();
		foreach($this->oField as $Key => $Value) {
			if ($iReturn > 0) {
				foreach($this->oField[$Key] as $InfoKey => $InfoValue) {
					if ($iReturn > 0) {
						$LangIsoCode = $Key;
						if (strlen($LangIsoCode) > 3) {
							$LangIsoCode = '';
						}
						$sQuery = "DELETE FROM Info WHERE InfoKey='" . $InfoKey . "' AND LangIsoCode='" . $LangIsoCode . "'";
						try {
							$this->conn->query($sQuery);
						} catch (Exception $e) {
							showErrorMsg("Error " . $e->getMessage());
							$iReturn = ERR_UNKNOWN_ERROR;
						}
						$sQuery = "INSERT INTO Info VALUES ('" . $InfoKey . "','" . $LangIsoCode . "','" . $InfoValue . "','','" . $now . "','" . $now . "','" . $now . "')";
						try {
							$this->conn->query($sQuery);
						} catch (Exception $e) {
							showErrorMsg("Error " . $e->getMessage());
							$iReturn = ERR_UNKNOWN_ERROR;
						}
					 } //if
				} //foreach
			} //if
		} //foreach
		$this->setConnection('core');
		//$this->saveInfoTrans('eng');
		//$this->saveInfoTrans($this->get('LangIsoCode'));
		return $iReturn;
	}
	
	public function saveInfoTrans($prmLangIsoCode) {
		$iReturn = ERR_NO_ERROR;
		$now = gmdate('c');
		$this->setConnection($this->get('RegionId'));
		$Translatable = $this->getTranslatableFields();
		foreach($this->oField[$prmLangIsoCode] as $InfoKey => $InfoValue) {
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
			$this->setConnection('core');
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
		$DBDir = VAR_DIR . '/database/' . $prmRegionId;
		$DBFile = $DBDir . '/desinventar.db';
		$this->q->dreg = null;
		try {
			if (!file_exists($DBDir)) {
				mkdir($DBDir);
			}
			if (file_exists(CONST_DBNEWREGION)) {
				// Backup previous desinventar.db if exists
				if (file_exists($DBFile)) {
					$DBFile2 = $DBFile . '.bak';
					if (file_exists($DBFile2)) {
						unlink($DBFile2);
					}
					rename($DBFile, $DBFile2);
				}
				$iReturn = copy(CONST_DBNEWREGION, $DBFile);
			}
		} catch (Exception $e) {
			showErrorMsg("Error " . $e->getMessage());
		}
		$this->session->q->setDBConnection($this->get('RegionId'));
		// Delete all database records
		$this->clearRegionTables();
		$this->set('RegionId', $prmRegionId);
		if ($iReturn > 0) {
			// Copy Predefined Event/Cause Lists
			$LangIsoCode = $this->get('LangIsoCode');
			$this->copyEvents($LangIsoCode);
			$this->copyCauses($LangIsoCode);
		}

		if ($iReturn > 0) {
			// Insert Data Into core.Region, create Info Table
			$this->insert();
		}
		if ($iReturn > 0) {
			// Calculate Name of GeoLevel 0
			if ($prmGeoLevelName == '') {
				$prmGeoLevelName = 'Level 0';
			}
			$g = new DIGeoLevel($this->session, 0);
			$g->set('GeoLevelName', $prmGeoLevelName);
			$g->set('RegionId', $this->get('RegionId'));
			$c = new DIGeoCarto($this->session);
			$c->set('GeoLevelId', 0);
			if ($g->exist() > 0) {
				$g->update();
				$c->update();
			}
			else {
				$g->insert();
				$c->insert();
			}
		}
		return $iReturn;
	}

	public function addRegionItem($prmRegionItemId, $prmRegionItemGeographyName, $prmRegionItemGeographyId='') {
		$iReturn = ERR_NO_ERROR;
		
		$RegionId = $this->get('RegionId');

		if ($prmRegionItemGeographyId == '') {
			$prmRegionItemGeographyId = $this->getRegionItemGeographyId($prmRegionItemId);
		}
		$this->addRegionItemSync($prmRegionItemId);

		// Create Geography element at GeographyLevel=0 for this RegionItem
		// Delete Existing Elements
		$query = "DELETE FROM Geography WHERE GeographyCode='" . $prmRegionItemId . "'";
		$this->q->dreg->query($query);
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
			$s->set('RegionId', $this->get('RegionId'));
			$s->set('SyncURL', "file:///" . $prmRegionItemId);
			$s->insert();
		} //foreach
	}

	public function clearRegionTables() {
		// Delete ALL Record from Database - Be Careful...
		foreach($this->getRegionTables() as $TableName) {
			$query = "DELETE FROM " . $TableName;
			$this->q->dreg->query($query);
		} //foreach
	}
	
	public function addRegionItem2($prmRegionItemId, $prmRegionItemGeographyName, $prmRegionItemGeographyId='') {
		$RegionId = $this->get('RegionId');
		$RegionDir = VAR_DIR . '/database/' . $this->get('RegionId');
		$RegionItemDir = VAR_DIR . '/database/' . $prmRegionItemId;
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
	
	public function attachQuery($prmRegionId, $prmName) {
		$RegionItemDir = VAR_DIR . '/database/' . $prmRegionId;
		$RegionItemDB = $RegionItemDir . '/desinventar.db';
		$query = "ATTACH DATABASE '" . $RegionItemDB . "' AS " . $prmName;
		return $query;
	}
	public function detachQuery($prmName) {
		$query = "DETACH DATABASE " . $prmName;
		return $query;
	}
	public function addRegionItemGeography($prmRegionItemId, $prmRegionItemGeographyId) {
		$iReturn = ERR_NO_ERROR;
		$this->setConnection($this->get('RegionId'));
		$q = $this->q->dreg;
		
		// Copy Geography From Database
		if ($iReturn > 0) {
			$RegionItemDir = VAR_DIR . '/database/' . $prmRegionItemId;
			$RegionItemDB = $RegionItemDir . '/desinventar.db';
			// Attach Database
			$q->query($this->attachQuery($prmRegionItemId,'RegItem'));
			$this->copyData($q, 'Geography','GeographyId', $prmRegionItemId, $prmRegionItemGeographyId, false);
			$q->query($this->detachQuery());
		}
		$this->setConnection('core');
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
	
	public function rebuildEventData() {
		$this->copyEvents($this->get('LangIsoCode'));
		$this->q->dreg->query("DELETE FROM Event WHERE EventPredefined=0");
		$o = new DIEvent($this->session);
		$query = "SELECT * FROM Sync WHERE SyncTable='Event'";
		foreach($this->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));
			foreach($this->q->dreg->query("SELECT * FROM RegItem.Event WHERE EventPredefined=0") as $row) {
				$o->setFromArray($row);
				$o->insert();
			}
			$this->q->dreg->query($this->detachQuery('RegItem'));
		}
	}

	public function rebuildCauseData() {
		$this->copyCauses($this->get('LangIsoCode'));
		$this->q->dreg->query("DELETE FROM Cause WHERE CausePredefined=0");
		$o = new DICause($this->session);
		$query = "SELECT * FROM Sync WHERE SyncTable='Cause'";
		foreach($this->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));
			foreach($this->q->dreg->query("SELECT * FROM RegItem.Cause WHERE CausePredefined=0") as $row) {
				$o->setFromArray($row);
				$o->insert();
			}
			$this->q->dreg->query($this->detachQuery('RegItem'));
		}
	}

	public function rebuildGeographyData() {
		$iReturn = ERR_NO_ERROR;
		
		// Delete existing Geography except for Level0 in Virtual Region
		$query = "DELETE FROM Geography WHERE GeographyLevel>0";
		$this->q->dreg->query($query);
		
		$list = array();
		$query = "SELECT * FROM Sync WHERE SyncTable='Geography'";
		foreach($this->q->dreg->query($query) as $row) {
			$list[] = $row['SyncURL'];
		}
		
		foreach($list as $SyncURL) {
			$url = $this->processURL($SyncURL);
			$RegionItemId = $url['regionid'];
			$prmRegionItemId = $RegionItemId;
			$prmRegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

			// Attach Database
			$this->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			// Copy Geography From Database
			$this->copyData($this->q->dreg, 'Geography','GeographyId', $prmRegionItemId, $prmRegionItemGeographyId, false);
			
			// Update GeographyFQName in child nodes
			$g = new DIGeography($this->session, $prmRegionItemGeographyId);
			$GeographyFQName = $g->get('GeographyFQName');
			$query = 'UPDATE Geography SET GeographyFQName="' . $GeographyFQName . '/' . '"||GeographyFQName WHERE GeographyLevel>0 AND GeographyId LIKE "' . $prmRegionItemGeographyId . '%"';
			$this->q->dreg->query($query);

			$this->q->dreg->query($this->detachQuery('RegItem'));
		}
		return $iReturn;
	}

	public function rebuildGeoLevelData() {
		$iReturn = ERR_NO_ERROR;
		$this->q->dreg->query("DELETE FROM GeoLevel WHERE GeoLevelId>0");
		$query = "SELECT * FROM Sync WHERE SyncTable='Cause'";
		foreach($this->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			// GetCurrentMaxLevel
			$iMaxLevel = 0;
			foreach($this->q->dreg->query('SELECT MAX(GeoLevelId) AS MAXVAL FROM GeoLevel') as $row) {
				$iMaxLevel = $row['MAXVAL'];
			}
			foreach($this->q->dreg->query('SELECT * FROM RegItem.GeoLevel') as $row) {
				if ($iReturn > 0) {
					if (($row['GeoLevelId'] + 1) > $iMaxLevel) {
						$iMaxLevel++;
						$g = new DIGeoLevel($this->session, $iMaxLevel);
						$g->set('GeoLevelName', 'Nivel ' . $iMaxLevel);
						$iReturn = $g->insert();
					}
				}			
			} //foreach
			$this->q->dreg->query($this->detachQuery('RegItem'));
		}
		return $iReturn;
	}

	public function rebuildGeoCartoData() {
		$iReturn = ERR_NO_ERROR;
		$query = "SELECT * FROM Sync WHERE SyncTable='GeoCarto'";
		$list = array();
		foreach($this->q->dreg->query($query) as $row) {
			$list[] = $row['SyncURL'];
		}
		
		foreach($list as $SyncURL) {
			$url = $this->processURL($SyncURL);
			$RegionItemId = $url['regionid'];
			$prmRegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

			$query = "DELETE FROM GeoCarto WHERE GeographyId='" . $prmRegionItemGeographyId . "'";
			$this->q->dreg->query($query);
			
			// Attach Database
			$this->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			// Copy GeoCarto Items
			$this->copyData($this->q->dreg, 'GeoCarto','GeographyId',$RegionItemId, $prmRegionItemGeographyId, false);
			// Copy SHP,SHX,DBF files from each RegionItem to Region
			$RegionDir     = VAR_DIR . '/database/' . $this->get('RegionId');
			$RegionItemDir = VAR_DIR . '/database/' . $RegionItemId;
			foreach($this->q->dreg->query('SELECT * FROM RegItem.GeoCarto') as $row) {
				foreach(array('dbf','shp','shx','prj') as $ext) {
					$file0 = $row['GeoLevelLayerFile'] . '.' . $ext;
					$file1 = $RegionItemDir . '/' . $file0;
					$file2 = $RegionDir . '/' . $file0;
					if (file_exists($file1)) {
						copy($file1, $file2);
					}
				} //foreach
			} //foreach
			
			$g = new DIGeoCarto($this->session, 0);
			$g->set('GeographyId', $prmRegionItemGeographyId);
			$g->set('RegionId', $RegionItemId);
			$g->insert();

			$this->q->dreg->query($this->detachQuery('RegItem'));
		}
		return $iReturn;
	}
	
	public function rebuildDisasterData($prmRegionItemId='') {
		$iReturn = ERR_NO_ERROR;
		$query = "SELECT * FROM Sync WHERE SyncTable='Disaster'";
		if ($prmRegionItemId != '') {
			$query .= "AND SyncURL LIKE '%" . $prmRegionItemId . "%'";
		}
		$list = array();
		foreach($this->q->dreg->query($query) as $row) {
			$list[] = $row['SyncURL'];
		}
		
		$query = "DELETE FROM Disaster";
		$this->q->dreg->query($query);
		$query = "DELETE FROM EEData";
		$this->q->dreg->query($query);
		
		foreach($list as $SyncURL) {
			$url = $this->processURL($SyncURL);
			$RegionItemId = $url['regionid'];
			$RegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

			// Attach Database
			$this->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			// Copy Disaster Table, adjust GeographyId Field
			$this->copyData($this->q->dreg, 'Disaster','GeographyId', $RegionItemId, $RegionItemGeographyId, false);
			
			// Delete Non Published Data cards
			$this->q->dreg->query("DELETE FROM Disaster WHERE RecordStatus<>'PUBLISHED'");
			
			// Copy DisasterId from EEData, Other Fields are Ignored...
			$this->q->dreg->query("INSERT INTO EEData (DisasterId) SELECT DisasterId FROM Disaster WHERE GeographyId LIKE '" . $RegionItemGeographyId . "%'");

			$this->q->dreg->query($this->detachQuery('RegItem'));
		}
	}
	
	public function rebuildRegionData() {
		$this->rebuildEventData();
		$this->rebuildCauseData();
		$this->rebuildGeoLevelData();
		$this->rebuildGeographyData();
		$this->rebuildGeoCartoData();
		$this->rebuildDisasterData();
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
	
	public function copyEvents($prmLangIsoCode='') {
		if ($prmLangIsoCode == '') {
			$prmLangIsoCode = $this->get('LangIsoCode');
		}
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

	public function copyCauses($prmLangIsoCode='') {
		if ($prmLangIsoCode == '') {
			$prmLangIsoCode = $this->get('LangIsoCode');
		}
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
	
	// Read an specific InfoKey value from the table
	public function getDBInfoValue($prmInfoKey, $LangIsoCode) {
		$sReturn = '';
		if ($this->q->dreg != null) {
			$query = "SELECT * FROM Info WHERE InfoKey='" . $prmInfoKey . "'";
			if ($prmInfoKey != 'LangIsoCode') {
				$query .= " AND LangIsoCode='" . $LangIsoCode . "'";
			}
			foreach($this->q->dreg->query($query) as $row) {
				$sReturn = $row['InfoValue'];
			}
		} //if
		return $sReturn;
	} //function

	public function saveDBInfoValue($prmInfoKey, $LangIsoCode, $prmInfoValue, $prmInfoAuxValue = '') {
		$iReturn = ERR_NO_ERROR;
		$now = gmdate('c');
		if ($this->q->dreg != null) {
			$query = "DELETE FROM Info WHERE InfoKey='" . $prmInfoKey . "'" . " AND LangIsoCode='" . $LangIsoCode . "'";
			$this->q->dreg->query($query);
			$query = sprintf("INSERT INTO Info (InfoKey,LangIsoCode,InfoValue,InfoAuxValue,RecordCreation,RecordUpdate,RecordSync) VALUES ('%s','%s','%s','%s','%s','%s','%s')",
			         $prmInfoKey,$LangIsoCode,$prmInfoValue,$prmInfoAuxValue,$now,$now,$now);
		}
		return $iReturn;
	}

	public function getLanguageList() {
		$LangIsoCode = $this->get('LangIsoCode');
		$ll = array('eng'=>'eng');
		if ($LangIsoCode != 'eng') {
			$ll[$LangIsoCode] = $LangIsoCode;
		}
		return $ll;
	}
		
	public function getDBInfo($prmLang = 'eng') {
		$a = array();
		foreach(array('RegionId','RegionLabel', 'PeriodBeginDate','PeriodEndDate',
		              'RegionLastUpdate') as $Field) {
			$a[$Field] = $this->get($Field);
		}
		$ll = $this->getLanguageList();
		if (!array_key_exists($prmLang, $ll)) {
			$prmLang = 'eng';
		}
		foreach(array('InfoGeneral','InfoCredits','InfoSources','InfoSynopsis') as $Field) {
			$a[$Field] = $this->get($Field, $prmLang);
		}
		$a['RegionLastUpdate'] = substr($a['RegionLastUpdate'],0,10);
		return $a;
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
	
	public static function renameRegion($oldRegionId, $newRegionId) {
		
	}
	
	public static function rebuildRegionListFromDirectory($us, $prmDir = '') {
		if ($prmDir == '') {
			$prmDir = CONST_DBREGIONDIR;
		}
		// ADMINREG: Create database list from directory
		$dbb = dir($prmDir);
		while (false !== ($Dir = $dbb->read())) {
			if (($Dir != '.') && ($Dir != '..')) {
				DIRegion::createRegionEntryFromDir($us, $Dir);
			}
		}
		$dbb->close();
	}

	public static function createRegionEntryFromDir($us, $dir, $reglabel) {
		$iReturn = ERR_NO_ERROR;
		$regexist = $us->q->checkExistsRegion($dir);
		$regexist = 0;
		$difile = CONST_DBREGIONDIR . '/' . $dir ."/desinventar.db";
		if (strlen($dir) >= 4 && file_exists($difile) && !$regexist) {
			if ($us->q->setDBConnection($dir)) {
				$data['RegionUserAdmin'] = "root";
				foreach($us->q->dreg->query("SELECT InfoKey, InfoValue FROM Info", PDO::FETCH_ASSOC) as $row) {
					if ($row['InfoKey'] == "RegionId" || $row['InfoKey'] == "RegionLabel" || $row['InfoKey'] == "LangIsoCode " || 
						$row['InfoKey'] == "CountryIso" || $row['InfoKey'] == "RegionOrder" || $row['InfoKey'] == "RegionStatus" || 
						$row['InfoKey'] == "IsCRegion" || $row['InfoKey'] == "IsVRegion")
							$data[$row['InfoKey']] = $row['InfoValue'];
				}
				$data['RegionId'] = $dir;
				if (!empty($reglabel))
					$data['RegionLabel'] = $reglabel;
				$r = new DIRegion($us, $data['RegionId']);
				$r->setFromArray($data);
				$iReturn = $r->insert();
				if (!iserror($iReturn)) {
					$rol = $us->setUserRole($data['RegionUserAdmin'], $data['RegionId'], "ADMINREGION");
				}
			}
		}
		return $iReturn;
	}
	
	public static function createRegionBackup($us, $RegionId, $OutFile) {
		$iReturn = ERR_NO_ERROR;
		$zip = new ZipArchive();
		if ($zip->open($OutFile, ZIPARCHIVE::CREATE) != TRUE) {
			$iReturn = ERR_UNKNOWN_ERROR;
		} else {
			$DBDir = VAR_DIR . '/database/' . $RegionId ."/";
			$fhdir = dir($DBDir);
			while ($file = $fhdir->read()) {
				if (is_file($DBDir . $file)) {
					$zip->addFile($DBDir . $file, $file);
				}
			}
			$fhdir->close();
		}
		$zip->close();
		return $iReturn;
	}
	
	public function toXML() {
		$xml = '';
		$xml .= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$xml .= '<RegionInfo version="1.0">' . "\n";
		// Add RegionInfo Sections
		foreach(array_keys($this->oField) as $section) {
			$xml .= '	<' . $section . '>' . "\n";
			foreach($this->oField[$section] as $key => $value) {
				$xml .= '		<' . $key . '>';
				$xml .= $value;
				$xml .= '</' . $key . '>' . "\n";
			}
			$xml .= '	</' . $section . '>' . "\n";
		}
		$xml .= '</RegionInfo>' . "\n";
		return $xml;
	}
} //class

</script>
