<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
class Query //extends PDO
{
	public $RegionId = '';
	public $dreg = null;
	public $core = null;
	public $DBFile = '';

	public function __construct()
	{
		try
		{
			$num_args = func_num_args();

			// Open core.db - Users, Regions, Auths.. 
			$dbc = CONST_DBCORE;
			if (file_exists($dbc))
			{
				$this->core = new PDO('sqlite:' . $dbc);
			}
			else
			{
				$this->rebuildCore($dbc); // Rebuild data from directory..
			}

			// Open base.db - DI's Basic database
			$dbb = CONST_DBBASE;
			if (file_exists($dbb))
			{
				$this->base = new PDO('sqlite:' . $dbb);
			}

			if ($num_args > 0)
			{
				$this->RegionId = func_get_arg(0);
			}
			
			if ($this->RegionId != '')
			{
				$this->setDBConnection($this->RegionId);
			}
			else
			{
				$this->setDBConnection('core');
			} //if
		}
		catch (Exception $e)
		{
			showErrorMsg('Error !: ' . $e->getMessage());
			die();
		}
	}

	public function getDBFile($prmRegionId)
	{
		$DBFile = VAR_DIR;
		if ($prmRegionId != '')
		{
			if ($prmRegionId == 'core')
			{
				$DBFile .= '/main/core.db';
			}
			else
			{
				$DBFile .= '/database/' . $prmRegionId .'/desinventar.db';
			}
		}
		return $DBFile;
	}
	
	public function setDBConnection($prmRegionId, $prmDBFile='')
	{
		$iReturn = ERR_NO_ERROR;
		$DBFile = VAR_DIR;
		if ($prmRegionId != '')
		{
			if ($prmRegionId == 'core')
			{
				$DBFile .= '/main/core.db';
			}
			else
			{
				if ($prmDBFile == '')
				{
					$DBFile .= '/database/' . $prmRegionId .'/desinventar.db';
				}
				else
				{
					$DBFile = $prmDBFile;
				}
			}
			if (file_exists($DBFile))
			{
				try
				{
					$this->dreg = new PDO('sqlite:' . $DBFile);
					// set the error reporting attribute
					$this->dreg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->dreg->setAttribute(PDO::ATTR_TIMEOUT, 15.0);
					$this->RegionId = $prmRegionId;
					$this->DBFile = $DBFile;
				}
				catch (PDOException $e)
				{
					showErrorMsg($e->getMessage());
				}
			}
			else
			{
				$iReturn = ERR_NO_DATABASE;			
			} //if
		}
		else
		{
			$iReturn = ERR_NO_DATABASE;
			$this->dreg = null;
			$this->RegionId = '';
		}
		return $iReturn;
	}
  
	public function getassoc($sQuery)
	{
		$data = false;
		if (!empty($sQuery))
		{
			$data = array();
			try
			{
				$i = 0;
				foreach($this->dreg->query($sQuery, PDO::FETCH_ASSOC) as $row)
				{
					foreach($row as $key=>$val)
						$data[$i][$key] = $val;
					$i++;
				}
			}
			catch (Exception $e)
			{
				showErrorMsg($e->getMessage());
			}
		}
		else
		{
			echo 'Empty Query !!';
		}
		return $data;
	}

	public function getresult($qry)
	{
		$rst = null;
		$row = null;
		try
		{
			if ($this->dreg != null)
			{
				$rst = $this->dreg->query($qry);
				$row = $rst->fetch(PDO::FETCH_ASSOC);
			}
		}
		catch (Exception $e)
		{
			showErrorMsg($e->getMessage());
		}
		return $row;
	}

	public function getnumrows($qry)
	{
		$rst = $this->getassoc($qry);
		return count($rst);
	}

	function loadEvents($type, $status, $LangIsoCode, $RegionLangIsoCode, $withTranslate = true)
	{
		$data = array();
		if ($type == 'BASE')
		{
			$data = $this->getBasicEventList($LangIsoCode);
		}
		else
		{
			// This is the data from the Region Database...
			$data = $this->getRegionEventList($type, $status, $LangIsoCode);
			if ($withTranslate)
			{
				if ($type != 'USER') 
				{
					// Attempt to translate the list to the requested language using 
					// data from base.Event table
					$data1 = $this->getBasicEventList($LangIsoCode);
					foreach($data as $EventId => $EventData)
					{
						if (array_key_exists($EventId, $data1))
						{
							if ($LangIsoCode != $RegionLangIsoCode)
							{
								$bReplace = $data[$EventId]['EventPredefined'] > 0;
							}
							else
							{
								$bReplace = $data[$EventId]['EventPredefined'] == 1;
							}
							if ($bReplace == true)
							{
								$data[$EventId][0] = $data1[$EventId][0]; // Name
								$data[$EventId][1] = $data1[$EventId][1]; // Desc
								$data[$EventId]['EventName'] = $data1[$EventId]['EventName'];
								$data[$EventId]['EventDesc'] = $data1[$EventId]['EventDesc'];
							}
						}
					}
				}
			}
		}
		return $data;
	}

	// active : active, inactive  | types : predef, user | empty == all
	function loadCauses($type, $status, $LangIsoCode, $RegionLangIsoCode, $withTranslate=true)
	{
		$data = array();
		if ($type == 'BASE')
		{
			$data = $this->getBasicCauseList($LangIsoCode);
		}
		else
		{
			$data = $this->getRegionCauseList($type, $status, $LangIsoCode);
			if ($withTranslate)
			{
				if ($type != 'USER') 
				{
					// Attempt to translate the list to the requested language using 
					// data from base.Cause table
					$data1 = $this->getBasicCauseList($LangIsoCode);
					foreach($data as $CauseId => $CauseData)
					{
						if (array_key_exists($CauseId, $data1))
						{
							if ($LangIsoCode != $RegionLangIsoCode)
							{
								$bReplace = $data[$CauseId]['CausePredefined'] > 0;
							}
							else
							{
								$bReplace = $data[$CauseId]['CausePredefined'] == 1;
							}
							if ($bReplace == true)
							{
								$data[$CauseId][0] = $data1[$CauseId][0]; // Name
								$data[$CauseId][1] = $data1[$CauseId][1]; // Desc
								$data[$CauseId]['CauseName'] = $data1[$CauseId]['CauseName']; // Name
								$data[$CauseId]['CauseDesc'] = $data1[$CauseId]['CauseDesc']; // Desc
							}
						}
					} //foreach
				} //if
			}
		}
		return $data;
	}

	public function getBasicEventList($lg)
	{
		$sql = 'SELECT EventId,EventName,EventDesc,EventActive,EventPredefined,RecordUpdate FROM Event ' .
		       ' WHERE LangIsoCode="' . $lg . '" ORDER BY EventName';
		$data = array();
		$sth = $this->base->prepare($sql);
		$this->base->beginTransaction();
		try
		{
			$sth->execute();
			$this->base->commit();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$row = array_merge($row, array(0 => $row['EventName'], 
				                               1 => $row['EventDesc']));
				$data[$row['EventId']] = $row;
			}
		}
		catch (Exception $e)
		{
			$this->base->rollBack();
			showErrorMsg('ERROR getBasicEventList : ' . $e->getMessage());
		}			
		return $data;
	}

	public function getBasicCauseList($lg)
	{
		$sql = 'SELECT CauseId,CauseName,CauseDesc,CausePredefined,RecordUpdate FROM Cause ' .
		       ' WHERE LangIsoCode="' . $lg . '" ORDER BY CauseName';
		$data = array();
		$res = $this->base->query($sql);
		foreach($res as $row)
		{
			$data[$row['CauseId']] = array(0 => $row['CauseName'],
			                               1 => $row['CauseDesc'],
			                               'CauseName'       => $row['CauseName'],
			                               'CauseDesc'       => $row['CauseDesc'],
			                               'CausePredefined' => $row['CausePredefined'],
			                               'RecordUpdate'    => $row['RecordUpdate']);
		}
		return $data;
	}

	public function getRegionEventList($type, $status, $LangIsoCode)
	{
		if ($type == 'PREDEF')
		{
			$sqlt = 'EventPredefined>=1';
		}
		else if ($type == 'USER')
		{
			$sqlt = 'EventPredefined=0';
		}
		else
		{
			$sqlt = "'1=1'";	// all
		}
		if ($status == 'active')
		{
			$sqls = 'EventActive=1';
		}
		else
		{
			$sqls = "'1=1'"; // all
		}
		$sql = "SELECT EventId,EventName,EventDesc,EventActive,EventPredefined,RecordUpdate FROM Event WHERE ". $sqls ." AND ". $sqlt ." ORDER BY EventName";
		$data = array();
		foreach($this->dreg->query($sql, PDO::FETCH_ASSOC) as $row)
		{
			$row = array_merge($row, array(0 => $row['EventName'],
			                               1 => str2js($row['EventDesc']),
			                               2 => $row['EventActive']
			                              ));
			$data[$row['EventId']] = $row;
		}
		return $data;
	}

	public function getRegionCauseList($type, $status, $LangIsoCode)
	{
		if ($type == "PREDEF")
		{
			$sqlt = "CausePredefined>0";
		}
		else if ($type == "USER")
		{
			$sqlt = "CausePredefined=0";
		}
		else
		{
			$sqlt = "'1=1'";	// all
		}

		if ($status == "active")
		{
			$sqls = "CauseActive=1";
		}
		else
		{
			$sqls = "'1=1'"; // all
		}
		$sql = "SELECT * FROM Cause WHERE ". $sqls ." AND ". 
		$sqlt ." ORDER BY CauseName";
		$data = array();
		$res = $this->dreg->query($sql);
		foreach($res as $row)
		{
			$data[$row['CauseId']] = array(0 => $row['CauseName'], 
			                               1 => str2js($row['CauseDesc']),
			                               2 => $row['CauseActive'],
			                               'CauseName' => $row['CauseName'],
			                               'CauseDesc' => $row['CauseDesc'],
			                               'CauseActive' => $row['CauseActive'],
			                               'CausePredefined' => $row['CausePredefined'],
			                               'RecordUpdate' => $row['RecordUpdate']);
		} //foreach
		return $data;
	} //function

	// READ OBJECTS :: EVENT, CAUSE, GEOGRAPHY, GEOLEVEL READ
	public function isvalidObjectToInactivate($id, $obj)
	{
		switch ($obj)
		{
			case DI_EVENT:
				$whr = "EventId='$id'";
			break;
			case DI_CAUSE:
				$whr = "CauseId='$id'";
			break;
			case DI_GEOGRAPHY:
				$whr = "GeographyId like '$id%'";
			break;
		}
		$sql = "SELECT COUNT(DisasterId) AS counter FROM Disaster WHERE $whr ";
		$res = $this->getresult($sql);
		if ($res['counter'] > 0)
		{
			return false;
		}
		return true;
	}

	public function isvalidObjectName($id, $sugname, $obj)
	{
		switch ($obj)
		{
			case DI_EVENT:
				$name  = "EventName";
				$table = "Event";
				$fld   = "EventId";
			break;
			case DI_CAUSE:
				$name  = "CauseName";
				$table = "Cause";
				$fld   = "CauseId";
			break;
			case DI_GEOGRAPHY:
				$name  = "GeographyCode";
				$table = "Geography";
				$fld   = "GeographyId";
			break;
			case DI_GEOLEVEL:
				$name  = "GeoLevelName";
				$table = "GeoLevel";
				$fld   = "GeoLevelId";
			break;
			case DI_EEFIELD:
				$name  = "EEFieldLabel";
				$table = "EEField";
				$fld   = "EEFieldId";
			break;
			case DI_DISASTER:
				$name  = "DisasterSerial";
				$table = "Disaster";
				$fld   = "DisasterId";
			break;
			case DI_REGION:
				$name  = "RegionId";
				$table = "Region";
				$fld   = "RegionId";
			break;
			default:
				return null;
			break;
		} //switch
		if ($sugname == "")
		{
			return false;
		}
		// uhmm, for spanish only..
		$tilde = array('á','é','í','ó','ú');
		$vocal = array('a','e','i','o','u');
		$sugname = str_replace($tilde, $vocal, $sugname);
		$sql = "SELECT COUNT($fld) as counter FROM $table WHERE $name LIKE '". 
			$sugname ."' AND $fld != '$id'";
		$res = $this->getresult($sql);
		if ($res['counter'] == 0)
		{
			return true;
		}
		return false;
	}

	public function getObjectNameById($id, $obj)
	{
		switch ($obj)
		{
			case DI_EVENT:
				$name  = "EventName";
				$table = "Event";
				$fld   = "EventId";
				break;
			case DI_CAUSE:
				$name  = "CauseName";
				$table = "Cause";
				$fld   = "CauseId";
			break;
			case DI_GEOGRAPHY:
				$name  = "GeographyCode";
				$table = "Geography"; 
				$fld   = "GeographyId";
			break;
			// case "GEONAME":		$name = "GeographyName";	$table = "Geography"; 	$fld = "GeographyId";	break;
			case "GEOCODE":
				$name  = "GeographyId";
				$table = "Geography";
				$fld   = "GeographyCode";
			break;
			case DI_GEOLEVEL:
				$name  = "GeoLevelName";
				$table = "GeoLevel";
				$fld   = "GeoLevelId";
			break;
			default:
				return null;
			break;
		}
		$sql = "SELECT $name FROM $table WHERE $fld = '$id'";
		$res = $this->getresult($sql);
		if (isset($res[$name]))
		{
			return $res[$name];
		}
		else
		{
			return null;
		}
	}

	public function getObjectColor($val, $obj)
	{
		switch ($obj)
		{
			case DI_EVENT:
				$color = "EventRGBColor";
				$table = "Event";
				$fld   = "EventName";
			break;
			case DI_CAUSE:
				$color = "CauseRGBColor";
				$table = "Cause";
				$fld   = "CauseName";
			break;
			default:
				return null;
			break;
		}
		$sql = "SELECT $color FROM $table WHERE $fld = '$val'";
		$res = $this->getresult($sql);
		if (isset($res[$color]))
		{
			return $res[$color];
		}
		return null;
	}

	// GEOGRAPHY & GEO-LEVELS QUERIES
	function buildGeographyId($fid, $lev)
	{
		$sql = "SELECT MAX(GeographyId) AS max FROM Geography WHERE GeographyId ".
				"LIKE '$fid%' AND GeographyLevel = $lev ORDER BY GeographyId";
		$data = array();
		$res = $this->getresult($sql);
		$myid = (int)substr($res['max'], -5);
		$myid += 1;
		$newid = $fid . sprintf("%05s", $myid);
		return $newid;
	}

	function getGeoNameById($geoid)
	{
		if ($geoid == "")
		{
			return null;
		}
		$sql = "SELECT GeographyName FROM Geography WHERE 1!=1";
		$levn = (strlen($geoid) / 5);
		for ($n = 0; $n < $levn; $n++)
		{
			$len = 5 * ($n + 1);
			$geo = substr($geoid, 0, $len);
			$sql .= " OR GeographyId='". $geo ."'";
		}
		$sql .= " ORDER BY GeographyLevel";
		$data = "";
		$res = $this->dreg->query($sql);
		$i = 0;
		foreach($res as $row)
		{
			if ($i > 0)
			{
				$data .= '/';
			}
			$data .= $row['GeographyName'];
			$i++;
		}
		//	$sql = "SELECT GeographyFQName FROM Geography WHERE GeographyId='". $geoid ."'";
		//	$data = $this->dreg->query($sql);
		return $data;
	}

	// function to build geography tree. Using child = '' built full tree.
	function buildGeoTree($child, $mylev, $maxlev, $selgeolist)
	{
		$gtree = array();
		if ($maxlev >= $mylev)
		{
			foreach ($this->loadGeoChilds($child) as $gkey=>$gitem)
			{
				$chked = false;
				if (in_array($gkey, $selgeolist))
				{
					$chked = true;
				}
				// Use only active geography elements
				if ($gitem[2])
				{
					$gtree[$gkey .'|'. $gitem[1] .'|'. $chked] = $this->buildGeoTree($gkey, $mylev+1, $maxlev, $selgeolist);
				}
			}
			return $gtree;
		}
		return null;
	}

	function loadGeography($level, $prmOnlyActive = true)
	{
		$data = array();
		$continue = ERR_NO_ERROR;
		if (!is_numeric($level) || $level < 0)
		{
			$continue = ERR_DEFAULT_ERROR;
		}
		if ($continue)
		{
			$sql = 'SELECT * FROM Geography WHERE GeographyLevel="' . $level . '"' .
			       ' AND GeographyId NOT LIKE "%00000"';
			if ($prmOnlyActive == true)
			{
				$sql .= ' AND GeographyActive>0 ';
			}
			$sql .= ' ORDER BY GeographyName';
			$res = $this->dreg->query($sql, PDO::FETCH_ASSOC);
			foreach($res as $row)
			{
				$data[$row['GeographyId']] = array_merge($row, array(
					0 => $row['GeographyCode'],
					1 => str2js($row['GeographyName']),
					2 => $row['GeographyActive']
				));
			}
		}
		return $data;
	}

	function loadGeoChilds($geoid, $prmOnlyActive = true)
	{
		$data = array();
		$level = $this->getNextLev($geoid);
		$sql = "SELECT * FROM Geography WHERE GeographyId LIKE '". $geoid .
			"%' AND GeographyLevel=" . $level;
		if ($prmOnlyActive == true)
		{
			$sql .= ' AND GeographyActive>0 ';
		}
		$sql .= ' ORDER BY GeographyName';
		$res = $this->dreg->query($sql, PDO::FETCH_ASSOC);
		foreach($res as $row)
		{
			$data[$row['GeographyId']] = array_merge($row, array($row['GeographyCode'], $row['GeographyName'], $row['GeographyActive']));
		}
		return $data;
	}

	function getNextLev($geoid)
	{
		return (strlen($geoid) / 5);
	}

	// fill struct looking by: 
	//prefix: string with prefix used in VRegions
	//level: integer of level, return only data of this level. -1 to all
	//mapping: only levels with files assigned in database, shp - dbf..
	function loadGeoLevels($prefix = '', $lev, $mapping)
	{
		$sqlev  = 'SELECT GeoLevelId, GeoLevelName, GeoLevelDesc FROM GeoLevel ';
		if ($lev >= 0)
		{
			$sqlev .= ' WHERE GeoLevelId=' . $lev . ' ';
		}
		$sqlev .= 'ORDER BY GeoLevelId';

		$sqcar  = 'SELECT GeographyId, GeoLevelId, GeoLevelLayerFile, GeoLevelLayerCode, GeoLevelLayerName FROM GeoCarto ';
		$WhereSQL = '';
		if ($lev >= 0)
		{
			$WhereSQL .= 'GeoLevelId=' . $lev . ' ';
		}
		if (strlen($prefix) > 0)
		{
			if ($WhereSQL != '')
			{
				$WhereSQL .= ' AND ';
			}
			$WhereSQL .= 'GeographyId="' . $prefix . '" ';
		}
		if ($mapping > 0)
		{
			if ($WhereSQL != '')
			{
				$WhereSQL .= ' AND ';
			}
			$WhereSQL .= '(GeoLevelLayerFile != "" AND GeoLevelLayerCode != "" AND GeoLevelLayerName != "") ';
		}
		if (strlen($WhereSQL) > 0)
		{
			$sqcar .= ' WHERE ' . $WhereSQL . ' ';
		}
		$sqcar .= ' ORDER BY GeoLevelId';

		$data = array();
		$rcar = $this->getassoc($sqcar);
		$rlev = $this->dreg->query($sqlev);
		foreach($rlev as $row)
		{
			$lay = array();
			$bAdd = 1;

			foreach ($rcar as $car)
			{
				if ($car['GeoLevelId'] == $row['GeoLevelId'])
				{
					$lay[] = array(
						$car['GeographyId'],
						$car['GeoLevelLayerFile'],
						$car['GeoLevelLayerCode'],
						$car['GeoLevelLayerName']
					);
				}
			}
			if ($mapping > 0)
			{
				$bAdd = count($lay);
			}
			if ($bAdd > 0)
			{
				$data[$row['GeoLevelId']] = array(
					$row['GeoLevelName'],
					$row['GeoLevelDesc'],
					$lay
				);
			}

		}
		return $data;
	} //loadGeoLevels()

	function getMaxGeoLev()
	{
		$sQuery = "SELECT MAX(GeoLevelId) AS Max FROM GeoLevel";
		$MaxGeoLevel = -1;
		foreach($this->dreg->query($sQuery) as $row)
		{
			$MaxGeoLevel = $row['Max'];
		}
		return $MaxGeoLevel;
	}

	function loadGeoLevById($geolevid)
	{
		if (!is_numeric($geolevid))
		{
			return null;
		}
		$sql = "SELECT * FROM GeoLevel WHERE GeoLevelId=". $geolevid;
		$data = array();
		$res = $this->dreg->query($sql);
		foreach($res as $row)
		{
			$data = array(str2js($row['GeoLevelName']), str2js($row['GeoLevelDesc']));
		}
		return $data;
	}

	function getEEFieldList($act)
	{
		$sql = "SELECT * FROM EEField";
		if ($act != "")
		{
			$sql .= " WHERE EEFieldStatus=1 OR EEFieldStatus=3";
		}
		$data = array();
		try
		{
			$res = $this->dreg->query($sql);
		}
		catch (Exception $e)
		{
			showErrorMsg("Error !: " . $e->getMessage());
		}
		foreach($res as $row)
		{
			// Split EEFieldStatus in Fields Active/Public ?
			$row['EEFieldActive'] = 0;
			if ($row['EEFieldStatus'] & CONST_REGIONACTIVE)
			{
				$row['EEFieldActive'] = 1;
			}
			$row['EEFieldPublic'] = 0;
			if ($row['EEFieldStatus'] & CONST_REGIONPUBLIC)
			{
				$row['EEFieldPublic'] = 1;
			}
			$data[$row['EEFieldId']] = array(
				$row['EEFieldLabel'], 
				str2js($row['EEFieldDesc']), 
				$row['EEFieldType'], 
				$row['EEFieldSize'], 
				$row['EEFieldActive'],
				$row['EEFieldPublic']
			);
		}
		return $data;
	}

	function getEEFieldSeries()
	{
		$sql = "SELECT COUNT(EEFieldId) as count FROM EEField";
		$res = $this->getresult($sql);
		if (isset($res['count']))
		{
			return sprintf("%03d", $res['count']);
		}
		return -1;
	}

	// GET DISASTERS INFO: DATES, DATACARDS NUMBER, ETC
	function getDBInfo()
	{
		$data = array();
		if ($this->dreg != null)
		{
			$sql = "SELECT InfoKey, LangIsoCode, InfoValue FROM Info";
			foreach($this->dreg->query($sql) as $row)
			{
				$data[$row['InfoKey'] .'|'. $row['LangIsoCode']] = $row['InfoValue'];
			}
		} //if
		return $data;
	}

	// Read an specific InfoKey value from the table
	function getDBInfoValue($prmInfoKey)
	{
		$sReturn = '';
		if ($this->dreg != null)
		{
			$sql = "SELECT * FROM Info WHERE InfoKey='" . $prmInfoKey . "'";
			if ($prmInfoKey != 'LangIsoCode')
			{
				$sql .= " AND (LangIsoCode='" . $this->getDBInfoValue('LangIsoCode') . "' OR LangIsoCode='')";
			}
			try
			{
				foreach($this->dreg->query($sql) as $row)
				{
					$sReturn = $row['InfoValue'];
				}
			}
			catch (Exception $e)
			{
				showErrorMsg("Error !: " . $e->getMessage());
			}
		} //if
		return $sReturn;
	}
	
	// This function returns an array with the database fields of Disaster
	public function getDisasterFld()
	{
		$fld = array();
		$sql = "SELECT * FROM Disaster LIMIT 0,1";
		$res = $this->getassoc($sql);
		foreach ($res[0] as $key => $val)
		{
			$fld[] = $key;
		}
		// (jhcaiced) SyncRecord should not appear in data grid
		return $fld;
	}
	
	public function getNextDisasterSerial($prmYear)
	{
		$NextSerial = '';
		if ($prmYear != '')
		{
			//$sQuery = "SELECT COUNT(DisasterId) AS num FROM Disaster WHERE DisasterBeginTime LIKE '". $prmYear ."-%'";
			//$res = $this->getresult($sQuery);
			//$NextSerial = sprintf("%05d", $res['num'] + 1);
			$sQuery = "SELECT DisasterSerial FROM Disaster WHERE DisasterSerial LIKE '" . $prmYear . "-%' ORDER BY DisasterSerial DESC LIMIT 1";
			$res = $this->getresult($sQuery);
			$MaxNumber = substr($res['DisasterSerial'], strlen($prmYear) + 1);
			$MaxNumber = $MaxNumber + 1;
			// Try incrementing the MaxNumber value until we find a disaster not used...
			$bFound = 0;
			while (! $bFound)
			{
				$NextSerial = sprintf("%05d", $MaxNumber);
				$sQuery = "SELECT COUNT(DisasterId) AS NUM FROM Disaster WHERE DisasterSerial='" . $prmYear . '-' . $NextSerial . "'";
				$iCount = 0;
				$res = $this->getresult($sQuery);
				$iCount = $res['NUM'];
				if ($iCount > 0)
				{
					$MaxNumber++;
				}
				else
				{
					$bFound = true;
				}
			} //while
		}
		return $NextSerial;
	}

	public function getDisasterBySerial($diser)
	{
		$sql = "SELECT * FROM Disaster WHERE DisasterSerial='$diser'";
		$res = $this->dreg->query($sql);
		return $res;
	}

	public function getDisasterById($diid)
	{
		$sql = "SELECT * FROM Disaster WHERE DisasterId='$diid'";
		$res = $this->dreg->query($sql);
		return $res;
	}

	// Get number of datacards by status: PUBLISHED, DRAFT, ..
	public function getNumDisasterByStatus($status)
	{
		$sql = 'SELECT COUNT(DisasterId) AS counter FROM Disaster';
		if ($status != '')
		{
			$sql .= ' WHERE RecordStatus="' . $status . '"';
		}
		$dat = $this->getresult($sql);
		return $dat['counter'];
	}

	public function getLastUpdate()
	{
		$sql = "SELECT MAX(RecordUpdate) AS lastupdate FROM Disaster";
		$dat = $this->getresult($sql);
		return substr($dat['lastupdate'],0,10);
	}

  
	public function getRegLogList()
	{
		$data = array();
		if ($this->dreg != null)
		{
			$sql = "SELECT DBLogDate, DBLogType, DBLogNotes FROM DatabaseLog ORDER BY DBLogDate DESC";
			$data = array();
			$res = $this->dreg->query($sql);
			foreach($res as $row)
			{
				$data[$row['DBLogDate']] = array($row['DBLogType'], str2js($row['DBLogNotes'])); 
			}
		}
		return $data;
	}

	// BASE.DB & CORE.DB -> COUNTRIES, REGIONS AND VIRTUAL REGIONS FUNCTIONS
	function getCountryByCode($idcnt)
	{
		$sql = "SELECT CountryName FROM Country WHERE CountryIso = '$idcnt'";
		$res = $this->base->query($sql);
		$dat = $res->fetch(PDO::FETCH_ASSOC);
		return $dat['CountryName'];
	}

	function getCountryList()
	{
		$sQuery = "SELECT CountryIso, CountryName FROM Country ORDER BY CountryName";
		$data = array('' => '');
		foreach($this->base->query($sQuery) as $row)
		{
			$data[$row['CountryIso']] = $row['CountryName'];
		}
		return $data;
	}

	function checkExistsRegion($rid)
	{
		$sql = "SELECT RegionId FROM Region WHERE RegionId = '$rid'";
		$res = $this->core->query($sql);
		$dat = $res->fetch(PDO::FETCH_ASSOC);
		if (empty($dat['RegionId']))
		{
			return false;
		}
		return true;
	}

	public function getRegionList($cnt, $status)
	{
		if (!empty($cnt))
		{
			$opt = " CountryIso='$cnt'";
		}
		else
		{
			$opt = " 1=1";
		}
		if ($status == "ACTIVE")
		{
			$opt .= " AND RegionStatus >= 1";
		}
		$sql = "SELECT RegionId, RegionLabel FROM Region WHERE IsCRegion=0 AND IsVRegion=0 AND $opt ORDER BY RegionLabel";
		$res = $this->core->query($sql);
		$data = array();
		foreach ($res as $row)
		{
			$data[$row['RegionId']] = $row['RegionLabel'];
		}
		return $data;
	}

	public function getRegionAdminList()
	{
		$sql = "SELECT * FROM Region WHERE RegionStatus>0 " . 
				"ORDER BY CountryIso, RegionLabel";
		$data = array();
		foreach($this->core->query($sql) as $row)
		{
			$RegionId = $row['RegionId'];
			$row['RegionActive'] = ($row['RegionStatus'] & CONST_REGIONACTIVE) > 0;
			$row['RegionPublic'] = ($row['RegionStatus'] & CONST_REGIONPUBLIC) > 0;
			$RegionLabel  = $row['RegionLabel'];
			if ($row['RegionLabel'] == '')
			{
				$row['RegionLabel'] = $row['RegionId'];
			}
			$row['RegionAdminUserId'] = '';
			$data[$RegionId] = $row;
		} //foreach
		
		foreach($data as $RegionId => &$info)
		{
			$sQuery = 'SELECT RA.*,U.UserFullName FROM RegionAuth RA,User U WHERE ' . 
				' RA.UserId=U.UserId AND ' .
				' RA.RegionId="' . $RegionId . '" AND ' . 
				' RA.AuthKey="ROLE" AND RA.AuthAuxValue="ADMINREGION" LIMIT 1;';
			foreach($this->core->query($sQuery) as $row)
			{
				$info['RegionAdminUserId']       = $row['UserId'];
				$info['RegionAdminUserFullName'] = $row['UserFullName'];
			} //foreach
		} //foreach
		return $data;
	}

	public function getCVRegItems($cvregid)
	{
		$sql = "SELECT RegionItem FROM CVRegionItem WHERE RegionId='".
				$cvregid ."' ORDER BY RegionItem";
		$data = array();
		$res = $this->core->query($sql);
		foreach ($res as $row)
		{
			$data[] = $row['RegionItem'];
		}
		return $data;
	}

	// General SQL test function
	public function chkSQLWhere($sql)
	{
		if (substr($sql, 0, 5) == "WHERE")
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function chkSQL($sql)
	{
		if (substr($sql, 0, 6) == "SELECT")
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function querySQLAddTextField($prmQuery, $prmField, $prmValue, $prmOp)
	{
		$Value = trim($prmValue);
		$Query = $prmQuery;
		if ($Value != '')
		{
			if ($prmQuery != '') 
			{
				$Query = $prmQuery . ' '. $prmOp . ' ';
			}
			$Query .= '(' . $prmField . '="' . $Value . '"' . ')';
		}
		return $Query;
	}

	function querySQLAddMemoField($prmQuery, $prmField, $prmValue, $prmOp)
	{
		$Value = trim($prmValue);
		$Query = $prmQuery;
		if ($Value != '')
		{
			if ($prmQuery != '') 
			{
				$Query = $prmQuery . ' '. $prmOp . ' ';
			}
			$Query .= '(' . $prmField . ' LIKE "%' . $Value . '%"' . ')';
		}
		return $Query;
	}

	// Generate SQL from Associative array from Desconsultar Form
	public function genSQLWhereDesconsultar($dat)
	{
		// 2011-01-29 (jhcaiced) Updated method for applying operator between fields
		$dat['D_DisasterSiteNotes'][0] = $dat['QueryGeography']['OP'];

		$e		   = array();
		$e['Eff']  = '';
		$e['Item'] = '';
		// 2009-12-30 (jhcaiced) Try to separate query in logical units
		$QueryItem   = array();
		$QueryItem['Period']    = '';
		$QueryItem['Event']     = '';
		$QueryItem['Cause']     = '';
		$QueryItem['Geography'] = '';
		$QueryItem['Custom']    = '';
		$QueryItem['EEField']   = '';
		$QueryDisasterSerial    = '';
		$QueryDisasterSerialOp  = ' AND ';

		// Remove parameters from list of options...
		foreach($dat as $k => $v)
		{
			// replace D_ by D.
			if (substr($k, 1, 1) == "_")
			{
				$newK = substr_replace($k, ".", 1, 1);
				$dat[$newK] = $v;
				unset($dat[$k]);
			}
			if (substr($k,0,3) == 'prm')
			{
				unset($dat[$k]);
			}
		}

		// Add Custom Query..
		if (isset($dat['QueryCustom']) && !empty($dat['QueryCustom']))
		{
			$QueryItem['Custom'] = trim($dat['QueryCustom']);
			unset($dat['QueryCustom']);
		}
		// Process EEFields...
		$First = true;
		$EEQuery = '';
		foreach($dat['EEFieldQuery'] as $EEField => $QueryParams)
		{
			if (array_key_exists('Type', $QueryParams))
			{
				$QueryTmp = '';
				switch($QueryParams['Type'])
				{
					case 'INTEGER':
					case 'CURRENCY':
						if (isset($QueryParams['Operator']))
						{
							switch($QueryParams['Operator'])
							{
								case '>=':
									if (is_numeric($QueryParams['Value1']))
									{
										$QueryTmp = 'E.' . $EEField . $QueryParams['Operator'] . $QueryParams['Value1'];
									}
								break;
								case '<=':
									if (is_numeric($QueryParams['Value1']))
									{
										$QueryTmp = 'E.' . $EEField . $QueryParams['Operator'] . $QueryParams['Value1'] . ' AND E.' . $EEField . '>=0';
									}
								break;
								case '=':
									if (is_numeric($QueryParams['Value1']))
									{
										$QueryTmp = 'E.' . $EEField . $QueryParams['Operator'] . $QueryParams['Value1'];
									}
								break;
								case '-3':
									if (is_numeric($QueryParams['Value1']) && is_numeric($QueryParams['Value2']))
									{
										$QueryTmp = '(' . 'E.' . $EEField . '>=' . $QueryParams['Value1'] . ' AND ' . 'E.' . $EEField . '<=' . $QueryParams['Value2'] . ')';
									}
								break;
								default:
									$QueryTmp = '(' . '(E.' . $EEField . '>0) OR (E.' . $EEField . '=-1)' . ')';
								break;
							}
						}
					break;
					case 'STRING':
					case 'DATE':
					case 'TEXT':
						if ($QueryParams['Text'] != '')
						{
							$QueryTmp = 'E.' . $EEField . " LIKE '" . $QueryParams['Text'] . "'";
						}
					break;
				} //switch
				if ($QueryTmp != '')
				{	
					if (! $First)
					{
						$EEQuery .= ' ' . $dat['QueryEEField']['OP'] . ' ';
					}
					$First = false;
					$EEQuery .= $QueryTmp;
				} #if
			} //if
		} //foreach
		$QueryItem['EEField'] = $EEQuery;
		// Geography Section Query (GeographyId + DisasterSiteNotes)
		$Query = '';
		$bFirst = true;
		$Field = 'D.GeographyId';
		$GeographyList = $dat[$Field];
		foreach ($GeographyList as $i)
		{
			if (! $bFirst)
			{
				$Query .= ' OR ';
			}
			// Restrict to childs elements only
			$hasChildsSelected = false;
			foreach ($GeographyList as $j)
			{
				if ($i != $j && ($i == substr($j, 0, 5) || $i == substr($j, 0, 10)))
				{
					$hasChildsSelected = true;
					$bFirst = true;
				}
			}
			if (! $hasChildsSelected)
			{
				$Query .= $Field . ' LIKE "' . $i . '%"';
				$bFirst = false;
			}
		} //foreach
		if ($Query != '') 
		{
			$Query = '(' . $Query . ')';
		}
		$Query = $this->querySQLAddMemoField($Query, 'D.DisasterSiteNotes', $dat['D.DisasterSiteNotes'][1], $dat['QueryGeography']['OP']);
		$QueryItem['Geography'] = $Query;
		// Remove data to avoid further processing by old query method..
		unset($dat['D.GeographyId']);
		unset($dat['D.DisasterSiteNotes']);

		// Event Section Query
		$Query = '';
		$Field = 'D.EventId';
		$bFirst = true;
		foreach ($dat[$Field] as $EventId)
		{
			if (! $bFirst)
			{
				$Query .= ' OR ';
			}
			$Query .= $Field . '="' . $EventId . '"';
			$bFirst = false;
		}
		if ($Query != '')
		{
			$Query = '(' . $Query . ')';
		}
		$Query = $this->querySQLAddTextField($Query, 'D.EventDuration', $dat['D.EventDuration'], $dat['QueryEvent']['OP']);
		$Query = $this->querySQLAddMemoField($Query, 'D.EventNotes', $dat['D.EventNotes'][1], $dat['QueryEvent']['OP']);
		$QueryItem['Event'] = $Query;
		unset($dat['D.EventId']);
		unset($dat['D.EventDuration']);
		unset($dat['D.EventNotes']);

		// Cause Section Query
		$Query = '';
		$Field = 'D.CauseId';
		$bFirst = true;
		foreach ($dat[$Field] as $CauseId)
		{
			if (! $bFirst)
			{
				$Query .= ' OR ';
			}
			$Query .= $Field . '="' . $CauseId . '"';
			$bFirst = false;
		}
		if ($Query != '')
		{
			$Query = '(' . $Query . ')';
		}
		$Query = $this->querySQLAddMemoField($Query, 'D.CauseNotes', $dat['D.CauseNotes'][1], $dat['QueryCause']['OP']);
		$QueryItem['Cause'] = $Query;
		unset($dat['D.CauseId']);
		unset($dat['D.CauseNotes']);

		// Effects Query
		$Query = '';
		$bFirst = true;
		foreach($dat as $k => $v)
		{
			if ((substr($k, 2, 6) == 'Effect' || substr($k, 2, 6) == 'Sector') && isset($v[0]))
			{
				if (is_array($v))
				{
					$value_min = isset($v[1]) ? $v[1] : 0;
					if ($value_min == '') { $value_min = 0; }
					$value_max = isset($v[2]) ? $v[2] : 0;
					if ($value_max == '') { $value_max = 0; }
					
					$op = $dat['QueryEffect']['OP'];
					if (! $bFirst)
					{
						$Query .= ' ' . $op . ' ';
					}
					if ($v[0] == '>=' || $v[0] == '<=' || $v[0] == '=')
					{
						$Query .= '(' . $k . ' ' . $v[0] . $value_min . ')';
					}
					elseif ($v[0] == '-1')
					{
						$Query .= '(' . $k . '=' . $v[0] . ' OR ' . $k . '>0)';
					}
					elseif ($v[0] == '0' || $v[0] == '-2')
					{
						$Query .= $k . '=' . $v[0];
					}
					elseif ($v[0] == '-3')
					{
						$Query .= '(' . $k . ' BETWEEN ' . $value_min . ' AND ' . $value_max . ')';
					}
					$bFirst = false;
					unset($dat[$k]);
				}
			}
		} //foreach
		$Query = $this->querySQLAddMemoField($Query, 'D.EffectNotes', $dat['D.EffectNotes'], $dat['QueryEffect']['OP']);
		$Query = $this->querySQLAddMemoField($Query, 'D.EffectOtherLosses', $dat['D.EffectOtherLosses'], $dat['QueryEffect']['OP']);
		unset($dat['D.EffectNotes']);
		unset($dat['D.EffectOtherLosses']);
		$e['Eff'] = $Query;
		
		// Process all other fields...		
		foreach ($dat as $k=>$v)
		{
			if (!empty($v))
			{
				if (is_int($v) || is_float($v))
				{
					$e['Item'] .= "$k = $v AND ";
				}
				elseif ($k == "D.RecordStatus")
				{
					if (!is_array($v))
					{
						$v = explode(' ',$v);						
					}
					$QueryRecordStatus = '';
					$bFirst = true;
					foreach($v as $i)
					{
						if (! $bFirst)
						{
							$QueryRecordStatus .= ' OR ';
						}
						$QueryRecordStatus .= "$k = '$i'";
						$bFirst = false;
					}
				}
				elseif (is_array($v))
				{
					if ($k == "D.DisasterBeginTime")
					{
						if (empty($v[0]))
						{
							$v[0] = '0000';
						}
						$begt = padNumber($v[0], 4);
						if (!empty($v[1]))
						{ 
							$begt .= '-' . padNumber($v[1], 2);
							if (!empty($v[2]))
							{
								$begt .= '-' . padNumber($v[2], 2);
							}
						}
					} elseif ($k == "D.DisasterEndTime")
					{
						$aa = !empty($v[0])? $v[0] : "9999"; //substr($datedb[1], 0, 4);
						$mm = !empty($v[1])? $v[1] : "12";
						$dd = !empty($v[2])? $v[2] : "31";
						$endt = sprintf("%04d-%02d-%02d", $aa, $mm, $dd);
					}
					elseif ($k == "D.DisasterSource")
					{
						// Process text fields with separator AND, OR..
						$Query = '';
						foreach (explode(" ", $v[1]) as $i)
						{
							$i = trim($i);
							if ($i != '')
							{
								$Query .= "$k LIKE '%$i%' ". $v[0] ." "; 
							}
						}
						if ($Query != '')
						{
							$Query = '(' . $Query;
							if ($v[0] == "AND")
							{
								$Query .= "1=1) AND ";
							}
							else
							{
								$Query .= "1!=1) AND ";
							}
							$e['Item'] .= $Query;
						}
					}
					elseif ($k == "D.DisasterSerial")
					{
						// Process serials..
						$QueryDisasterSerialOp = ' AND '; 
						if ($v[0] == 'NOT')
						{
							$QueryDisasterSerialOp = ' AND NOT ';
						}
						if ($v[0] == 'INCLUDE')
						{
							$QueryDisasterSerialOp = ' OR ';
						}
						if (strlen($v[1]) > 0)
						{
							// 2009-12-30 (jhcaiced) This implementation uses the "a in (x,y,z)" construction instead of
							// the OR..OR..OR... this way we can avoid the Depth Tree limit in SQLite
							$bFirst = true;
							$SerialCount = 0;
							$QueryDisasterSerial = $k . ' IN (';
							foreach(explode(" ", $v[1]) as $i)
							{
								if (! $bFirst)
								{
									$QueryDisasterSerial .= ',';
								}
								$bFirst = false;
								$QueryDisasterSerial .= "'$i'";
								$SerialCount++;
							}
							$QueryDisasterSerial .= ')';
						}
					}
				}
				elseif (substr($k, 0, 1) != "_")
				{
					// all minus DC hidden fields _MyField
					$v = trim($v);
					if ($v != '')
					{
						$e['Item'] .= "$k LIKE '%$v%' AND ";
					}
				}
			}
		} //foreach

		if (isset($begt) || isset($endt))
		{
			if (!isset($begt))
			{
				$begt = "0000-00-00"; // $datedb[0];
			}
			if (!isset($endt))
			{
				$endt = "9999-12-31"; //$datedb[1];
			}
			$QueryItem['Period'] = "D.DisasterBeginTime BETWEEN '$begt' AND '$endt'";
		}
		$lan = "spa"; // select from local languages of database..
		//$e['Item'] .= "D.EventId=V.EventId AND D.CauseId=C.CauseId AND D.GeographyId=G.GeographyId ".
        //          "AND V.LangIsoCode='$lan' AND C.LangIsoCode='$lan' AND G.LangIsoCode='$lan'";
        $e['Item'] = substr($e['Item'],0,-4);

		// Finally, build the WHERE Conditional Query using all parts...
		// Add the JOIN conditions with all other tables...
		$WhereQuery  = "WHERE ";
		$WhereQuery .= '(D.DisasterId=E.DisasterId AND D.EventId=V.EventId AND D.CauseId=C.CauseId AND D.GeographyId=G.GeographyId) ';

		$WhereQuery1 = '';
		if (count($e) > 0)
		{
			$bFirst = true;
			foreach ($e as $k => $v)
			{
				$v = trim($v);
				if ($v != '')
				{
					if (! $bFirst)
					{
						$WhereQuery1 .= ' AND ';
					}
					$bFirst = false;
					$WhereQuery1 .= '(' . $v . ') ';
				}
			} //foreach
		}
		$QueryItem['Other'] = $WhereQuery1;
		
		$bFirst = true;
		$WhereQuery2 = '';
		foreach ($QueryItem as $QueryKey => $QueryValue)
		{
			if ($QueryValue != '')
			{
				if (! $bFirst)
				{
					$WhereQuery2 .= ' AND ';
				}
				$bFirst = false;
				$WhereQuery2 .= ' (' . $QueryValue . ')';
			}
		}

		if ($QueryRecordStatus != '')
		{
			$WhereQuery .= ' AND (' . $QueryRecordStatus . ') ';
		}

		if ( ($WhereQuery2 != '') || ($QueryDisasterSerial != '') || ($QueryRecordStatus != '') )
		{
			$WhereQuery .= ' AND (';
			if ($WhereQuery2 != '')
			{
				$WhereQuery .= ' (' . $WhereQuery2 . ') ';
				if ($QueryDisasterSerial != '')
				{
					$WhereQuery .= ' ' . $QueryDisasterSerialOp;
				}
			}
			if ($QueryDisasterSerial != '')
			{
				$WhereQuery .= ' (' . $QueryDisasterSerial . ') ';
			}
			$WhereQuery .= ')';
		}
    	return $WhereQuery;
	} //function

	// Count number of records in result
	public function genSQLSelectCount($whr)
	{
		$sql = "SELECT COUNT(D.DisasterId) as counter FROM Disaster AS D, EEData AS E, Event AS V, Cause AS C, Geography AS G ";
		if ($this->chkSQLWhere($whr))
		{
			return ($sql . $whr);
		}
		return false;
	}

	// Generate SQL to data lists
	public function genSQLSelectData ($whr, $fld, $order)
	{
		$fld = str_ireplace("D.EventId", "V.EventName", $fld); //Join with Event table
		$fld = str_ireplace("D.EventName", "V.EventName", $fld); //Join with Event table
		$fld = str_ireplace("D.CauseId", "C.CauseName", $fld); //Join with Cause table
		$fld = str_ireplace("D.CauseName", "C.CauseName", $fld); //Join with Cause table
		$fld = str_ireplace("D.GeographyCode", "G.GeographyCode", $fld);
		$fld = str_ireplace("D.GeographyFQName", "G.GeographyFQName", $fld);
		// Process fields to show
		$sql = "SELECT ". $fld ." FROM Disaster AS D, EEData AS E, Event AS V, Cause AS C, Geography AS G ";
		if ($this->chkSQLWhere($whr))
		{
			$sql .= $whr;
			if (!empty($order))
			{
				$sql .= "ORDER BY $order";
			}
			return ($sql);
		}
		else
		{
			return false;
		}
	}

	public function getGroupFieldName($prmGroup)
	{
		$GroupFieldName = '';
		$gp = explode("|", $prmGroup);
		switch ($gp[1])
		{
			case "D.DisasterBeginTime":
				// Check if exist Operator(s): Year, month, week, day
				if (!empty($gp[0]))
				{
					$GroupFieldName = substr($gp[1],2) ."_". $gp[0] ; // delete last ,'_',
				}
				else
				{
					$GroupFieldName = $gp[1];
				} //if
			break;
			case "D.GeographyId":
				// Lev is 0, 1, .. N 
				$lev = isset($gp[0]) ? $gp[0]: 0;
				$off = ($lev * 5) + 5;
				$GroupFieldName = substr($gp[1],2) .'_' . $lev;
			break;
			default:
				if (!empty($gp[1]))
				{
					$GroupFieldName = $gp[1];
				}
			break;
		} //switch
		return $GroupFieldName;
	} //function

	// Generate Special SQL with grouped fields
	public function genSQLProcess ($dat, $opc)
	{
		$sql = '';
		$sel = array();
		$whr = array();
		$grp = array();
		$where_extra = array();
		// Vars to be agrupated (BeginTime, Geography, Event, Cause)
		$j = 1;
		// Group has this struct: FUNC|VAR
		foreach ($opc['Group'] as $item)
		{
			$gp = explode("|", $item);
			switch ($gp[1])
			{
				case "D.DisasterBeginTime":
					// Check if exist Operator(s): Year, month, week, day
					if (!empty($gp[0]))
					{
						// Error with strftime when date contain '00' like '1997-06-00'
						switch ($gp[0])
						{
							case "YEAR":
								$func = "SUBSTR(". $gp[1] .", 1, 4) "; #%Y
								$where_extra[] = 'LENGTH(' . $gp[1] . ')>=4';
							break;
							case "YMONTH":
								$func = "SUBSTR(". $gp[1] .", 1, 7) "; #%Y-%m
								$where_extra[] = 'LENGTH(' . $gp[1] . ')>=7';
							break;
							case "MONTH":
								$func = "SUBSTR(". $gp[1] .", 6, 2) "; #%m
								$where_extra[] = 'LENGTH(' . $gp[1] . ')>=7';
							break;
							case "YWEEK":
								$func = "STRFTIME('%Y-%W', ". $gp[1] .") "; #%Y-%W
								$where_extra[] = 'LENGTH(' . $gp[1] . ')>=10';
							break;
							case "WEEK":
								$func = "STRFTIME('%W', ". $gp[1] .") "; #%W
								$where_extra[] = 'LENGTH(' . $gp[1] . ')>=10';
							break;
							case "YDAY":
								$func = "SUBSTR(". $gp[1] .", 1, 10) "; #%Y-%m-%d
								$where_extra[] = 'LENGTH(' . $gp[1] . ')>=10';
							break;
							case "DAY":
								$func = "STRFTIME('%j', ". $gp[1] .") "; #%j
								$where_extra[] = 'LENGTH(' . $gp[1] . ')>=10';
							break;
						} //switch
						$sel[$j] = $func . ' AS '. substr($gp[1],2) ."_". $gp[0] ; // delete last ,'_',
						$grp[$j] = $func;
					}
					else
					{
						$sel[$j] = $gp[1];
						$grp[$j] = $gp[1];
					} //if
					// 100, 10, 5, 1 years, month
					// $sta = explode("|", $opc['Stat']);
				break;
				case "D.GeographyId":
					// Lev is 0, 1, .. N 
					$lev = isset($gp[0]) ? $gp[0]: 0;
					$off = ($lev * 5) + 5;
					$sel[$j] = "SUBSTR(". $gp[1] .", 1, $off) AS ". substr($gp[1],2) ."_$lev";
					$grp[$j] = "SUBSTR(". $gp[1] .", 1, $off)";
				break;
				default:
					if (!empty($gp[1]))
					{
						$sel[$j] = $gp[1];
						$grp[$j] = $gp[1];
					}
				break;
			} //switch
			$j++;
		} #foreach
		
		// Process Field in select and group
		if (!is_array($opc['Field']))
		{
			$FieldList[0] = $opc['Field'];
		}
		else
		{
			$FieldList = $opc['Field'];
		}

		foreach ($FieldList as $Field)
		{
			// Field(s) to show
			$fl = explode("|", $Field);
			$FieldName  = $fl[0];
			$FieldOp    = $fl[1];
			$FieldValue = $fl[2];
			// 2009-11-30 (jhcaiced) This is un ugly fix, we need to change this...
			if ( ($FieldName == 'D.EffectFarmingAndForestQ') ||
			     ($FieldName == 'D.EffectLiveStockQ') ||
			     ($FieldName == 'D.EffectRoadsQ') ||
			     ($FieldName == 'D.EffectEducationCentersQ') ||
			     ($FieldName == 'D.EffectMedicalCentersQ'))
			{
				$FieldName = substr($FieldName,0,-1);
			}			
			// SUM > 0 values
			if ($FieldOp == ">")
			{
				$sel[$j] = "SUM(". $FieldName .") AS ". substr($FieldName,2);
				$whr[$j] = "OR ". $FieldName . $FieldOp . $FieldValue;
			}
			elseif ($FieldOp == "S")
			{
				// S, code to SECTORS 
				$sel[$j] = "SUM(ABS(". $FieldName .")) AS ". substr($FieldName, 2);
				$whr[$j] = "OR ". $FieldName . " = " . $FieldValue;
			}
			else
			{
				// Count Records
				$sel[$j] = 'COUNT('. $FieldName .') AS ' . substr($FieldName, 2) . '_';
				// Counts Records with "Hay"
				if ($FieldOp == '=')
				{
					$whr[$j] = "OR (". $FieldName . $FieldOp . $FieldValue . " OR ". $FieldName .">0)";
				}
			}
			$j++;
		} //foreach
		
		// Code Select
		if ($this->chkSQLWhere($dat))
		{
			$selec = implode(", ", $sel);
			$where = implode(" ", $whr);
			if (!empty($whr))
			{
				#$where = "AND (1!=1 AND $where)";
				$where = 'AND (' . $where . ')';
			}
			$group = implode(', ', $grp);
			$where2 = implode(' AND ', $where_extra);
			$sql = $this->genSQLSelectData ($dat, $selec, '');
			$sql .= $where;
			$sql .= ' AND (' . $where2 . ')';
			$sql .= ' GROUP BY ' . $group;
		}
		return $sql;
	} //function

	// Reformat array setting to arr[X1] = array {a, b, c, d..}
	function prepareList ($dl, $mode)
	{
		$res = array();
		$j = 0;
		$creg = $this->getDBInfoValue('IsCRegion');
		foreach ($dl as $it)
		{	
			foreach ($it as $k=>$i)
			{
				$val = $i;
				if (substr($k,0,11) == "GeographyId")
				{
					if ($mode == "GRAPH")
					{
						$val = $this->getGeoNameById($i);
					}
					elseif ($mode == "MAPS")
					{
						$val = $this->getObjectNameById($i, DI_GEOGRAPHY);
						// in VirtualRegion set base prefix - 
						if ($creg)
						{
							if ($j == 0)
							{
								$res['CVReg'] = array();
							}
							array_push($res['CVReg'], substr($i, 0, 5));
						}
						if ($j == 0)
						{
							$glv = $k; // save key of GeographyId
						}
					}
				}
				elseif ($j == 0)
				{
					$eff = $k; // save key of Effectvar
				}
				if ($j == 0)
				{
					$res[$k] = array();
				}
				array_push($res[$k], $val);
			}
			$j++;
		}
		// Sorting list in maps to order legend - ORDER BY not found with GROUP BY in sqlite3 ??
		if ($mode == "MAPS")
		{
			if ($creg)
			{
				array_multisort($res[$eff], $res[$glv], $res['CVReg']);
			}
			else
			{
				array_multisort($res[$eff], $res[$glv]);	
			}
		}
		return $res;
	}
	
	// Print results like associative array or fields separate by Tabs
	function printResults($dl, $exp, $mode)
	{
		$txt = '';
		// Get results
		if (!empty($dl))
		{
			$j = 0;
			foreach ($dl as $k=>$i)
			{
				foreach (array_keys($i) as $idx)
				{
					if (substr($idx,0,11) == "GeographyId")
					{
						switch ($mode)
						{
							case "CODE": 
								$dl[$j][$idx] = $this->getObjectNameById($i[$idx], DI_GEOGRAPHY);
							break;
							case "NAME":
								$dl[$j][$idx] = $this->getGeoNameById($i[$idx]);
							break;
							case "CODENAME":
								$dl[$j][$idx] = $this->getObjectNameById($i[$idx], DI_GEOGRAPHY) ." | ". $this->getGeoNameById($i[$idx]);
							break;
							default:
								$dl[$j][$idx] = "";
							break;
						} //switch
					}
					elseif (is_numeric($dl[$j][$idx]) && empty($exp))
					{
						$dl[$j][$idx] = number_format($dl[$j][$idx], 0, ',', ' ');
					}
				} //foreach
				if (!empty($exp))
				{
					//$txt = '';
					foreach (array_values($dl[$j]) as $vals)
					{
						if ($vals == -1)
						{ 
							$myv = "YES"; 
						}
						else
						{
							$myv = $vals;
						}
						if ($exp == 'csv')
						{
							$sep = ",";	// use comma separator to CSV
						}
						else
						{
							$sep = "\t";// use tab separator to XLS (default option)
						}
						if (is_numeric($myv))
						{
							$txt .= $myv . $sep;
						}
						else
						{
							$txt .= '"'. $myv .'"'. $sep;
						}
					} //foreach
					$txt .= "\n";
				} //if exp
				$j++;
			} //foreach
		} //if !empty
		if (!empty($exp))
		{
			# Bug #012: Convert encoding of file to make it easier to open in Excel
			return mb_convert_encoding($txt, 'iso-8859-1', 'utf-8');
			#return $txt;
		}
		else
		{
			return $dl;
		}
	}

	// Print results like json array to Javascript
	function hash2json($dlist)
	{
		$js = array();
		foreach ($dlist as $ky=>$vl)
		{
			$js[$ky] = "{";
			foreach ($vl as $k=>$v)
			{
				if ($k == "DisasterBeginTime")
				{
					$dt = explode("-", $v);
					if (!isset($dt[0]))
					{
						$dt[0] = 0;
					}
					if (!isset($dt[1]))
					{
						$dt[1] = 0; 
					}
					if (!isset($dt[2]))
					{
						$dt[2] = 0; 
					}
					$js[$ky] .= "'". $k ."[0]':'". $dt[0] ."', '". $k ."[1]':'". $dt[1] ."', '". $k ."[2]':'". $dt[2] ."', ";
				}
				else
				{
					$js[$ky] .= "'$k': '$v', ";
				}
			} //foreach
			$js[$ky] .= "'_REG': '". $this->RegionId . "'";
			$js[$ky] .= "}";
		}
		return $js;
	} //function
  
	// SET SQL TO TOTALIZATION RESULTS
	function totalize($sql)
	{
		$sq = explode("GROUP", $sql);
		return $sq[0] . " GROUP BY null";
	}

	function getQueryDetails($dic, $post)
	{
		$info = $lsf = array();
		$dinf = $this->getDBInfo();
		$info['TITLE'] = "";
		if (isset($post['_M+Field']))
		{
			$fld = explode("|", $post['_M+Field']);
			$fd0 = substr($fld[0],2);
			$fd1 = substr($fd0, 0, -1);
			if (isset($dic["MapOpt". $fd0 ."_"][0]))
			{
				$info['TITLE'] = $dic["MapOpt". $fd0 ."_"][0];
			}
			elseif (isset($dic[$fd0][0]))
			{
				$info['TITLE'] = $dic[$fd0][0];
			}
			elseif (isset($dic[$fd1][0]))
			{
				$info['TITLE'] = $dic[$fd1][0];
			}
		}
		$info['LEVEL'] = "";
		if (isset($post['_M+Type']) && !(isset($post['_VREG']) && $post['_VREG'] == "true"))
		{
			$fld = explode("|", $post['_M+Type']);
			$val = $this->loadGeoLevById($fld[0]);
			$info['LEVEL'] = $val[0];
		}
		$info['EXTENT'] = $dinf['GeoLimitMinX'] ." ". $dinf['GeoLimitMaxX'] ." ". $dinf['GeoLimitMinY'] ." ". $dinf['GeoLimitMaxY'];
		$info['KEYWORDS'] = $dinf['RegionLabel'];
		//Process post
		foreach ($post as $k=>$v)
		{
			$k = substr($k,2);
			// 2009-12-30 (jhcaiced) Remove CR LF from some items which causes some javascript issues later...
			if (array_key_exists(0, $v))
			{
				$v[0] = trim(ereg_replace("[\r\n]", '', $v[0]));
			}
			if (array_key_exists(1, $v))
			{
				$v[1] = trim(ereg_replace("[\r\n]", '', $v[1]));
			}
			if ($k == "GeographyId")
			{
				foreach($v as $itm)
				{
					$lsg[] = $this->getGeoNameById($itm);
				}
				$info['GEO'] = implode(", ", $lsg);
			}
			elseif ($k == "EventId")
			{
				foreach($v as $itm)
				{
					$lse[] = $this->getObjectNameById($itm, DI_EVENT);
				}
				$info['EVE'] = implode(", ", $lse);
			}
			elseif ($k == "CauseId")
			{
				foreach($v as $itm)
				{
					$lsc[] = $this->getObjectNameById($itm, DI_CAUSE);
				}
				$info['CAU'] = implode(", ", $lsc);
			}
			elseif ($k == "DisasterBeginTime")
			{
				$info['BEG'] = $v[0];
			}
			elseif ($k == "DisasterEndTime")
			{
				$info['END'] = $v[0];
			}
			elseif ($k == "DisasterSource" && !empty($v[1]))
			{
				$info['SOU'] = $v[1];
			}
			elseif ($k == "DisasterSerial" && !empty($v[1]))
			{
				$info['SER'] = $v[1];
			}
			elseif (substr($k, 0, 6) == "Effect" && isset($v[0]) && isset($dic[$k][0]))
			{
				$opt = "";
				if ($v[0] == "=" || $v[0] == ">=" || $v[0] == "<=")
				{
					$opt = "(". $v[0] . $v[1] .")";
				}
				elseif ($v[0] == "-3")
				{
					$opt = "(". $v[1] ."-". $v[2] .")";
				}
				$lsf[] = $dic[$k][0] . $opt;
			}
		} //foreach
		if (!empty($lsf))
		{
			$info['EFF'] = implode(", ", $lsf);
		}
		return $info;
	}
  
	// DICTIONARY FUNCTIONS
	function existLang($LangIsoCode)
	{
		$answer = false;
		if ($LangIsoCode != "")
		{
			$sql = "select LangIsoCode from Language where LangIsoCode='". $LangIsoCodeID ."'";
			foreach ($this->base->query($sql) as $row)
			{
				if (count($row) > 0)
				{
					$answer = true;
				}
			} //foreach
		}
		return $answer;
	}
  
	function queryLabel($labgrp, $labname, $langID)
	{
		$data = '';
		$sql = "select d.DictTranslation as DTr, d.DictTechHelp as DTe, ".
			"d.DictBasDesc as DBa, d.DictFullDesc as DFu from Dictionary d,".
			" LabelGroup g where (g.LGName like '" . $labgrp . "%') ".
			"and (d.LangIsoCode='" . $langID . "') and (g.LabelName= '".
			$labname ."') and (d.DictLabelID = g.DictLabelID) ".
			"order by g.LGorder";
		foreach ($this->base->query($sql) as $row)
		{
			$data = array('DictTranslation'=>$row['DTr'],//utf8_encode($row['DTr']),
			              'DictTechHelp'=>$row['DTe'],//utf8_encode($row['DTe']),
			              'DictBasDesc'=>$row['DBa'],//utf8_encode($row['DBa']),
			              'DictFullDesc'=>$row['DFu']);//utf8_encode($row['DFu']));
		}
		return $data;
	}

	function queryLabelsFromGroup($labgrp, $langID, $withLabelGroupPrefix=true)
	{
		$dictio = '';
		$sql = "SELECT g.LGName as lgn, g.LabelName as lbn, DictTranslation, ".
			"DictTechHelp, DictBasDesc, DictFullDesc from Dictionary d,".
			" LabelGroup g where (g.LGName like '". $labgrp ."%') and ".
			"(d.LangIsoCode='". $langID ."') and (d.DictLabelID = g.DictLabelID) ".
			"order by g.LGorder";
		try
		{
			foreach ($this->base->query($sql) as $row)
			{
				$grp = explode("|", $row['lgn']);
				if ($withLabelGroupPrefix)
				{
					$dictlabel = $grp[0];
				}
				else
				{
					$dictlabel = '';
				}
				$dictlabel .= $row['lbn'];
				$dictio[$dictlabel] = array(
					$row['DictTranslation'],
					$row['DictTechHelp'],
					$row['DictBasDesc'],
					$row['DictFullDesc']
				);
			} // foreach
		}
		catch (Exception $e)
		{
			showErrorMsg($e->getMessage());
		}
		return $dictio;
	}

	function querySecLabelFromGroup($labgrp, $langID)
	{
		$sql = "select g.LGName as lgn, g.LabelName as lbn, DictTranslation, ".
			"DictTechHelp, DictBasDesc, DictFullDesc from Dictionary d,".
			" LabelGroup g where (g.LGName like '". $labgrp ."%') and ".
			"(d.LangIsoCode='". $langID ."') and (d.DictLabelID = g.DictLabelID) ".
			"order by g.LGorder";
		foreach ($this->base->query($sql) as $row)
		{
			$grp = explode("|", $row['lgn']);
			$dictio[$grp[0].$row['lbn']] = $grp[2];
		}
		return $dictio;
	}

	function loadAllGroups($langID)
	{
		$sql = "select g.LGName as lgn, g.LabelName as lbn, DictTranslation, ".
			"DictTechHelp, DictBasDesc, DictFullDesc from Dictionary d,".
			" LabelGroup g where (d.LangIsoCode='" . $langID . "') and ".
			"(d.DictLabelID = g.DictLabelID) order by g.LGorder";
		foreach ($this->base->query($sql) as $row)
		{
			$grp = explode("|", $row['lgn']);
			$dictio[$grp[0].$row['lbn']] = array($row['DictTranslation'], 
				$row['DictTechHelp'], $row['DictBasDesc'], $row['DictFullDesc']);
		}
		return $dictio;
	}

	function loadAllLabels()
	{
		$sql = "select DictLabelID, LGName, LabelName from LabelGroup order by LGName";
		$diction = array();
		foreach ($this->base->query($sql) as $row)
		{
			$dictio[$row['DictLabelID']] = $row['LGName'] .'|'. $row['LabelName'];
		}
		return $dictio;
	}

	function loadLanguages($prmLangStatus='')
	{
		$sQuery = "SELECT * FROM Language";
		if ($prmLangStatus != '')
		{
			$sQuery .= " WHERE LangStatus=" . $prmLangStatus;
		}
		$langlist = array();
		foreach ($this->base->query($sQuery) as $row)
		{
			$langlist[$row['LangIsoCode']] = $row['LangLocalName'];
		}
		return $langlist;
	}
  
	function findDicLabelID($labgrp, $labname)
	{
		$sql = "select DictLabelID from LabelGroup where LGName like '". $labgrp .
			"%' and LabelName='". $labname ."';";
		foreach ($this->base->query($sql) as $row)
		{
			$diclabelID = $row['DictLabelID'];
		}
		return $diclabelID;
	}
  
	function existLabel($diclabelID)
	{
		$answer = false;
		$sql = "select DictLabelID from Dictionary where DictLabelID='". 
			$diclabelID . "';";
		foreach ($this->base->query($sql) as $row)
		{
			$diclabelID = $row['DictLabelID'];
			if ($diclabelID != null)
			{
				$answer = true;
			}
		}
		return $answer;
	}

	// Check!!
	function updateDicLabel($labgrp, $labname, $translation, $techhelp, $basdesc, $fulldesc, $langID)
	{
		if (!$this->existLang($langID))
		{
			return false;
		}
		else
		{
			$diclabID = $this->findDicLabelID($labgrp, $labname);
			if (!$this->existLabel($diclabID))
			{
				$sql = "insert into Dictionary values ('". $diclabID ."','". $langID .
					"','". $translation ."','". $techhelp ."','". $basdesc ."','".
					$fulldesc ."');";
			}
			else
			{
				$sql = "update Dictionary set LangID='". $langID ."', DicTranslation='".
					$translation ."', DicTechHelp='". $techhelp ."', DicBasDesc='".
    	          $basdesc ."', DicFullDesc='".$fulldesc .
        	      "' where DicLabelID='". $diclabID ."';";
        	    $this->base->exec($sql);
			}
		}
		return true;
	}
  
	public function getCountryName($prmCountryIso)
	{
		$query = "SELECT * FROM Country WHERE CountryIso='" . $prmCountryIso . "'";
		foreach($this->base->query($query) as $row)
		{
			$CountryName = $row['CountryName'];
		}
		return $CountryName;
	}
  
	// Check
	function rebuildCore($fcore)
	{
		return true;
	}
} // end class
</script>
