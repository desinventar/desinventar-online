<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

class DIRegionRecord extends DIRegion {
	public function __construct($prmSession) {
		$this->sTableName   = 'Region';
		$this->sPermPrefix  = 'INFO';
		$this->sFieldKeyDef = 'RegionId/STRING';
		$this->sFieldDef    = 'RegionLabel/STRING,' .
		                      'LangIsoCode/STRING,' . 
		                      'CountryIso/STRING,' .
		                      'RegionOrder/INTEGER,' .
		                      'RegionStatus/INTEGER,' .
		                      'RegionLastUpdate/DATETIME,' .
		                      'IsCRegion/INTEGER,' .
		                      'IsVRegion/INTEGER';
		$this->sInfoDef     = 'DBVersion/STRING,' .
		                      'PeriodBeginDate/DATE,' .
		                      'PeriodEndDate/DATE,' .
		                      'GeoLimitMinX/DOUBLE,' . 
		                      'GeoLimitMinY/DOUBLE,' . 
		                      'GeoLimitMaxX/DOUBLE,' . 
		                      'GeoLimitMaxY/DOUBLE,' .
		                      'OptionOutOfRange/INTEGER,' .
		                      'OptionLanguageList/STRING,' .
		                      'OptionOldName/STRING';
		$this->sInfoTrans   = 'InfoCredits/STRING,' . 
		                      'InfoGeneral/STRING,' .
		                      'InfoSources/STRING,' .
		                      'InfoSynopsis/STRING,' . 
		                      'InfoObservation/STRING,' . 
		                      'InfoGeography/STRING,' . 
		                      'InfoCartography/STRING,' .
		                      'InfoAdminURL/STRING';
		parent::__construct($prmSession);
		$this->createFields($this->sInfoDef);		
		$this->addLanguageInfo('eng');
		$this->set('PeriodBeginDate', '');
		$this->set('PeriodEndDate', '');

		$prmRegionId = '';
		$XMLFile = '';
		$num_args = func_num_args();
		if ($num_args >= 2) {
			// Load region if parameter was specified
			$prmRegionId = func_get_arg(1);
			if ($num_args >= 3) {
				// Load Info from Specified XML File
				$prmRegionId = '';
				$XMLFile = func_get_arg(2);
			}
		} else {
			// Try to load region from Current Session if no parameter was specified
			if ( ($prmSession->RegionId != '') && ($prmSession->RegionId != 'core')) {
				$prmRegionId = $prmSession->RegionId;
			}
		}
		$iReturn = ERR_NO_ERROR;
		if ($prmRegionId != '') {
			$this->set('RegionId', $prmRegionId);
			$iReturn = $this->session->q->setDBConnection($prmRegionId);
		}
		if ($iReturn > 0) {
			$iReturn = $this->load();
			if ($XMLFile != '') {
				// Load Info from specified XML File
				$iReturn = $this->loadFromXML($XMLFile);
			} else {
				// Attempt to load from XML in Region directory...
				$XMLFile = $this->getXMLFileName();
				if (file_exists($XMLFile)) {
					// XML File Exists, load data...
					$iReturn = $this->loadFromXML($XMLFile);
				} else {
					//XML File does not exists, create xml file using Info Table
					// Load eng Info
					$LangIsoCode = $this->get('LangIsoCode');
					if ($LangIsoCode != 'eng') {
						$this->addLanguageInfo($LangIsoCode);
					}
					$iReturn = $this->saveToXML();
				} //if
			} //if
		}
		if ($this->get('OptionLanguageList') == '') {
			$this->set('OptionLanguageList', $this->get('LangIsoCode'));
		}
	} // __construct
	
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

	public function insert() {
		parent::insert();
		$this->createRegionDB('');
		return ERR_NO_ERROR;
	}

	public function createRegionDB($prmGeoLevelName='') {
		// Creates/Initialize the region database
		$iReturn = ERR_NO_ERROR;
		$prmRegionId = $this->get('RegionId');
		// Create Directory for New Region
		$DBDir = DBDIR . '/' . $prmRegionId;
		$DBFile = $DBDir . '/desinventar.db';
		$this->session->q->dreg = null;
		$this->createRegionDBDir();
		try {
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
			showErrorMsg('Error : ' . $e->getMessage());
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
			//$this->insert();
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
			} else {
				$g->insert();
				$c->insert();
			}
		}
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
				$Query = 'UPDATE Geography SET GeographyName=:GeographyName WHERE GeographyLevel=0 AND GeographyCode=:GeographyCode';
				$sth = $this->session->q->dreg->prepare($Query);
				$sth->bindParam(':GeographyName', $prmRegionItemGeographyName, PDO::PARAM_STR);
				$sth->bindParam(':GeographyCode', $prmRegionItemId, PDO::PARAM_STR);
				$sth->execute();
			}
		}
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
		$g = DIGeography::loadByCode($this->session, $prmRegionId);
		$GeographyId = $g->get('GeographyId');
		if ($GeographyId == '') {
			$GeographyId = $g->buildGeographyId('');
		}
		return $GeographyId;
	}
	
	public function addRegionItemGeography($prmRegionItemId, $prmRegionItemGeographyId) {
		$iReturn = ERR_NO_ERROR;
		$q = $this->session->q->dreg;
		
		// Copy Geography From Database
		if ($iReturn > 0) {
			$RegionItemDir = VAR_DIR . '/database/' . $prmRegionItemId;
			$RegionItemDB = $RegionItemDir . '/desinventar.db';
			// Attach Database
			$q->query($this->attachQuery($prmRegionItemId,'RegItem'));
			$this->copyData($q, 'Geography','GeographyId', $prmRegionItemId, $prmRegionItemGeographyId, false);
			$q->query($this->detachQuery());
		}
		return $iReturn;
	}
	
	
	public function rebuildEventData() {
		$this->copyEvents($this->get('LangIsoCode'));
		$this->session->q->dreg->query("DELETE FROM Event WHERE EventPredefined=0");
		$o = new DIEvent($this->session);
		$query = "SELECT * FROM Sync WHERE SyncTable='Event'";
		foreach($this->session->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));
			foreach($this->session->q->dreg->query("SELECT * FROM RegItem.Event WHERE EventPredefined=0") as $row) {
				$o->setFromArray($row);
				$o->insert();
			}
			$this->session->q->dreg->query($this->detachQuery('RegItem'));
		}
	}

	public function rebuildCauseData() {
		$this->copyCauses($this->get('LangIsoCode'));
		$this->session->q->dreg->query("DELETE FROM Cause WHERE CausePredefined=0");
		$o = new DICause($this->session);
		$query = "SELECT * FROM Sync WHERE SyncTable='Cause'";
		foreach($this->session->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));
			foreach($this->session->q->dreg->query("SELECT * FROM RegItem.Cause WHERE CausePredefined=0") as $row) {
				$o->setFromArray($row);
				$o->insert();
			}
			$this->session->q->dreg->query($this->detachQuery('RegItem'));
		}
	}


	public function rebuildGeoLevelData() {
		$iReturn = ERR_NO_ERROR;
		$this->session->q->dreg->query("DELETE FROM GeoLevel WHERE GeoLevelId>0");
		$query = "SELECT * FROM Sync WHERE SyncTable='Cause'";
		foreach($this->session->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			// GetCurrentMaxLevel
			$iMaxLevel = 0;
			foreach($this->session->q->dreg->query('SELECT MAX(GeoLevelId) AS MAXVAL FROM GeoLevel') as $row) {
				$iMaxLevel = $row['MAXVAL'];
			}
			foreach($this->session->q->dreg->query('SELECT * FROM RegItem.GeoLevel') as $row) {
				if ($iReturn > 0) {
					if (($row['GeoLevelId'] + 1) > $iMaxLevel) {
						$iMaxLevel++;
						$g = new DIGeoLevel($this->session, $iMaxLevel);
						$g->set('GeoLevelName', 'Nivel ' . $iMaxLevel);
						$iReturn = $g->insert();
					}
				}			
			} //foreach
			$this->session->q->dreg->query($this->detachQuery('RegItem'));
		}
		return $iReturn;
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
			$this->session->q->dreg->query($Query);
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
			$this->session->q->dreg->query($Query);
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
			$a[$Field] = strip_tags($this->get($Field, $prmLang));
			$a[$Field] = preg_replace('/\n/','<br />', $a[$Field]);
			//$a[$Field] = $this->get($Field, $prmLang);
		}
		$a['RegionLastUpdate'] = substr($a['RegionLastUpdate'],0,10);

		$Query = "SELECT MIN(DisasterBeginTime) AS MinDate, MAX(DisasterBeginTime) AS MaxDate FROM Disaster ".
			"WHERE RecordStatus='PUBLISHED'";
		foreach($this->session->q->dreg->query($Query) as $row) {
			$MinDate = $row['MinDate'];
			$MaxDate = $row['MaxDate'];
		}
		// 2010-01-21 (jhcaiced) Fix some weird cases in MinDate/MaxDate
		if (substr($MinDate, 5, 2) == '00') {
			$MinDate = substr($MinDate, 0, 4) . '-01-01';
		}
		if (substr($MaxDate, 5, 2) > '12') {
			$MaxDate = substr($MaxDate, 0, 4) . '-12-31';
		}
		$a['DataMinDate'] = $MinDate;
		$a['DataMaxDate'] = $MaxDate;

		// 2010-07-06 (jhcaiced) Manually Calculate RegionLastUpdate
		$sQuery = "SELECT MAX(RecordUpdate) AS MAX FROM Disaster;";
		foreach($this->session->q->dreg->query($sQuery) as $row) {
			$a['RegionLastUpdate'] = substr($row['MAX'],0,10);
		}

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
			foreach ($this->session->q->core->query($Query) as $row) {
				$RegionItemId = $row['RegionItem'];
				$r = new DIRegion($this->session, $RegionItemId);
				$ItemMinX = $r->getRegionInfoValue('GeoLimitMinX');
				if ($ItemMinX < $MinX) { $MinX = $ItemMinX; }
				$ItemMaxX = $r->getRegionInfoValue('GeoLimitMaxX');
				if ($ItemMaxX > $MaxX) { $MaxX = $ItemMaxX; }
				$ItemMinY = $r->getRegionInfoValue('GeoLimitMinY');
				if ($ItemMinY < $MinY) { $MinY = $ItemMinY; }
				$ItemMaxY = $r->getRegionInfoValue('GeoLimitMaxY');
				if ($ItemMaxY > $MaxY) { $MaxY = $ItemMaxY; }
				$r = null;
			} //foreach
			$this->session->q->setDBConnection($this->get('RegionId'));
			if ($iReturn > 0) {
				$this->set('GeoLimitMinX', $MinX);
				$this->set('GeoLimitMaxX', $MaxX);
				$this->set('GeoLimitMinY', $MinY);
				$this->set('GeoLimitMaxY', $MaxY);
				$this->update();
			}
		} //if
	} //updateMapArea
	
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
					$rol = $us->setUserRole($_POST['RegionInfo']['RegionUserAdmin'], 
					                        $_POST['RegionInfo']['RegionId'],
					                        'ADMINREGION');
				}
			}
		}
		return $iReturn;
	}
	
	public static function createRegionBackup($us,$OutFile) {
		$iReturn = ERR_NO_ERROR;
		if ($us->RegionId == '') {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($iReturn > 0) {
			$DirName = dirname($OutFile);
			if (! file_exists($DirName) ) {
				if (! mkdir($DirName, 0777, true)) {
					$iReturn = ERR_UNKNOWN_ERROR;
				}
			}
		}
		if ($iReturn > 0) {
			unlink($OutFile);
			$zip = new ZipArchive();
			if ($zip->open($OutFile, ZIPARCHIVE::CREATE) != TRUE) {
				$iReturn = ERR_UNKNOWN_ERROR;
			} else {
				$DBDir = $us->getDBDir();
				// Build a list of files that goes into the zip file
				$filelist = array('desinventar.db','info.xml');
				$sQuery = "SELECT * FROM GeoCarto ORDER BY GeoLevelId";
				foreach($us->q->dreg->query($sQuery) as $row) {
					foreach(array('dbf','shp','shx') as $ext) {
						array_push($filelist, $row['GeoLevelLayerFile'] . '.' . $ext);
					}
				}
				// Add each file to the zip file
				foreach($filelist as $file) {
					if (file_exists($DBDir . '/' . $file) ) {
						$zip->addFile($DBDir . '/' . $file, $file);
					}
				}
			}
			$zip->close();
		}
		return $iReturn;
	}
	
	public static function existRegion($us, $prmRegionId) {
		$iReturn = STATUS_NO;
		$sQuery = 'SELECT RegionId FROM Region WHERE RegionId="' . $prmRegionId . '"';
		try {
			foreach($us->q->core->query($sQuery) as $row) {
				$iReturn = STATUS_YES;
			}
		} catch (Exception $e) {
			$iReturn = ERR_NO_DATABASE;
		}
		return $iReturn;
	}
	
	public static function deleteRegion($us, $prmRegionId) {
		$iReturn = STATUS_NO;
		try {
			$sQuery = 'DELETE FROM Region WHERE RegionId=:RegionId';
			$sth = $us->q->core->prepare($sQuery);
			$sth->bindParam(':RegionId', $prmRegionId, PDO::PARAM_STR);
			$sth->execute();
			$iReturn = STATUS_YES;
		} catch (Exception $e) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		return $iReturn;
	}
	
	public function removeRegionUserAdmin() {
		$iReturn = ERR_NO_ERROR;
		try {
			$sQuery = 'SELECT * FROM RegionAuth WHERE RegionId=:RegionId AND AuthKey=:AuthKey AND AuthAuxValue=:AuthAuxValue';
			$sth = $this->session->q->core->prepare($sQuery);
			$sth->bindValue(':RegionId'    , $this->get('RegionId'), PDO::PARAM_STR);
			$sth->bindValue(':AuthKey'     , 'ROLE'       , PDO::PARAM_STR);
			$sth->bindValue(':AuthAuxValue', 'ADMINREGION', PDO::PARAM_STR);
			$sth->execute();
			$a = array();
			while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$a[] = $row['UserId'];
			} //foreach
			foreach($a as $UserId) {
				$this->session->setUserRole($UserId, $this->get('RegionId'), 'NONE');
			}
		} catch (Exception $e) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		return $iReturn;
	}
} //class

</script>
