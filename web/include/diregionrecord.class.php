<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
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
		return ERR_NO_ERROR;
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
		$sQuery = 'DELETE FROM Region WHERE RegionId=:RegionId';
		$sth = $us->q->core->prepare($sQuery);
		$us->q->core->beginTransaction();
		try
		{
			$sth->bindParam(':RegionId', $prmRegionId, PDO::PARAM_STR);
			$sth->execute();
			$us->q->core->commit();
			$iReturn = STATUS_YES;
		} catch (Exception $e) {
			$us->q->core->rollBack();
			showErrorMsg('ERROR deleteRegion : ' . $e->getMessage());
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		return $iReturn;
	}
	
	public function removeRegionUserAdmin()
	{
		$iReturn = ERR_NO_ERROR;
		$sQuery = 'SELECT * FROM RegionAuth WHERE RegionId=:RegionId AND AuthKey=:AuthKey AND AuthAuxValue=:AuthAuxValue';
		$sth = $this->session->q->core->prepare($sQuery);
		$this->session->q->core->beginTransaction();
		try
		{
			$sth->bindValue(':RegionId'    , $this->get('RegionId'), PDO::PARAM_STR);
			$sth->bindValue(':AuthKey'     , 'ROLE'       , PDO::PARAM_STR);
			$sth->bindValue(':AuthAuxValue', 'ADMINREGION', PDO::PARAM_STR);
			$sth->execute();
			$this->session->q->core->commit();
			$a = array();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$a[] = $row['UserId'];
			} //foreach
			foreach ($a as $UserId)
			{
				$this->session->setUserRole($UserId, $this->get('RegionId'), 'NONE');
			}
		}
		catch (Exception $e)
		{
			$this->session->q->core->rollBack();
			showErrorMsg($e->getMessage());
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		return $iReturn;
	}
} //class

</script>
