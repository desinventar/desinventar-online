<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class Query extends PDO
{
	public $sRegionId = "";
	public $dreg = null;
	public $core = null;
	public $dbfile = '';

	public function __construct() {
		if (!extension_loaded('pdo')) {
		  dl( "pdo.so" );
		  dl( "pdo_sqlite.so" );
		}
		try {
			$num_args = func_num_args();

			// Open core.db - Users, Regions, Auths.. 
			$dbc = CONST_DBCORE;
			if (file_exists($dbc))
				$this->core = new PDO("sqlite:" . $dbc);
			else
				$this->rebuildCore($dbc); // Rebuild data from directory..

			// Open base.db - DI's Basic database
			$dbb = CONST_DBBASE;
			if (file_exists($dbb))
				$this->base = new PDO("sqlite:" . $dbb);

			if ($num_args > 0) {
				$this->sRegionId = func_get_arg(0);
			}
			
			if ($this->sRegionId != '') {
				$this->setDBConnection($this->sRegionId);
			} else {
				$this->setDBConnection('core');
			} //if
		} catch (Exception $e) {
			showErrorMsg("Error !: " . $e->getMessage());
			die();
		}
	}

	public function getDBFile($prmRegionId) {
		$DBFile = VAR_DIR;
		if ($prmRegionId != '') {
			if ($prmRegionId == 'core') {
				$DBFile .= "/main/core.db";
			} else {
				$DBFile .= "/database/" . $prmRegionId ."/desinventar.db";
			}
		}
		return $DBFile;
	}
	
	public function setDBConnection($prmRegionId) {
		$iReturn = ERR_NO_ERROR;
		$DBFile = VAR_DIR;
		if ($prmRegionId != '') {
			if ($prmRegionId == 'core') {
				$DBFile .= "/main/core.db";
			} else {
				$DBFile .= "/database/" . $prmRegionId ."/desinventar.db";
			}
			if (file_exists($DBFile)) {
				try {
					$this->dreg = new PDO("sqlite:" . $DBFile);
					/*** set the error reporting attribute ***/
					$this->dreg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->sRegionId = $prmRegionId;
					$this->dbfile = $DBFile;
				} catch (PDOException $e) {
					showErrorMsg($e->getMessage());
				}
			} else {
				$iReturn = ERR_NO_DATABASE;			
			} //if
		} else {
			$this->dreg = null;
			$this->sRegionId = '';
		}
		return $iReturn;
	}
  
	public function getassoc($sQuery) {
		$data = false;
		if (!empty($sQuery)) {
			$data = array();
			try {
				$i = 0;
				foreach($this->dreg->query($sQuery, PDO::FETCH_ASSOC) as $row) {
					foreach($row as $key=>$val)
						$data[$i][$key] = $val;
					$i++;
				}
			} catch (Exception $e) {
				showErrorMsg($e->getMessage());
			}
		} else {
			echo "Empty Query !!";
		}
		return $data;
	}

	public function getresult($qry) {
		$rst = null;
		$row = null;
		try {
			if ($this->dreg != null) {
				$rst = $this->dreg->query($qry);
				$row = $rst->fetch(PDO::FETCH_ASSOC);
			}
		} catch (Exception $e) {
			showErrorMsg($e->getMessage());
		}
		return $row;
	}

	public function getnumrows($qry) {
		$rst = $this->getassoc($qry);
		return count($rst);
	}

	// STANDARDS FUNCTION TO GET GENERAL EVENTS, CAUSES LISTS
	function loadEvents($type, $status, $lang) {
		$data = array();
		if ($type == "BASE")
			$data = $this->getBasicEventList($lang);
		else
			$data = $this->getRegionEventList($type, $status, $lang);
		return $data;
	}

	// active : active, inactive  | types : predef, user | empty == all
	function loadCauses($type, $status, $lang) {
		$data = array();
		if ($type == "BASE")
			$data = $this->getBasicCauseList($lang);
		else
			$data = $this->getRegionCauseList($type, $status, $lang);
		return $data;
	}

	public function getBasicEventList($lg) {
		$sql = "SELECT EventId, EventName, EventDesc FROM Event ".
				"WHERE LangIsoCode='$lg' ORDER BY EventName";
		$data = array();
		$res = $this->base->query($sql);
		foreach($res as $row)
			$data[$row['EventId']] = array($row['EventName'], $row['EventDesc']);
		return $data;
	}

	public function getBasicCauseList($lg) {
		$sql = "SELECT CauseId, CauseName, CauseDesc FROM Cause ".
				"WHERE LangIsoCode='$lg' ORDER BY CauseName";
		$data = array();
		$res = $this->base->query($sql);
		foreach($res as $row)
			$data[$row['CauseId']] = array($row['CauseName'], $row['CauseDesc']);
		return $data;
	}

	public function getRegionEventList($type, $status, $lang) {
		if ($type == "PREDEF")
			$sqlt = "EventPreDefined=1";
		else if ($type == "USER")
			$sqlt = "EventPreDefined=0";
		else
			$sqlt = "'1=1'";	// all
		if ($status == "active")
			$sqls = "EventActive=1";
		else
			$sqls = "'1=1'"; // all
		$sql = "SELECT * FROM Event WHERE ". $sqls ." AND ". $sqlt ." ORDER BY EventName";
		$data = array();
		$res = $this->dreg->query($sql);
		foreach($res as $row)
			$data[$row['EventId']] = array($row['EventName'], str2js($row['EventDesc']), $row['EventActive']);
		return $data;
	}

	public function getRegionCauseList($type, $status, $lang) {
		if ($type == "PREDEF")
			$sqlt = "CausePredefined=1";
		else if ($type == "USER")
			$sqlt = "CausePredefined=0";
		else
			$sqlt = "'1=1'";	// all
		if ($status == "active")
			$sqls = "CauseActive=1";
		else
			$sqls = "'1=1'"; // all
		$sql = "SELECT * FROM Cause WHERE ". $sqls ." AND ". 
		$sqlt ." ORDER BY CauseName";
		$data = array();
		$res = $this->dreg->query($sql);
		foreach($res as $row) {
			$data[$row['CauseId']] = array($row['CauseName'], str2js($row['CauseDesc']), $row['CauseActive']);
		} //foreach
		return $data;
	} //function

  /***** READ OBJECTS :: EVENT, CAUSE, GEOGRAPHY, GEOLEVEL READ *****/
  public function isvalidObjectToInactivate($id, $obj) {
    switch ($obj) {
      case DI_EVENT:		$whr = "EventId='$id'";		break;
      case DI_CAUSE:		$whr = "CauseId='$id'";		break;
      case DI_GEOGRAPHY:	$whr = "GeographyId like '$id%'";		break;
    }
    $sql = "SELECT COUNT(DisasterId) AS counter FROM Disaster WHERE $whr ";
    $res = $this->getresult($sql);
    if ($res['counter'] > 0)
      return false;
    else
      return true;
  }

  public function isvalidObjectName($id, $sugname, $obj) {
    switch ($obj) {
      case DI_EVENT:			$name = "EventName";			$table = "Event";			$fld = "EventId";			break;
      case DI_CAUSE:			$name = "CauseName";			$table = "Cause";			$fld = "CauseId";			break;
      case DI_GEOGRAPHY:	$name = "GeographyCode";	$table = "Geography"; $fld = "GeographyId";	break;
      case DI_GEOLEVEL:		$name = "GeoLevelName";		$table = "GeoLevel"; 	$fld = "GeoLevelId";	break;
      case DI_EEFIELD:		$name = "EEFieldLabel";		$table = "EEField"; 	$fld = "EEFieldId";		break;
      case DI_DISASTER:		$name = "DisasterSerial";	$table = "Disaster"; 	$fld = "DisasterId";	break;
      case DI_REGION:			$name = "RegionId";				$table = "Region"; 		$fld = "RegionId";	break;
      default:						return null; 		break;
    }
    if ($sugname == "")
      return false;
    // uhmm, for spanish only..
    $tilde = array('á','é','í','ó','ú');
    $vocal = array('a','e','i','o','u');
    $sugname = str_replace($tilde, $vocal, $sugname);
    $sql = "SELECT COUNT($fld) as counter FROM $table WHERE $name LIKE '". 
          $sugname ."' AND $fld != '$id'";
    $res = $this->getresult($sql);
    if ($res['counter'] == 0)
      return true;
    else
      return false;
  }
  
  public function getObjectNameById($id, $obj) {
    switch ($obj) {
      case DI_EVENT:		$name = "EventName";		$table = "Event";		$fld = "EventId";		break;
      case DI_CAUSE:		$name = "CauseName";		$table = "Cause";		$fld = "CauseId";		break;
      case DI_GEOGRAPHY:	$name = "GeographyCode";	$table = "Geography"; 	$fld = "GeographyId";	break;
      case "GEOCODE":		$name = "GeographyId";		$table = "Geography"; 	$fld = "GeographyCode";	break;
      case DI_GEOLEVEL:		$name = "GeoLevelName";		$table = "GeoLevel"; 	$fld = "GeoLevelId";	break;
      default:				return null; 		break;
    }
    $sql = "SELECT $name FROM $table WHERE $fld = '$id'";
    $res = $this->getresult($sql);
    if (isset($res[$name]))
      return $res[$name];
    else
      return null;
  }

  public function getObjectColor($val, $obj) {
    switch ($obj) {
      case DI_EVENT:			$color = "EventRGBColor";			$table = "Event";			$fld = "EventName";		break;
      case DI_CAUSE:			$color = "CauseRGBColor";			$table = "Cause";			$fld = "CauseName";		break;
      default:						return null; 		break;
    }
    $sql = "SELECT $color FROM $table WHERE $fld = '$val'";
    $res = $this->getresult($sql);
    if (isset($res[$color]))
      return $res[$color];
    else
      return null;
  }

/*** GEOGRAPHY & GEO-LEVELS QUERIES  ***/
  function buildGeographyId($fid, $lev) {
    $sql = "SELECT MAX(GeographyId) AS max FROM Geography WHERE GeographyId ".
            "LIKE '$fid%' AND GeographyLevel = $lev ORDER BY GeographyId";
    $data = array();
    $res = $this->getresult($sql);
    $myid = (int)substr($res['max'], -5);
    $myid += 1;
    $newid = $fid . sprintf("%05s", $myid);
    return $newid;
  }

  function getGeoNameById($geoid) {
	if ($geoid == "")
	  return null;
	$sql = "SELECT GeographyName FROM Geography WHERE 1!=1";
	$levn = (strlen($geoid) / 5);
	for ($n = 0; $n < $levn; $n++) {
		$len = 5 * ($n + 1);
		$geo = substr($geoid, 0, $len);
		$sql .= " OR GeographyId='". $geo ."'";
	}
	$sql .= " ORDER BY GeographyLevel";
	$data = "";
	$res = $this->dreg->query($sql);
	foreach($res as $row)
	  $data .= $row['GeographyName'] . "/";
//	$sql = "SELECT GeographyFQName FROM Geography WHERE GeographyId='". $geoid ."'";
//	$data = $this->dreg->query($sql);
	return $data;
  }

  /*** GEOGRAPHY ***/
  /* uhmm... testing function.. 
  function loadGeoTree() {
    $data = array();
    $sql = "SELECT GeographyId, GeographyCode, GeographyName, GeographyLevel".
            " FROM Geography WHERE GeographyActive=1 ORDER BY GeographyId";
    $res = $this->dreg->query($sql);
    $max = $this->getMaxGeoLev();
    foreach($res as $row) {
      $lev = $row['GeographyLevel'];
      $key = $row['GeographyId'] ."|". str2js($row['GeographyName']);
      if ($lev == 0) {
        $par0 = $key;
        $data[$par0] = array();
      }
      elseif ($lev == 1) {
        $par1 = $key;
        $ele[$key] = array();
        $data[$par0] = array_merge($data[$par0], $ele);
      }
      elseif ($lev == 2) {
        $ele[$key] = array();
        $data[$par0][$par1] = array_merge($data[$par0][$par1], $ele);
      }
      $ele = null;
    }
    return $data;
  }*/
  
  function loadGeography($level) {
    if (!is_numeric($level) && $level >= 0)
      return null;
    $sql = "SELECT * FROM Geography WHERE GeographyLevel=" . $level . 
           " AND GeographyId NOT LIKE '%00000' ORDER BY GeographyName";
    $data = array();
    $res = $this->dreg->query($sql);
    foreach($res as $row)
      $data[$row['GeographyId']] = array($row['GeographyCode'], str2js($row['GeographyName']), $row['GeographyActive']);
    return $data;
  }

  function loadGeoChilds($geoid) {
    $level = $this->getNextLev($geoid);
    $sql = "SELECT * FROM Geography WHERE GeographyId LIKE '". $geoid .
           "%' AND GeographyLevel=" . $level . " ORDER BY GeographyName";
    $data = array();
    $res = $this->dreg->query($sql);
    foreach($res as $row)
      $data[$row['GeographyId']] = array($row['GeographyCode'], $row['GeographyName'], $row['GeographyActive']);
    return $data;
  }

  function getNextLev($geoid) {
    return (strlen($geoid) / 5);
  }
  
  /* fill struct looking by: 
	prefix: string with prefix used in VRegions
	level: integer of level, return only data of this level. -1 to all
	mapping: only levels with files assigned in database, shp - dbf..
  */
  function loadGeoLevels($prefix, $lev, $mapping) {
	if ($lev >= 0)
		$olev = "GeoLevelId = $lev ";
	else
		$olev = "1=1 ";
    $sqlev = "SELECT GeoLevelId, GeoLevelName, GeoLevelDesc FROM GeoLevel WHERE $olev ORDER BY GeoLevelId";
	if (!empty($prefix))
		$opre = "GeographyId = '$prefix' ";
	else
		$opre = "1=1 ";
	if ($mapping)
		$omap = "GeoLevelLayerFile != '' AND GeoLevelLayerCode != '' AND GeoLevelLayerName != '' ";
	else
		$omap = "1=1 ";
	$sqcar = "SELECT GeographyId, GeoLevelId, GeoLevelLayerFile, GeoLevelLayerCode, GeoLevelLayerName ".
				"FROM GeoCarto WHERE $olev AND $opre AND $omap ORDER BY GeoLevelId";
    $data = array();
	$rcar = $this->getassoc($sqcar);
    $rlev = $this->dreg->query($sqlev);
    foreach($rlev as $row) {
		$lay = array();
		foreach ($rcar as $car)
			if ($car['GeoLevelId'] == $row['GeoLevelId'])
				$lay[] = array($car['GeographyId'], $car['GeoLevelLayerFile'], $car['GeoLevelLayerCode'], $car['GeoLevelLayerName']);
		if (!empty($lay))
			$data[$row['GeoLevelId']] = array(str2js($row['GeoLevelName']), str2js($row['GeoLevelDesc']), $lay);
	}
    return $data;
  }
	/*
	function loadGeoCarto($geo, $lev) {
		$sql = "SELECT * FROM GeoCarto WHERE ";
		if (!empty($geo))
			$sql .= "GeographyId = '$geo'";
		else
			$sql .= "1=1";
		$sql .= " AND ";
		if ($lev >= 0)
			$sql .= "GeoLevelId = $lev";
		else
			$sql .= "1=1";
		//$res = $this->dreg->query($sql);
		return $this->getassoc($sql);
	}*/

  function getMaxGeoLev() {
    $sql = "SELECT MAX(GeoLevelId) AS max FROM GeoLevel";
    $res = $this->getresult($sql);
    if (isset($res['max']))
      return $res['max'];
    return -1;
  }

  function loadGeoLevById($geolevid) {
    if (!is_numeric($geolevid))
      return null;
    $sql = "SELECT * FROM GeoLevel WHERE GeoLevelId=". $geolevid;
    $data = array();
    $res = $this->dreg->query($sql);
    foreach($res as $row)
      $data = array(str2js($row['GeoLevelName']), str2js($row['GeoLevelDesc']));
    return $data;
  }

  function getEEFieldList($act) {
    $sql = "SELECT * FROM EEField";
    if ($act != "")
      $sql .= " WHERE EEFieldStatus=1";
    $data = array();
    $res = $this->dreg->query($sql);
    foreach($res as $row)
      $data[$row['EEFieldId']] = array($row['EEFieldLabel'], str2js($row['EEFieldDesc']), 
          $row['EEFieldType'], $row['EEFieldSize'], $row['EEFieldStatus']);
    return $data;
  }

  function getEEFieldSeries() {
    $sql = "SELECT COUNT(EEFieldId) as count FROM EEField";
    $res = $this->getresult($sql);
    if (isset($res['count']))
      return sprintf("%03d", $res['count']);
    return -1;
  }

	/* GET DISASTERS INFO: DATES, DATACARDS NUMBER, ETC */
	function getDBInfo() {
		$data = array();
		if ($this->dreg != null) {
			$sql = "SELECT InfoKey, LangIsoCode, InfoValue FROM Info";
			foreach($this->dreg->query($sql) as $row)
				$data[$row['InfoKey'] .'|'. $row['LangIsoCode']] = $row['InfoValue'];
		} //if
		return $data;
	}
	
	// Read an specific InfoKey value from the table
	function getDBInfoValue($prmInfoKey) {
		$sReturn = '';
		if ($this->dreg != null) {
			$sql = "SELECT * FROM Info WHERE InfoKey='" . $prmInfoKey . "'";
			if ($prmInfoKey != 'LangIsoCode') {
				$sql .= " AND (LangIsoCode='" . $this->getDBInfoValue('LangIsoCode') . "' OR LangIsoCode='')";
			}
			try {
				foreach($this->dreg->query($sql) as $row) {
					$sReturn = $row['InfoValue'];
				}
			} catch (Exception $e) {
				showErrorMsg("Error !: " . $e->getMessage());
			}
		} //if
		return $sReturn;
	}

	public function getDateRange() {
		$res = array();
		$datemin = $this->getDBInfoValue('PeriodBeginDate');
		$datemax = $this->getDBInfoValue('PeriodEndDate');
		if (($datemin == '') || ($datemax == '')) {
			$sql = "SELECT MIN(DisasterBeginTime) AS datemin, MAX(DisasterBeginTime) AS datemax FROM Disaster ".
			"WHERE RecordStatus='PUBLISHED'";
			$r2 = $this->getresult($sql);
			if ($datemin == '' ) { $datemin = $r2['datemin']; }
			if ($datemax == '' ) { $datemax = $r2['datemax']; }
		}
		$res[0] = substr($datemin, 0, 10);
		$res[1] = substr($datemax, 0, 10);
		return $res;		
	} //function
	
	/* This function returns an array with the database fields 
	   of Disaster */
	public function getDisasterFld() {
		$fld = array();
		$sql = "SELECT * FROM Disaster LIMIT 0,1";
		$res = $this->getassoc($sql);
		foreach ($res[0] as $key => $val) {
			$fld[] = $key;
		}
	// (jhcaiced) SyncRecord should not appear in data grid
		/*
		foreach (array('RecordSync','RecordUpdate') as $item) {
			if (array_key_exists($item, $fld)) { 
				unset $fld[$item]; 
			}
		}
		*/
		/*
		$pos = array_search('SyncRecord', $fld);
		if ($pos) {
			unset($fld[$pos]);
		}
		*/
		return $fld;
	}

	public function getDisasterIdFromSerial($serial) {
		$sql = "SELECT DisasterId FROM Disaster WHERE DisasterSerial = '". $serial."'";
		$res = $this->getresult($sql);
		return $res['DisasterId'];
	}
	
	public function getNextDisasterSerial($year) {
		$sql = "SELECT COUNT(DisasterId) AS num FROM Disaster WHERE DisasterBeginTime LIKE '". $year ."%'";
		$res = $this->getresult($sql);
		return sprintf("%05d", $res['num'] + 1);
	}

	public function getDisasterBySerial($diser) {
		$sql = "SELECT * FROM Disaster WHERE DisasterSerial='$diser'";
		$res = $this->dreg->query($sql);
		return $res;
	}

	public function getDisasterById($diid) {
		$sql = "SELECT * FROM Disaster WHERE DisasterId='$diid'";
		$res = $this->dreg->query($sql);
		return $res;
	}

	// Get number of datacards by status: PUBLISHED, DRAFT, ..
	public function getNumDisasterByStatus($status) {
		$sql = "SELECT COUNT(DisasterId) AS counter FROM Disaster WHERE RecordStatus='$status'";
		$dat = $this->getresult($sql);
		return $dat['counter'];
	}

	public function getLastUpdate() {
		$sql = "SELECT MAX(RecordUpdate) AS lastupdate FROM Disaster";
		$dat = $this->getresult($sql);
		return substr($dat['lastupdate'],0,10);
	}

	public function getFirstDisasterid() {
		$sql = "SELECT DisasterId as first FROM Disaster ORDER BY DisasterBeginTime, DisasterId LIMIT 1";
		$dat = $this->getresult($sql);
		return $dat['first'];
	}

	public function getPrevDisasterId($id) {
		$dcd = $this->getassoc("SELECT * FROM Disaster WHERE DisasterId='$id'");
		$sql = "SELECT DisasterId FROM Disaster WHERE DisasterId < '$id' AND DisasterBeginTime < '".
			$dcd[0]['DisasterBeginTime'] ."' ORDER BY DisasterBeginTime DESC, DisasterId DESC LIMIT 1";
		$dat = $this->getresult($sql);
		return $dat['DisasterId'];
	}

	public function getNextDisasterId($id) {
		$dcd = $this->getassoc("SELECT * FROM Disaster WHERE DisasterId='$id'");
		$sql = "SELECT DisasterId FROM Disaster WHERE DisasterId > '$id' AND DisasterBeginTime > '".
			$dcd[0]['DisasterBeginTime'] ."' ORDER BY DisasterBeginTime, DisasterId LIMIT 1";
		$dat = $this->getresult($sql);
		return $dat['DisasterId'];
	}

	public function getLastDisasterId() {
		$sql = "SELECT DisasterId as last FROM Disaster ORDER BY DisasterBeginTime DESC, DisasterId DESC LIMIT 1";
		$dat = $this->getresult($sql);
		return $dat['last'];
	}
  
	public function getRegLogList() {
		$data = array();
		if ($this->dreg != null) {
			$sql = "SELECT DBLogDate, DBLogType, DBLogNotes FROM DatabaseLog ORDER BY DBLogDate DESC";
			$data = array();
			$res = $this->dreg->query($sql);
			foreach($res as $row)
				$data[$row['DBLogDate']] = array($row['DBLogType'], str2js($row['DBLogNotes'])); 
		}
		return $data;
	}

	/* BASE.DB & CORE.DB -> COUNTRIES, REGIONS AND VIRTUAL REGIONS FUNCTIONS */
	function getCountryByCode($idcnt) {
		$sql = "SELECT CountryName FROM Country WHERE CountryIso = '$idcnt'";
		$res = $this->base->query($sql);
		$dat = $res->fetch(PDO::FETCH_ASSOC);
		return $dat['CountryName'];
	}

	function getCountryList() {
		$sql = "SELECT CountryIso, CountryName FROM Country ORDER BY CountryName";
		$data = array();
		$res = $this->base->query($sql);
		foreach ($res as $row)
		  $data[$row['CountryIso']] = $row['CountryName'];
		return $data;
	}

	function checkExistsRegion($rid) {
		$sql = "SELECT RegionId FROM Region WHERE RegionId = '$rid'";
		$res = $this->core->query($sql);
		$dat = $res->fetch(PDO::FETCH_ASSOC);
		if (empty($dat['RegionId']))
			return false;
		return true;
	}

	public function getRegionList($cnt, $status) {
		if (!empty($cnt))
			$opt = " CountryIso='$cnt'";
		else
			$opt = " 1=1";
		if ($status == "ACTIVE")
			$opt .= " AND RegionStatus >= 1";
		$sql = "SELECT RegionId, RegionLabel FROM Region WHERE IsCRegion=0 AND IsVRegion=0 AND $opt ORDER BY RegionLabel";
		$res = $this->core->query($sql);
		$data = array();
		foreach ($res as $row)
			$data[$row['RegionId']] = $row['RegionLabel'];
		return $data;
	}

	public function getRegionAdminList() {
		$sql = "SELECT R.RegionId AS RegionId, R.CountryIso AS CountryIso, R.RegionLabel AS RegionLabel, ".
				"R.LangIsoCode AS LangIsoCode, RA.UserId AS UserId, R.RegionStatus AS RegionStatus ".
				"FROM Region AS R, RegionAuth AS RA WHERE R.RegionId=RA.RegionId AND RA.AuthAuxValue='ADMINREGION' ".
				"ORDER BY R.CountryIso, R.RegionLabel";
		$data = array();
		foreach($this->core->query($sql) as $row) {
			$RegionActive = ($row['RegionStatus'] & 1) > 0;
			$RegionPublic = ($row['RegionStatus'] & 2) > 0;
			$data[$row['RegionId']] = array($row['CountryIso'], $row['RegionLabel'], $row['LangIsoCode'], 
											$row['UserId'], $RegionActive, $RegionPublic);
		}
		return $data;
	}

	public function getCVRegItems($cvregid) {
		$sql = "SELECT RegionItem FROM CVRegionItem WHERE RegionId='".
				$cvregid ."' ORDER BY RegionItem";
		$data = array();
		$res = $this->core->query($sql);
		foreach ($res as $row)
			$data[] = $row['RegionItem'];
		return $data;
	}

  /**************************
    General SQL test function
    *************************/
  public function chkSQLWhere($sql) {
    if (substr($sql, 0, 5) == "WHERE")
      return true;
    else
      return false;
  }

  public function chkSQL($sql) {
    if (substr($sql, 0, 6) == "SELECT")
      return true;
    else
      return false;
  }
  
  /*****************************
   Generate SQL from Associative array from Desconsultar Form
   ******************************/
  public function genSQLWhereDesconsultar($dat) {
    $sql 	= "WHERE ";
    $e		= array();
    $e['Eff'] = "";
    $e['Item'] = "";
    $serial = "";
	$cusqry = "";
    //$datedb = $this->getDateRange();
	// Add Custom Query..
	if (isset($dat['__CusQry']) && !empty($dat['__CusQry']))
		$cusqry = "AND (". $dat['__CusQry'] .") ";
    foreach ($dat as $k=>$v) {
      // replace D_ by D.
      if (substr($k, 1, 1) == "_")
        $k = substr_replace($k, ".", 1, 1);
      if (!empty($v)) {
        if (is_int($v) || is_float($v))
          $e['Item'] .= "$k = $v AND ";
        else if ($k == "D.RecordStatus") {
          if (is_array($v)) {
            $e['Item'] .= "(";
            foreach($v as $i)
              $e['Item'] .= "$k = '$i' OR ";
            $e['Item'] .= "1!=1) AND ";
          }
          else
            $e['Item'] .= "$k = '$v' AND ";
        }
        else if (is_array($v)) {
          if ($k == "D.DisasterBeginTime") {
            $aa = !empty($v[0])? $v[0] : "0000"; //substr($datedb[0], 0, 4);
            $mm = !empty($v[1])? $v[1] : "00";
            $dd = !empty($v[2])? $v[2] : "00";
            $begt = sprintf("%04d-%02d-%02d", $aa, $mm, $dd);
          }
          elseif ($k == "D.DisasterEndTime") {
            $aa = !empty($v[0])? $v[0] : "9999"; //substr($datedb[1], 0, 4);
            $mm = !empty($v[1])? $v[1] : "12";
            $dd = !empty($v[2])? $v[2] : "31";
            $endt = sprintf("%04d-%02d-%02d", $aa, $mm, $dd);
          }
          elseif ($k == "D.EventId" || $k == "D.CauseId") {
            $e[$k] = "(";
            foreach ($v as $i) {
              $e[$k] .= "$k = '$i' OR ";
            }
            $e[$k] .= "1!=1)";
          }
          elseif ($k == "D.GeographyId") {
            $e[$k] = "(";
            foreach ($v as $i) {
              // Restrict to childs elements only
              $chl = false;
              foreach ($v as $j)
                if ($i != $j && ($i == substr($j, 0, 5) || $i == substr($j, 0, 10)))
                  $chl = true;
              if (!$chl)
                $e[$k] .= "$k LIKE '$i%' OR ";
            }
            $e[$k] .= "1!=1)";
          }
          // Process effects and sectors..
          elseif ((substr($k, 2, 6) == "Effect" || substr($k, 2, 6) == "Sector") && isset($v[0])) {
            if (isset($v[3]))
              $op = $v[3];
            else
              $op = "AND";
            if ($v[0] == ">=" || $v[0] == "<=" || $v[0] == "=")
              $e['Eff'] .= "$k ". $v[0] . $v[1] ." $op ";
            else if ($v[0] == "-1")
              $e['Eff'] .= "($k =". $v[0] ." OR $k>0) $op ";
            else if ($v[0] == "0" || $v[0] == "-2")
              $e['Eff'] .= "$k =". $v[0] ." $op ";
            else if ($v[0] == "-3")
              $e['Eff'] .= "($k BETWEEN ". $v[1] ." AND ". $v[2] .") $op ";
          }
          // Process text fields with separator AND, OR..
          elseif (substr($k, -5) == "Notes" || $k == "D.DisasterSource") {
            $e['Item'] .= "(";
            foreach (explode(" ", $v[1]) as $i)
              $e['Item'] .= "$k LIKE '%$i%' ". $v[0] ." "; 
            if ($v[0] == "AND")
              $e['Item'] .= "1=1) AND ";
            else
              $e['Item'] .= "1!=1) AND ";
          }
          // Process serials..
          elseif ($k == "D.DisasterSerial") {
            if (strlen($v[1]) > 0) {
              $serial = "AND ". $v[0] ." (";
              foreach (explode(" ", $v[1]) as $i)
                $serial .= "$k='$i' OR ";
              $serial .= "1!=1) ";
            }
          }
        }
        // all minus DC hidden fields _MyField
        elseif (substr($k, 0, 1) != "_")  {
          $e['Item'] .= "$k like '%$v%' AND ";
        }
      }
    } //foreach
    if (isset($begt) || isset($endt)) {
      if (!isset($begt))
        $begt = "0000-00-00"; // $datedb[0];
      if (!isset($endt))
        $endt = "9999-12-31"; //$datedb[1];
      $e['DisasterTime'] = "(D.DisasterBeginTime BETWEEN '$begt' AND '$endt')";
    }
    if (isset($op) && $op == "OR")
      $e['Eff'] = "(". $e['Eff'] ." 1!=1)";
    else
      $e['Eff'] = "(". $e['Eff'] ." 1=1)";
    $lan = "spa"; // select from local languages of database..
    $e['Item'] .= "D.EventId=V.EventId AND D.CauseId=C.CauseId AND D.GeographyId=G.GeographyId ".
                  "AND V.LangIsoCode='$lan' AND C.LangIsoCode='$lan' AND G.LangIsoCode='$lan'";
    foreach ($e as $i)
      $sql .= "$i AND ";
    $sql .= "D.DisasterId = E.DisasterId $serial $cusqry";
	//echo $sql;
    return ($sql);
  }

  /* Counter results */
  public function genSQLSelectCount($whr) {
    $sql = "SELECT COUNT(D.DisasterId) as counter FROM Disaster AS D, EEData AS E, Event AS V, Cause AS C, Geography AS G ";
    if ($this->chkSQLWhere($whr))
      return ($sql . $whr);
    return false;
  }

  /* Generate SQL to data lists */
  public function genSQLSelectData ($whr, $fld, $order) {
    $fld = str_ireplace("D.EventId", "V.EventName", $fld); //Join with Event table
    $fld = str_ireplace("D.EventName", "V.EventName", $fld); //Join with Event table
    $fld = str_ireplace("D.CauseId", "C.CauseName", $fld); //Join with Cause table
    $fld = str_ireplace("D.CauseName", "C.CauseName", $fld); //Join with Cause table
    $fld = str_ireplace("D.GeographyCode", "G.GeographyCode", $fld);
    $fld = str_ireplace("D.GeographyFQName", "G.GeographyFQName", $fld);
    // Process fields to show
    $sql = "SELECT ". $fld ." FROM Disaster AS D, EEData AS E, Event AS V, Cause AS C, Geography AS G ";
    if ($this->chkSQLWhere($whr)) {
      $sql .= $whr;
      if (!empty($order))
        $sql .= "ORDER BY $order";
      return ($sql);
    }
    else
      return false;
  }

	/* Generate Special SQL with grouped fields */
	public function genSQLProcess ($dat, $opc) {
		$sql = '';
		
		$sel = array();
		$whr = array();
		$grp = array();
		
		// Vars to be agrupated (BeginTime, Geography, Event, Cause)
		$j = 1;
		// Group has this struct: FUNC|VAR
		foreach ($opc['Group'] as $item) {
			$gp = explode("|", $item);
			switch ($gp[1]) {
				case "D.DisasterBeginTime":
					// Check if exist Operator(s): Year, month, week, day
					if (!empty($gp[0])) {
						// Error with strftime when date contain '00' like '1997-06-00'
						switch ($gp[0]) {
							case "YEAR":		$func = "SUBSTR(". $gp[1] .", 1, 4) ";		break; // %Y
							case "YMONTH":	$func = "SUBSTR(". $gp[1] .", 1, 7) ";		break; //%Y-%m
							case "MONTH":		$func = "SUBSTR(". $gp[1] .", 6, 2) ";		break; //%m
							case "YWEEK":		$func = "STRFTIME('%Y-%W', ". $gp[1] .") "; break; //%Y-%W
							case "WEEK":		$func = "STRFTIME('%W', ". $gp[1] .") ";	break; //%W
							case "YDAY": 		$func = "SUBSTR(". $gp[1] .", 1, 10) ";		break; //%Y-%m-%d
							case "DAY": 		$func = "STRFTIME('%j', ". $gp[1] .") ";	break; //%j
						} //switch
						$sel[$j] = $func ."AS ". substr($gp[1],2) ."_". $gp[0] ; // delete last ,'_',
						$grp[$j] = $func;
					} else {
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
					if (!empty($gp[1])) {
						$sel[$j] = $gp[1];
						$grp[$j] = $gp[1];
					}
				break;
			} //switch
			$j++;
		} //foreach
		
		// Process Field in select and group
		if (!is_array($opc['Field']))
			$f[0] = $opc['Field'];
		else
			$f = $opc['Field'];

		foreach ($f as $field) {
			// Field(s) to show
			$fl = explode("|", $field);
			// Code to totalization: SUM, COUNT
			if ($fl[1] == ">") {
				$sel[$j] = "SUM(". $fl[0] .") AS ". substr($fl[0],2);
				$whr[$j] = "OR ". $fl[0] . $fl[1] . $fl[2];
			} else {
				// Count Reports
				$sel[$j] = "COUNT(". $fl[0] .") AS ". substr($fl[0],2) ."_";
				// Counts Reports with "Hay"
				if ($fl[1] == "=") {
					$whr[$j] = "OR (". $fl[0] . $fl[1] . $fl[2] .
						" OR ". $fl[0] .">0)";
				}
			} //if
			$j++;
		} //foreach
		
		// Code Select
		if ($this->chkSQLWhere($dat)) {
			$selec = implode(", ", $sel);
			$where = implode(" ", $whr);
			if (!empty($whr)) 
				$where = "AND (1!=1 $where)";
			$group = implode(", ", $grp);
			$sql = $this->genSQLSelectData ($dat, $selec, "");
			$sql .= "$where GROUP BY $group";
		}
		return $sql;
	} //function

	/* Reformat array setting to arr[X1] = array {a, b, c, d..} */
	function prepareList ($dl, $mode) {
		$res = array();
		$j = 0;
		$creg = $this->getDBInfoValue('IsCRegion');
		foreach ($dl as $it) {	
			foreach ($it as $k=>$i) {
				$val = $i;
				if (substr($k,0,11) == "GeographyId") {
				  if ($mode == "GRAPH")
					$val = $this->getGeoNameById($i);
				  elseif ($mode == "MAPS") {
					$val = $this->getObjectNameById($i, DI_GEOGRAPHY);
					// in VirtualRegion set base prefix - 
					if ($creg) {
						if ($j == 0)
							$res['CVReg'] = array();
						array_push($res['CVReg'], substr($i, 0, 5));
					}
					if ($j == 0)
						$glv = $k; // save key of GeographyId
				  }
				}
				elseif ($j == 0)
					$eff = $k; // save key of Effectvar
				if ($j == 0)
					$res[$k] = array();
				array_push($res[$k], $val);
			}
			$j++;
		}
		// Sorting list in maps to order legend - ORDER BY not found with GROUP BY in sqlite3 ??
		if ($mode == "MAPS") {
			if ($creg)
				array_multisort($res[$eff], $res[$glv], $res['CVReg']);
			else
				array_multisort($res[$eff], $res[$glv]);	
		}
		return $res;
	}
	
	/* Print results like associative array or fields separate by Tabs */
	function printResults ($dl, $exp, $mode) {
		$txt = "";
		// Get results
		if (!empty($dl)) {
			$j = 0;
			foreach ($dl as $k=>$i) {
				foreach (array_keys($i) as $idx) {
					if (substr($idx,0,11) == "GeographyId") {
			            switch ($mode) {
			              case "CODE": 
			                $dl[$j][$idx] = $this->getObjectNameById($i[$idx], DI_GEOGRAPHY); break;
			              case "NAME": 
			                $dl[$j][$idx] = $this->getGeoNameById($i[$idx]); break;
			              case "CODENAME": 
			                $dl[$j][$idx] = $this->getObjectNameById($i[$idx], DI_GEOGRAPHY) ." | ". $this->getGeoNameById($i[$idx]); break;
			              default: $dl[$j][$idx] = ""; break;
			            }
					}
					elseif (is_numeric($dl[$j][$idx])) {
						$dl[$j][$idx] = number_format($dl[$j][$idx], 0, ',', ' ');
					}
				}
				if (!empty($exp)) {
					foreach (array_values($dl[$j]) as $vals) {
						if ($vals == -1)
							$myv = "YES";
						else
							$myv = $vals;
						if ($exp == 'csv')
							$sep = ", ";	// use comma separator to CSV
						else
							$sep = "\t";	// use tab separator to XLS (default option)
						$txt .= '"'. $myv .'"'. $sep;
					}
					$txt .= "\n";
				} //if exp
				$j++;
			} //foreach
		} //if !empty
		if (!empty($exp))
			return $txt;
		else
			return $dl;
	}
  
	// Print results like json array to Javascript
	function hash2json($dlist) {
		$js = array();
		foreach ($dlist as $ky=>$vl) {
			$js[$ky] = "{";
			foreach ($vl as $k=>$v) {
				if ($k == "DisasterBeginTime") {
					$dt = explode("-", $v);
					if (!isset($dt[0])) 
						$dt[0] = 0; 
					if (!isset($dt[1])) 
						$dt[1] = 0; 
					if (!isset($dt[2])) 
						$dt[2] = 0; 
					$js[$ky] .= "'". $k ."[0]':'". $dt[0] ."', '". $k ."[1]':'". $dt[1] ."', '". $k ."[2]':'". $dt[2] ."', ";
				} else {
					$js[$ky] .= "'$k': '$v', ";
				}
			} //foreach
			$js[$ky] .= "'_REG': '". $this->sRegionId ."'}";
		}
		return $js;
	} //function
  
  /*** SET SQL TO TOTALIZATION RESULTS ***/
  function totalize($sql) {
    $sq = explode("GROUP", $sql);
    return $sq[0] . " GROUP BY null";
  }

  function getQueryDetails($dic, $post) {
	$info = $lsf = array();
	$dinf = $this->getDBInfo();
	$info['TITLE'] = "";
	if (isset($post['_M+Field'])) {
		$fld = explode("|", $post['_M+Field']);
		$fd0 = substr($fld[0],2);
		$fd1 = substr($fd0, 0, -1);
		if (isset($dic["MapOpt". $fd0 ."_"][0]))
			$info['TITLE'] = $dic["MapOpt". $fd0 ."_"][0];
		elseif (isset($dic[$fd0][0]))
			$info['TITLE'] = $dic[$fd0][0];
		elseif (isset($dic[$fd1][0]))
			$info['TITLE'] = $dic[$fd1][0];
    }
	$info['LEVEL'] = "";
	if (isset($post['_M+Type']) && !(isset($post['_VREG']) && $post['_VREG'] == "true")) {
		$fld = explode("|", $post['_M+Type']);
		$val = $this->loadGeoLevById($fld[0]);
		$info['LEVEL'] = $val[0];
	}
	$info['EXTENT'] = $dinf['GeoLimitMinX'] ." ". $dinf['GeoLimitMaxX'] ." ". $dinf['GeoLimitMinY'] ." ". $dinf['GeoLimitMaxY'];
	$info['KEYWORDS'] = $dinf['RegionLabel'];
	//Process post
	foreach ($post as $k=>$v) {
	  $k = substr($k,2);
	  if ($k == "GeographyId") {
		foreach($v as $itm)
		  $lsg[] = $this->getGeoNameById($itm);
		$info['GEO'] = implode(", ", $lsg);
	  }
	  elseif ($k == "EventId") {
		foreach($v as $itm)
		  $lse[] = $this->getObjectNameById($itm, DI_EVENT);
		$info['EVE'] = implode(", ", $lse);
	  }
	  elseif ($k == "CauseId") {
		foreach($v as $itm)
		  $lsc[] = $this->getObjectNameById($itm, DI_CAUSE);
		$info['CAU'] = implode(", ", $lsc);
	  }
	  elseif ($k == "DisasterBeginTime")
		$info['BEG'] = $v[0];
	  elseif ($k == "DisasterEndTime")
		$info['END'] = $v[0];
	  elseif ($k == "DisasterSource" && !empty($v[1]))
		$info['SOU'] = $v[1];
	  elseif ($k == "DisasterSerial" && !empty($v[1]))
		$info['SER'] = $v[1];
	  elseif (substr($k, 0, 6) == "Effect" && isset($v[0]) && isset($dic[$k][0])) {
		$opt = "";
		if ($v[0] == "=" || $v[0] == ">=" || $v[0] == "<=")
		  $opt = "(". $v[0] . $v[1] .")";
		elseif ($v[0] == "-3")
		  $opt = "(". $v[1] ."-". $v[2] .")";
		$lsf[] = $dic[$k][0] . $opt;
	  }
	}
	if (!empty($lsf))
		$info['EFF'] = implode(", ", $lsf);
	return $info;
  }
  
  // DICTIONARY FUNCTIONS
  function existLang($langID) {
    if ($langID == "")
      return false;
    $sql = "select LangIsoCode from Language where LangIsoCode='". $langID ."'";
    foreach ($this->base->query($sql) as $row) {
      if (count($row) > 0) {
        return true;
      }
    }
    return false;
  }
  
  function queryLabel($labgrp, $labname, $langID) {
  	$data = '';
    $sql = "select d.DictTranslation as DTr, d.DictTechHelp as DTe, ".
            "d.DictBasDesc as DBa, d.DictFullDesc as DFu from Dictionary d,".
            " LabelGroup g where (g.LGName like '" . $labgrp . "%') ".
            "and (d.LangIsoCode='" . $langID . "') and (g.LabelName= '".
            $labname ."') and (d.DictLabelID = g.DictLabelID) ".
            "order by g.LGorder";
    foreach ($this->base->query($sql) as $row) {
      $data = array('DictTranslation'=>$row['DTr'],//utf8_encode($row['DTr']),
                    'DictTechHelp'=>$row['DTe'],//utf8_encode($row['DTe']),
                    'DictBasDesc'=>$row['DBa'],//utf8_encode($row['DBa']),
                    'DictFullDesc'=>$row['DFu']);//utf8_encode($row['DFu']));
    }
    return $data;
  }
  function queryLabelsFromGroup($labgrp, $langID, $withLabelGroupPrefix=true) {
  	$dictio = '';
    $sql = "SELECT g.LGName as lgn, g.LabelName as lbn, DictTranslation, ".
            "DictTechHelp, DictBasDesc, DictFullDesc from Dictionary d,".
            " LabelGroup g where (g.LGName like '". $labgrp ."%') and ".
            "(d.LangIsoCode='". $langID ."') and (d.DictLabelID = g.DictLabelID) ".
            "order by g.LGorder";
	try {
		foreach ($this->base->query($sql) as $row) {
			$grp = explode("|", $row['lgn']);
			if ($withLabelGroupPrefix) {
				$dictlabel = $grp[0];
			} else {
				$dictlabel = '';
			}
			$dictlabel .= $row['lbn'];
			$dictio[$dictlabel] = array(
			  $row['DictTranslation'],//utf8_encode($row['DicTranslation']), 
			  $row['DictTechHelp'],//utf8_encode($row['DicTechHelp']),
			  $row['DictBasDesc'], $row['DictFullDesc']);
		} // foreach
	} catch (Exception $e) {
		showErrorMsg($e->getMessage());
	}
    return $dictio;
  }

  function querySecLabelFromGroup($labgrp, $langID) {
    $sql = "select g.LGName as lgn, g.LabelName as lbn, DictTranslation, ".
            "DictTechHelp, DictBasDesc, DictFullDesc from Dictionary d,".
            " LabelGroup g where (g.LGName like '". $labgrp ."%') and ".
            "(d.LangIsoCode='". $langID ."') and (d.DictLabelID = g.DictLabelID) ".
            "order by g.LGorder";
    foreach ($this->base->query($sql) as $row) {
      $grp = explode("|", $row['lgn']);
      $dictio[$grp[0].$row['lbn']] = $grp[2];
    }
    return $dictio;
  }

  function loadAllGroups($langID) {
    $sql = "select g.LGName as lgn, g.LabelName as lbn, DictTranslation, ".
            "DictTechHelp, DictBasDesc, DictFullDesc from Dictionary d,".
            " LabelGroup g where (d.LangIsoCode='" . $langID . "') and ".
            "(d.DictLabelID = g.DictLabelID) order by g.LGorder";
    foreach ($this->base->query($sql) as $row) {
      $grp = explode("|", $row['lgn']);
      $dictio[$grp[0].$row['lbn']] = array($row['DictTranslation'], 
          $row['DictTechHelp'], $row['DictBasDesc'], $row['DictFullDesc']);
    }
    return $dictio;
  }

  function loadAllLabels() {
    $sql = "select DictLabelID, LGName, LabelName from LabelGroup order by LGName";
    $diction = array();
    foreach ($this->base->query($sql) as $row) {
      $dictio[$row['DictLabelID']] = $row['LGName'] .'|'. $row['LabelName'];
    }
    return $dictio;
  }

  function loadLanguages($status) {
    $sql = "select LangIsoCode, LangIsoName, LangLocalName from Language";
	if ($status != null)
		$sql .= " where LangStatus=". $status;
    foreach ($this->base->query($sql) as $row) {
      $lang[$row['LangIsoCode']] = array($row['LangLocalName'], $row['LangIsoName']);
    }
    return $lang;
  }
  
  function findDicLabelID($labgrp, $labname) {
    $sql = "select DictLabelID from LabelGroup where LGName like '". $labgrp .
            "%' and LabelName='". $labname ."';";
    foreach ($this->base->query($sql) as $row) {
      $diclabelID = $row['DictLabelID'];
    }
    return $diclabelID;
  }
  
  function existLabel($diclabelID) {
    $sql = "select DictLabelID from Dictionary where DictLabelID='". 
            $diclabelID . "';";
    foreach ($this->base->query($sql) as $row) {
      $diclabelID = $row['DictLabelID'];
      if ($diclabelID != null)
        return true;
    }
    return false;
  }
  // Check!!
  function updateDicLabel($labgrp, $labname, $translation, $techhelp, $basdesc, $fulldesc, $langID) {
    if (!$this->existLang($langID))
      return false;
    else {
      $diclabID = $this->findDicLabelID($labgrp, $labname);
      if (!$this->existLabel($diclabID))
        $sql = "insert into Dictionary values ('". $diclabID ."','". $langID .
              "','". $translation ."','". $techhelp ."','". $basdesc ."','".
              $fulldesc ."');";
      else
        $sql = "update Dictionary set LangID='". $langID ."', DicTranslation='".
              $translation ."', DicTechHelp='". $techhelp ."', DicBasDesc='".
              $basdesc ."', DicFullDesc='".$fulldesc .
              "' where DicLabelID='". $diclabID ."';";
      $this->base->exec($sql);
    }
    return true;
  }
  
	public function searchDB($prmQuery, $searchByCountry) {
		$RegionList = array();
		$query = "SELECT RegionId, RegionLabel FROM Region WHERE RegionStatus=3 AND "; 
		if ($searchByCountry) {
			$query .= "(CountryIso = '" . $prmQuery . "')";
		} else {
			$query .= "(RegionId LIKE '%" . $prmQuery . "%' OR RegionLabel LIKE '%" . $prmQuery . "%')";
		}
		$query .= " ORDER BY RegionLabel, RegionOrder";
		$result = $this->core->query($query);
		while ($row = $result->fetch(PDO::FETCH_OBJ)) {
			$RegionList[$row->RegionId] = $row->RegionLabel;
		}
		return $RegionList;
	}
	
	public function listDB($searchByCountry, $UserId) {
		$RegionList = array();
		$query = "SELECT R.RegionId AS RegionId, R.RegionLabel AS RegionLabel, R.CountryIso AS CountryIso, R.RegionStatus AS RegionStatus, ".
			"RA.AuthAuxValue AS Role FROM Region AS R, RegionAuth AS RA WHERE R.RegionId = RA.RegionId ";
		if ($searchByCountry)
			$query .= " AND R.CountryIso = '" . $prmQuery . "'";
		if ($UserId)
			$query .= " AND RA.AuthKey = 'ROLE' AND RA.UserId = '". $UserId ."'";
		else
			$query .= " AND R.RegionStatus = 3 GROUP BY R.RegionId";
		$query .= " ORDER BY R.CountryIso, R.RegionLabel, R.RegionOrder";
		$result = $this->core->query($query);
		while ($row = $result->fetch(PDO::FETCH_OBJ)) {
			$RegionList[$row->RegionId] = array($row->RegionLabel, $row->CountryIso, $row->RegionStatus, $row->Role);
		}
		return $RegionList;
	}

	public function getCountryName($prmCountryIso) {
		$query = "SELECT * FROM Country WHERE CountryIso='" . $prmCountryIso . "'";
		foreach($this->base->query($query) as $row) {
			$CountryName = $row['CountryName'];
		}
		return $CountryName;
	}
  
	// Check
	function rebuildCore($fcore) {
		return true;
	}
  
} // end class

</script>
