<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/
class DIRegionDB extends DIRegion
{
	public function __construct($prmSession, $prmRegionId)
	{
		parent::__construct($prmSession, $prmRegionId);		
	} //__construct

	public function rebuildRegionData()
	{
		//$this->rebuildEventData();
		//$this->rebuildCauseData();
		//$this->rebuildGeoLevelData();
		$this->rebuildGeographyData();
		$this->rebuildGeoCartoData();
		$this->rebuildDisasterData();
	}

	public function rebuildGeoCartoData()
	{
		$iReturn = ERR_NO_ERROR;
		$query = "SELECT * FROM Sync WHERE SyncTable='GeoCarto'";
		$list = array();
		foreach ($this->session->q->dreg->query($query) as $row)
		{
			$list[] = $row['SyncURL'];
		}
		
		foreach ($list as $SyncURL)
		{
			$url = $this->processURL($SyncURL);
			$RegionItemId = $url['regionid'];
			$prmRegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

			$query = "DELETE FROM GeoCarto WHERE GeographyId='" . $prmRegionItemGeographyId . "'";
			$this->session->q->dreg->query($query);
			
			// Attach Database
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			$r2 = new DIRegion($this->session, $RegionItemId);
			$CountryIso2 = $r2->get('CountryIso');

			// Copy GeoCarto Items
			$this->copyData($this->session->q->dreg, 'GeoCarto','GeographyId',$RegionItemId, $prmRegionItemGeographyId, false);
			
			$sQuery = 'UPDATE GeoCarto SET GeoLevelLayerFile="' . $CountryIso2 . '_"||GeoLevelLayerFile WHERE RegionId="' . $RegionItemId . '"';
			$this->session->q->dreg->query($sQuery);
			
			// Copy SHP,SHX,DBF files from each RegionItem to Region
			$RegionDir     = VAR_DIR . '/database/' . $this->get('RegionId');
			$RegionItemDir = VAR_DIR . '/database/' . $RegionItemId;			
			foreach ($this->session->q->dreg->query('SELECT * FROM RegItem.GeoCarto') as $row) {
				foreach (array('dbf','shp','shx','prj') as $ext) {
					$file0 = $row['GeoLevelLayerFile'] . '.' . $ext;
					$file1 = $RegionItemDir . '/' . $file0;
					$file2 = $RegionDir . '/' . $CountryIso2 . '_' . $file0;
					if (file_exists($file1)) {
						copy($file1, $file2);
					}
				} //foreach
			} //foreach
			
			$g = new DIGeoCarto($this->session, 0);
			$g->set('GeographyId', $prmRegionItemGeographyId);
			$g->set('RegionId', $RegionItemId);
			$g->insert();

			$this->session->q->dreg->query($this->detachQuery('RegItem'));
		} //foreach

		// Fix GeographyId for items with too many detail 
		$sQuery = 'SELECT COUNT(*) AS C FROM GeoLevel';
		foreach ($this->session->q->dreg->query($sQuery) as $row)
		{
			$MaxGeoLevel = $row['C'];
		}
		$sQuery = 'DELETE FROM GeoCarto WHERE GeoLevelId >=' . $MaxGeoLevel;
		$this->session->q->dreg->query($sQuery);
		return $iReturn;
	}
	

	public function rebuildDisasterData($prmRegionItemId='')
	{
		$iReturn = ERR_NO_ERROR;
		$query = "SELECT * FROM Sync WHERE SyncTable='Disaster'";
		if ($prmRegionItemId != '') {
			$query .= "AND SyncURL LIKE '%" . $prmRegionItemId . "%'";
		}
		$list = array();
		foreach ($this->session->q->dreg->query($query) as $row) {
			$list[] = $row['SyncURL'];
		}
		
		$query = "DELETE FROM Disaster";
		$this->session->q->dreg->query($query);
		$query = "DELETE FROM EEData";
		$this->session->q->dreg->query($query);
		
		foreach ($list as $SyncURL)
		{
			$url = $this->processURL($SyncURL);
			$RegionItemId = $url['regionid'];
			$RegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

			// Attach Database
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			// Copy Disaster Table, adjust GeographyId Field
			$this->copyData($this->session->q->dreg, 'Disaster','GeographyId', $RegionItemId, $RegionItemGeographyId, false);
			
			// Delete Non Published Data cards
			//$this->session->q->dreg->query("DELETE FROM Disaster WHERE RecordStatus<>'PUBLISHED'");
			
			// Copy DisasterId from EEData, Other Fields are Ignored...
			$this->session->q->dreg->query("INSERT INTO EEData (DisasterId) SELECT DisasterId FROM Disaster WHERE GeographyId LIKE '" . $RegionItemGeographyId . "%'");

			$this->session->q->dreg->query($this->detachQuery('RegItem'));
		} //foreach

		// Fix GeographyId for items with too many detail 
		$sQuery = 'SELECT COUNT(*) AS C FROM GeoLevel';
		foreach ($this->session->q->dreg->query($sQuery) as $row) {
			$MaxGeoLevel = $row['C'];
		}
		$sQuery = 'UPDATE Disaster SET GeographyId=SUBSTR(GeographyId,1,' . $MaxGeoLevel*5 . ')';
		$this->session->q->dreg->query($sQuery);
		
	} //function

	public function rebuildGeographyData()
	{
		$iReturn = ERR_NO_ERROR;
		
		// Delete existing Geography except for Level0 in Virtual Region
		$query = "DELETE FROM Geography WHERE GeographyLevel>0";
		$this->session->q->dreg->query($query);
		
		$list = array();
		$query = "SELECT * FROM Sync WHERE SyncTable='Geography'";
		foreach ($this->session->q->dreg->query($query) as $row)
		{
			$list[] = $row['SyncURL'];
		}
		
		foreach ($list as $SyncURL) {
			$url = $this->processURL($SyncURL);
			$RegionItemId = $url['regionid'];
			$prmRegionItemId = $RegionItemId;
			$prmRegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

			// Attach Database
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			// Copy Geography From Database
			$this->copyData($this->session->q->dreg, 'Geography','GeographyId', $prmRegionItemId, $prmRegionItemGeographyId, false);
			
			// Update GeographyFQName in child nodes
			$g = new DIGeography($this->session, $prmRegionItemGeographyId);
			$GeographyFQName = $g->get('GeographyFQName');
			$query = 'UPDATE Geography SET GeographyFQName="' . $GeographyFQName . '/' . '"||GeographyFQName WHERE GeographyLevel>0 AND GeographyId LIKE "' . $prmRegionItemGeographyId . '%"';
			$this->session->q->dreg->query($query);

			$this->session->q->dreg->query($this->detachQuery('RegItem'));
			
		}
		
		$sQuery = 'SELECT COUNT(*) AS C FROM GeoLevel';
		foreach ($this->session->q->dreg->query($sQuery) as $row) {
			$MaxGeoLevel = $row['C'];
		}
		$sQuery = 'DELETE FROM Geography WHERE GeographyLevel>=' . $MaxGeoLevel;
		$this->session->q->dreg->query($sQuery);
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

	public function copyData($prmConn, $prmTable, $prmField, $prmRegionItemId, $prmValue, $isNumeric) {
		$Queries = array();		
		// Create Empty Table
		$Query = "DROP TABLE IF EXISTS TmpTable";
		array_push($Queries, $Query);
		$Query = "CREATE TABLE TmpTable AS SELECT * FROM " . $prmTable . " LIMIT 0";
		array_push($Queries, $Query);
		
		$endLoop = 1;
		if ($prmTable == 'Geography') { $endLoop = 100; }
		
		for($i = 0; $i<$endLoop; $i++) {
			
			$Query = 'DELETE FROM TmpTable';
			array_push($Queries, $Query);
		
			$Query = "INSERT INTO TmpTable SELECT * FROM RegItem." . $prmTable;
			if ($prmTable == 'Geography') {
				$gId = padNumber($i, 5);
				$Query .= ' WHERE GeographyId LIKE "' . $gId . '%"';
			}
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
		} //for
		// Delete Table at End
		$Query = "DROP TABLE IF EXISTS TmpTable";
		//array_push($Queries, $Query);
		foreach ($Queries as $Query) {
			//$this->session->q->dreg->query($Query);
			try {
				$prmConn->query($Query);
			} catch (Exception $e) {
				showErrorMsg($e->getMessage());
			}
		}
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
		$query = 'DELETE FROM Geography WHERE GeographyCode=:GeographyCode';
		$sth = $this->session->q->dreg->prepare($query);
		$sth->bindParam(':GeographyCode', $prmRegionItemId, PDO::PARAM_STR);
		$sth->execute();
		$g = new DIGeography($this->session, 
		                     $prmRegionItemGeographyId,
		                     $this->get('LangIsoCode'));
		$g->set('GeographyCode', $prmRegionItemId);
		$g->set('GeographyName', $prmRegionItemGeographyName);
		$iReturn = $g->insert();
		return $iReturn;
	}

	public function getRegionItemGeographyId($prmRegionId) {
		$GeographyId = '';
		$g = DIGeography::loadByCode($this->session, $prmRegionId);
		if ($g != null) {
			$GeographyId = $g->get('GeographyId');
		}
		if ($GeographyId == '')
		{
			$GeographyId = $g->buildGeographyId('');
		}
		return $GeographyId;
	}

	public static function getRegionTables() {
		$RegionTables = array('Event','Cause','GeoLevel',
	                          'GeoCarto','Geography','Disaster',
	                          'EEData','EEField','EEGroup');
		return $RegionTables;
	}

	public function clearRegionTables() {
		// Delete ALL Record from Database - Be Careful...
		foreach ($this->getRegionTables() as $TableName) {
			$query = 'DELETE FROM ' . $TableName;
			$sth = $this->session->q->dreg->prepare($query);
			$sth->execute();
		} //foreach
	}

	public function addRegionItemSync($prmRegionItemId) {
		foreach ($this->getRegionTables() as $TableName) {
			$s = new DISync($this->session);
			$s->set('SyncTable', $TableName);
			$s->set('RegionId', $this->get('RegionId'));
			$s->set('SyncURL', 'file:///' . $prmRegionItemId);
			$s->insert();
		} //foreach
	}

	public function clearSyncTable() {
		$sth = $this->session->q->dreg->prepare('DELETE FROM Sync;');
		$sth->execute();
	}

	public function clearGeoLevelTable() {
		$sth = $this->session->q->dreg->prepare('DELETE FROM GeoLevel;');
		$sth->execute();
	}
	
	public function clearGeographyTable() {
		$sth = $this->session->q->dreg->prepare('DELETE FROM Geography;');
		$sth->execute();
	}

	public function clearGeoCartoTable() {
		$sth = $this->session->q->dreg->prepare('DELETE FROM Geography;');
		$sth->execute();
	}
	
	public function createGeoLevel($prmGeoLevelId, $prmGeoLevelName) {
		$g = new DIGeoLevel($this->session, $prmGeoLevelId);
		$g->set('GeoLevelName', $prmGeoLevelName);
		$g->set('RegionId', $this->get('RegionId'));
		if ($g->exist() > 0) {
			$g->update();
		} else {
			$g->insert();
		}
	}
	
	public function createCause($prmCauseId, $prmCauseName) {
		$o = new DICause($this->session, $prmCauseId, $prmCauseName);
		$o->set('CausePreDefined', 0);
		if ($o->exist() > 0) {
			$o->update(); 
		} else {
			$o->insert();
		}
	}

	public function createEvent($prmEventId, $prmEventName) {
		$o = new DIEvent($this->session, $prmEventId, $prmEventName);
		$o->set('EventPreDefined', 0);
		if ($o->exist() > 0) {
			$o->update(); 
		} else {
			$o->insert();
		}
	}

	public static function createRegionDBFromZip($us, $mode, $prmRegionId, $prmRegionLabel, $prmZipFile) {
		$iReturn = ERR_NO_ERROR;
		
		// Open zip file and extract files
		$zip = new ZipArchive();
		$res = $zip->open($prmZipFile);
		if ($res != TRUE) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($iReturn > 0) {
			$OutDir = DBDIR . '/' . $prmRegionId;
			if ($mode == 'NEW') {
				// Create directory for new database
				if (! mkdir(DBDIR . '/' . $prmRegionId, 0755)) {
					$iReturn = ERR_UNKNOWN_ERROR;
				}
			}
		}
		
		if ($iReturn > 0) {
			// Extract contents of zipfile
			$zip->extractTo($OutDir);
			$zip->close();
		}
		
		if ($iReturn > 0) {
			//Create/update info.xml and core.Region data...
			if ($mode == 'NEW') {
				$dbexist = DIRegion::existRegion($us, $prmRegionId);
				if ($dbexist > 0) {
					// RegionId already exists, cannot create db
					$iReturn = ERR_UNKNOWN_ERROR;
				} else {
					$r = new DIRegion($us, $prmRegionId, $OutDir . '/info.xml');
					$r->set('RegionId', $prmRegionId);
					$r->set('RegionLabel', $prmRegionLabel);
					$us->open($prmRegionId);
					$iReturn = $r->insert();
				}
			}
		}
		
		if ($iReturn < 0) {
			// In case of error, cleanup by removing the new directory
			if ($mode == 'NEW') {
				DIRegion::deleteRegion($us, $prmRegionId);
				rrmdir($OutDir);
			}
		}	
		return $iReturn;	
	} //function

	public function createRegionDB($prmGeoLevelName='')
	{
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

	public function addPredefinedItemSync()
	{
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

	public function addRegionItem2($prmRegionItemId, $prmRegionItemGeographyName, $prmRegionItemGeographyId='')
	{
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
		if ($iReturn > 0) {
			//$iReturn = $this->addRegionItemDisaster($prmRegionItemId,$prmRegionItemGeographyId);
		}
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

	public function addRegionItemGeography($prmRegionItemId, $prmRegionItemGeographyId)
	{
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

	public function rebuildEventData()
	{
		$this->copyEvents($this->get('LangIsoCode'));
		$this->session->q->dreg->query("DELETE FROM Event WHERE EventPredefined=0");
		$o = new DIEvent($this->session);
		$query = "SELECT * FROM Sync WHERE SyncTable='Event'";
		foreach ($this->session->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));
			foreach ($this->session->q->dreg->query("SELECT * FROM RegItem.Event WHERE EventPredefined=0") as $row) {
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
		foreach ($this->session->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));
			foreach ($this->session->q->dreg->query("SELECT * FROM RegItem.Cause WHERE CausePredefined=0") as $row) {
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
		foreach ($this->session->q->dreg->query($query) as $row) {
			$url = $this->processURL($row['SyncURL']);
			$RegionItemId = $url['regionid'];
			$this->session->q->dreg->query($this->attachQuery($RegionItemId,'RegItem'));

			// GetCurrentMaxLevel
			$iMaxLevel = 0;
			foreach ($this->session->q->dreg->query('SELECT MAX(GeoLevelId) AS MAXVAL FROM GeoLevel') as $row) {
				$iMaxLevel = $row['MAXVAL'];
			}
			foreach ($this->session->q->dreg->query('SELECT * FROM RegItem.GeoLevel') as $row) {
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
		foreach ($Queries as $Query) {
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
		foreach ($Queries as $Query) {
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

	public static function createRegionBackup($us,$OutFile)
	{
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
				foreach ($us->q->dreg->query($sQuery) as $row) {
					foreach (array('dbf','shp','shx') as $ext) {
						array_push($filelist, $row['GeoLevelLayerFile'] . '.' . $ext);
					}
				}
				// Add each file to the zip file
				foreach ($filelist as $file) {
					if (file_exists($DBDir . '/' . $file) ) {
						$zip->addFile($DBDir . '/' . $file, $file);
					}
				}
			}
			$zip->close();
		}
		return $iReturn;
	}
} //class
</script>
