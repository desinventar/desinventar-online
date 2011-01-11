<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

class DIRegionInfo {
	public function __construct($prmRegionId='',$prmXMLFile='') {
		$this->info = array('Info'        => array(),
		                    'Description' => array(),
		                    'GeoCarto'    => array()
		                   );
		$this->RegionId = '';
		$this->XMLFile = '';
		$num_args = func_num_args();
		if ($num_args >= 1) {
			$this->RegionId = $prmRegionId;
			if ($this->RegionId != '') {
				$this->XMLFile = $this->getRegionXMLFileName();
			}
			if ($num_args >= 2) {
				$this->XMLFile = $prmXMLFile;
			}
		}			
	} //__construct
	
	public function set($key, $value, $section='') {
		if ($section == '') {
			$section = 'Info';
		}
		$this->info[$section][$key] = $value;
	}

	public function getRegionXMLFileName() {
		$filename = DBDIR . '/' . $this->RegionId . '/info.xml';
		return $filename;
	}

	public function loadFromXML($prmXMLFile = '') {
		$iReturn = ERR_NO_ERROR;
		if ($prmXMLFile == '') {
			$prmXMLFile = $this->XMLFile;
		}
		if (! file_exists($prmXMLFile) ) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		if ($iReturn > 0) {
			$doc = new DomDocument('1.0','UTF-8');
			$doc->load($prmXMLFile);
			foreach($doc->getElementsByTagName('General') as $tree) {
				$section = 'Info';
				foreach($tree->childNodes as $node) {
					$key = $node->nodeName;
					$value = $node->nodeValue;
					print $key . "\n";
					$this->set($key, $value, $section);
				}
			} //foreach
			/*
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
			*/
		} //if
		return $iReturn;
	} //function

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
			foreach($this->q->dreg->query($sQuery) as $row) {
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
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		if ($iReturn > 0) {
			// Save to String...
			$xml = $doc->saveXML();
		} else {
			$xml = '';
		}
		return $xml;
	}
	
	public function saveToXML($filename='') {
		$iReturn = ERR_NO_ERROR;
		if ($filename == '') {
			$filename = $this->getXMLFileName();
		}
		$xml = $this->toXML();
		if ($xml != '') {
			$fh = fopen($filename, 'w');
			fwrite($fh, $this->toXML());
			fclose($fh);
		} else {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		return $iReturn;
	}
	
} //class


