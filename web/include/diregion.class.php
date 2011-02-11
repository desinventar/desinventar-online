<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
class DIRegion extends DIObject {
	public function __construct($prmSession) {
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
			$XMLFile = $this->getXMLFileName();
		}
		if ($iReturn > 0) {
			// Attempt to load from XML in Region directory...
			if (file_exists($XMLFile)) {
				// XML File Exists, load data...
				$iReturn = $this->loadFromXML($XMLFile);
				// Fix RegionId because in some files is wrong when copying data...
				$this->set('RegionId', $prmRegionId);
			} else {
				$this->set('RegionLabel', $prmRegionId);
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

	public function load() {
		$iReturn = ERR_NO_ERROR;
		return $iReturn;
	}

	public function createRegionDBDir() {
		$prmRegionId = $this->get('RegionId');
		// Create Directory for New Region
		$DBDir = DBDIR . '/' . $prmRegionId;
		if (!file_exists($DBDir)) {
			mkdir($DBDir);
		}
	}

	public function insert() {
		$iReturn = ERR_NO_ERROR;
		$this->createRegionDBDir();
		$this->saveToXML();
		$this->insertCore();
		return $iReturn;
	}
	
	public function update() {
		$iReturn = $this->saveToXML();
		$this->updateCore();
		return $iReturn;
	}
	
	public function insertCore() {
		$sQuery = 'INSERT INTO Region(RegionId) VALUES ("' . $this->get('RegionId') . '")';
		$this->session->q->core->query($sQuery);
		$this->updateCore();
	}

	public function updateCore() {
		// Update core.Region table using new data...
		$sQuery = 'UPDATE Region SET ' .
		 ' RegionLabel="' . $this->get('RegionLabel') . '",' .
		 ' LangIsoCode="' . $this->get('LangIsoCode') . '",' .
		 ' CountryIso="'  . $this->get('CountryIso') . '",' .
		 ' RegionStatus=' . $this->get('RegionStatus') .
		 ' WHERE RegionId="' . $this->get('RegionId') . '"';
		$iReturn = ERR_NO_ERROR;
		$this->session->q->core->query($sQuery);
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

	// Read an specific InfoKey value from the table
	public function getRegionInfoValue($prmInfoKey, $LangIsoCode='') {
		$sReturn = '';
		$sReturn = $this->get($prmInfoKey);
		if ($sReturn == '') {
			$sReturn = $this->get($prmInfoKey, $LangIsoCode);
		}
		return $sReturn;
	} //function

		
	public function getDBInfo($prmLang='eng') {
		$InfoSynopsis = trim($this->get('InfoSynopsis', $prmLang)) . '';
		$isInfoEmpty =  strlen($InfoSynopsis) < 1;
		if ($isInfoEmpty) {
			$prmLang = $this->get('LangIsoCode');
		}
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
		
		$this->session->open($this->get('RegionId'));

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

		if ($a['PeriodBeginDate'] == '')
		{
			$a['PeriodBeginDate'] = $a['DataMinDate'];
		}
		if ($a['PeriodEndDate'] == '')
		{
			$a['PeriodEndDate'] = $a['DataMaxDate'];
		}
		$a['NumDatacards'] = $this->session->q->getNumDisasterByStatus('PUBLISHED');
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
	
	public static function buildRegionId($prmCountryIso) {
		$RegionId = '';
		if ($prmCountryIso == '') {
			$prmCountryIso = 'DESINV';
		}
		$prmTimestamp = date('YmdHis', time());
		$RegionId = $prmCountryIso . '-' . $prmTimestamp;
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
	
	public function toXML() {
		$iReturn = ERR_NO_ERROR;
		$doc = new DomDocument('1.0','UTF-8');
		$root = $doc->createElement('RegionInfo');
		$root = $doc->appendChild($root);
		$root->setAttribute('Version', '1.0');
		
		// General Info and Translations of Descriptions
		foreach(array_keys($this->oField) as $section) {
			if ($section == 'info') {
				$occ = $doc->createElement('General');
				$occ = $root->appendChild($occ);
			} else {
				$occ = $doc->createElement('Description');
				$occ = $root->appendChild($occ);
				$occ->setAttribute('LangIsoCode', $section);
			} 
			foreach($this->oField[$section] as $key => $value) {
				$child = $doc->createElement($key);
				$child = $occ->appendChild($child);
				$value = $doc->createTextNode($value);
				$value = $child->appendChild($value);
			}
			
		}
		
		// Add GeoCarto Section
		$sQuery = "SELECT * FROM GeoCarto ORDER BY GeoLevelId";
		$occ = $doc->createElement('GeoCarto');
		$occ = $root->appendChild($occ);
		try {
			foreach($this->session->q->dreg->query($sQuery) as $row) {
				$level = $doc->createElement('GeoCartoItem');
				$level = $occ->appendChild($level);
				$level->setAttribute('GeoLevelId', $row['GeoLevelId']);
				$level->setAttribute('LangIsoCode', $row['LangIsoCode']);
				foreach(array('GeoLevelLayerFile','GeoLevelLayerName','GeoLevelLayerCode') as $field) {
					$child = $doc->createElement($field);
					$child = $level->appendChild($child);
					$value = $doc->createTextNode($row[$field]);
					$value = $child->appendChild($value);
				} //foreach
			} //foreach
		} catch (Exception $e) {
			$iReturn = ERR_NO_ERROR;
		}
		if ($iReturn > 0) {
			// Save to String...
			$xml = $doc->saveXML();
		} else {
			$xml = '';
		}
		return $xml;
	}
	
	public function getXMLFileName() {
		$filename = DBDIR . '/' . $this->get('RegionId') . '/info.xml';
		return $filename;
	}
	
	public function saveToXML($XMLFile='') {
		$iReturn = ERR_NO_ERROR;
		if ($XMLFile == '') {
			$XMLFile = $this->getXMLFileName();
		}
		$xml = $this->toXML();
		if ($xml != '') {
			$fh = fopen($XMLFile, 'w');
			fwrite($fh, $this->toXML());
			fclose($fh);
		} else {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		return $iReturn;
	}
	
	public function loadFromXML($XMLFile = '') {
		$iReturn = ERR_NO_ERROR;
		if ($XMLFile == '') {
			$XMLFile = $this->getXMLFileName();
		}
		if (! file_exists($XMLFile) ) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($iReturn > 0) {
			$doc = new DomDocument('1.0','UTF-8');
			try {
				$doc->load($XMLFile);
			} catch (Exception $e) {
				fb($e->getCode() . ' ' . $e->getMessage());
			}
			foreach($doc->getElementsByTagName('General') as $tree) {
				$section = 'info';
				foreach($tree->childNodes as $node) {
					$key = $node->nodeName;
					$value = str_replace("\n",'', $node->nodeValue);
					$value = str_replace("\r",'', $value);
					if ($this->existField($key, $section)) {
						$this->set($key, $value, $section);
					}
				}
			} //foreach

			$LangIsoCode = $this->get('LangIsoCode');
			if ($LangIsoCode != 'eng') {
				$this->addLanguageInfo($LangIsoCode);
			}

			// Add Translated Information
			foreach($doc->getElementsByTagName('Description') as $tree) {
				$LangIsoCode = $tree->getAttribute('LangIsoCode');
				$section = $LangIsoCode;
				$this->addLanguageInfo($section);
				foreach($tree->childNodes as $node) {
					$key = $node->nodeName;
					$value = $node->nodeValue;
					//print $node->nodeName . ' => ' . $node->nodeValue . "\n";
					if ($this->existField($key, $section)) {
						$this->set($key, $value, $section);
					}
				}
			} //foreach
		} //if
		return $iReturn;
	} //function

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
