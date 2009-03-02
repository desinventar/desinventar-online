<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class Query extends PDO
{
	public $regid = "";

	public function __construct() {
		if (!extension_loaded('pdo')) {
		  dl( "pdo.so" );
		  dl( "pdo_sqlite.so" );
		}
		try {
			$num_args = func_num_args();
			// Load base.db - DI's Basic database
			$dbb = VAR_DIR ."/base.db";
			if (file_exists($dbb))
				$this->base = new PDO("sqlite:" . $dbb);
			// Load core.db - Users, Regions, Auths.. 
			$dbc = VAR_DIR ."/core.db";
			if (file_exists($dbc))
				$this->core = new PDO("sqlite:" . $dbc);
			else
				$this->rebuildCore($dbc); // Rebuild data from directory..
			switch($num_args) {
			case 0:
				$this->sSessionId = uuid();
				break;
			case 1:
				$this->regid = func_get_arg(0);
				$dbr = VAR_DIR ."/". $this->regid ."/desinventar.db";
				$this->dreg = null;
				try {
					if (file_exists($dbr)) {
						$this->dreg = new PDO("sqlite:" . $dbr);
					}
				} catch (PDOException $e) {
					print $e->getMessage();
				}
				//          else
				//            exit();
				break;
			} //switch
		} catch (PDOException $e) {
			print "Error !: " . $e->getMessage() . "<br/>\n";
			die();
		}
	}
  
  public function getassoc($qry) {
    if (!empty($qry)) {
      $rst = $this->dreg->query($qry);
      $res = $rst->fetchAll(PDO::FETCH_NAMED);
      $data = array();
      foreach($res as $row)
        $data[] = $row;
      return $data;
    }
    echo "Query Empty";
    return false;
  }

	public function getresult($qry) {
		$rst = null;
		$row = null;
		try {
			if ($this->dreg != null) {
				$rst = $this->dreg->query($qry);
				$row = $rst->fetch(PDO::FETCH_ASSOC);
			}
		} catch (PDOException $e) {
			print $e->getMessage() . "<br>";
		}
		return $row;
	}

  public function getnumrows($qry) {
    $rst = $this->dreg->query($qry);
    return $rst->rowCount();
  }

  // STANDARDS FUNCTION TO GET GENERAL EVENTS, CAUSES LISTS
  function loadEvents($type, $status, $lang) {
    $data = array();
    if ($type == "BASE") {
      $data = $this->getBasicEventList($lang);
    }
    else {
      $data = $this->getRegionEventList($type, $status, $lang);
      // Set complete event list with DI82 struct, according with language, etc..
    }
    return $data;
  }

  // active : active, inactive  | types : predef, user | empty == all
  function loadCauses($type, $status, $lang) {
    $data = array();
    if ($type == "BASE") {
      $data = $this->getBasicCauseList($lang);
    }
    else {
      $data = $this->getRegionCauseList($type, $status, $lang);
      // Set complete cause list with DI82 struct, according with language, etc..
    }
    return $data;
  }

  public function getBasicEventList($lg) {
    $sql = "SELECT EventId, EventName, EventDesc FROM DI_Event ".
            "WHERE LangIsoCode='$lg' ORDER BY EventName";
    $data = array();
    $res = $this->base->query($sql);
    foreach($res as $row)
      $data[$row['EventId']] = array($row['EventName'], $row['EventDesc']);
    return $data;
  }
  
  public function getBasicCauseList($lg) {
    $sql = "SELECT CauseId, CauseName, CauseDesc FROM DI_Cause ".
            "WHERE LangIsoCode='$lg' ORDER BY CauseName";
    $data = array();
    $res = $this->base->query($sql);
    foreach($res as $row)
      $data[$row['CauseId']] = array($row['CauseName'], $row['CauseDesc']);
    return $data;
  }
  // DI82
  public function getRegionEventList($type, $status, $lang) {
    if ($type == "PREDEF")
      $sqlt = "EventPreDefined='True'";
    else if ($type == "USER")
      $sqlt = "EventPreDefined='False'";
    else
      $sqlt = "'1=1'";	// all
    if ($status == "active")
      $sqls = "EventActive='True'";
    else
      $sqls = "'1=1'"; // all
    $sql = "SELECT * FROM Event WHERE ". $sqls ." AND ". 
        $sqlt ." ORDER BY EventName";
    $data = array();
    $res = $this->dreg->query($sql);
    foreach($res as $row)
      $data[$row['EventId']] = array($row['EventName'], str2js($row['EventDesc']), $row['EventActive']);
    return $data;
  }
  // DI82
  public function getRegionCauseList($type, $status, $lang) {
    if ($type == "PREDEF")
      $sqlt = "CausePreDefined='True'";
    else if ($type == "USER")
      $sqlt = "CausePreDefined='False'";
    else
      $sqlt = "'1=1'";	// all
    if ($status == "active")
      $sqls = "CauseActive='True'";
    else
      $sqls = "'1=1'"; // all
    $sql = "SELECT * FROM Cause WHERE ". $sqls ." AND ". 
        $sqlt ." ORDER BY CauseName";
    $data = array();
    $res = $this->dreg->query($sql);
    foreach($res as $row)
      $data[$row['CauseId']] = array($row['CauseName'], str2js($row['CauseDesc']), $row['CauseActive']);
    return $data;
  }
  // DI82
  /***** READ OBJECTS :: EVENT, CAUSE, GEOGRAPHY, GEOLEVEL READ *****/
  public function isvalidObjectToInactivate($id, $obj) {
    switch ($obj) {
      case DI_EVENT:			$whr = "EventId='$id'";		break;
      case DI_CAUSE:			$whr = "CauseId='$id'";		break;
      case DI_GEOGRAPHY:	$whr = "DisasterGeographyId like '$id%'";		break;
    }
    $sql = "SELECT COUNT(DisasterId) AS counter FROM Disaster WHERE $whr ";
    $res = $this->getresult($sql);
    if ($res['counter'] > 0)
      return false;
    else
      return true;
  }
  // DI82
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
      case DI_EVENT:			$name = "EventName";			$table = "Event";			$fld = "EventId";		break;
      case DI_CAUSE:			$name = "CauseName";			$table = "Cause";			$fld = "CauseId";		break;
      case DI_GEOGRAPHY:	$name = "GeographyCode";	$table = "Geography"; $fld = "GeographyId";	break;
      case DI_GEOLEVEL:		$name = "GeoLevelName";		$table = "GeoLevel"; 	$fld = "GeoLevelId";	break;
      default:						return null; 		break;
    }
    $sql = "SELECT $name FROM $table WHERE $fld = '$id'";
    $res = $this->getresult($sql);
    if (isset($res[$name]))
      return $res[$name];
    else
      return null;
  }
  //DI82
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
  //DI82
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
    $data = "";
    $res = $this->dreg->query($sql);
    foreach($res as $row)
      $data .= $row['GeographyName'] . "/";
    return $data;
  }
  //DI82
  /*** GEOGRAPHY ***/
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
  //DI82
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
  // DI82
  function loadGeoLevels($mapping) {
    $opc = "";
    if ($mapping == "map")
      $opc = "WHERE GeoLevelLayerFile != '' AND GeoLevelLayerCode != '' AND GeoLevelLayerName != ''";
    $sql = "SELECT * FROM GeoLevel $opc ORDER BY GeoLevelId";
    $data = array();
    $res = $this->dreg->query($sql);
    foreach($res as $row)
      $data[$row['GeoLevelId']] = array(str2js($row['GeoLevelName']), str2js($row['GeoLevelDesc']), 
            $row['GeoLevelLayerFile'], $row['GeoLevelLayerCode'], $row['GeoLevelLayerName']);
    return $data;
  }
  // DI82
  function getMaxGeoLev() {
    $sql = "SELECT MAX(GeoLevelId) AS max FROM GeoLevel";
    $res = $this->getresult($sql);
    if (isset($res['max']))
      return $res['max'];
    return -1;
  }
  // DI82
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
  // DI82
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
  // DI82
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
			$sql = "SELECT InfoKey, InfoValue, InfoAuxValue FROM Info";
			//$res = $this->dreg->exec($sql);
			$res = $this->dreg->query($sql);
			foreach($res as $row)
				$data[$row['InfoKey']] = array($row['InfoValue'], $row['InfoAuxValue']);
		} //if
		return $data;
	}
	
  // DI82
  public function getDateRange() {
    $sql = "SELECT MIN(DisasterBeginTime) AS datemin, MAX(DisasterBeginTime)".
          " AS datemax FROM Disaster WHERE RecordStatus='PUBLISHED'";
    $res = $this->getresult($sql);
    return array($res['datemin'], $res['datemax']);
  }
  // DI82
  public function getDisasterFld() {
    $sql = "DESCRIBE ". $this->regid ."_Disaster";
    $res = $this->getassoc($sql);
    foreach ($res as $it)
      $fld[] = $it['Field'];
    return $fld;
  }
  // DI82
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
    $sql = "SELECT MAX(RecordLastUpdate) AS lastupdate FROM Disaster";
    $dat = $this->getresult($sql);
    return substr($dat['lastupdate'],0,10);
  }

  public function getFirstDisasterid() {
    $sql = "SELECT MIN(DisasterId) AS first FROM Disaster";
    $dat = $this->getresult($sql);
    return $dat['first'];
  }
  
  public function getPrevDisasterId($id) {
    $sql = "SELECT DisasterId AS prev FROM Disaster WHERE ".
      "DisasterId < '$id' ORDER BY RecordLastUpdate,DisasterId DESC LIMIT 1";
    $dat = $this->getresult($sql);
    return $dat['prev'];
  }
  
  public function getNextDisasterId($id) {
    $sql = "SELECT DisasterId AS next FROM Disaster WHERE ".
      "DisasterId > '$id' ORDER BY RecordLastUpdate,DisasterId ASC LIMIT 1";
    $dat = $this->getresult($sql);
    return $dat['next'];
  }

  public function getLastDisasterId() {
    $sql = "SELECT MAX(DisasterId) AS last FROM Disaster";
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

  function getRegionFieldByID($ruuid, $field) {
    $sql = "SELECT RegionId, $field FROM Region WHERE RegionId = '$ruuid'";
    $res = $this->core->query($sql);
    $dat = $res->fetch(PDO::FETCH_ASSOC);
    $data[$dat['RegionId']] = $dat[$field];
    return $data;
  }

  public function getRegionList($cnt, $status) {
    if (!empty($cnt))
      $opt = " CountryIso='$cnt'";
    else
      $opt = " 1=1";
    if ($status == "ACTIVE")
      $opt .= " AND RegionStatus >= 1";
    $sql = "SELECT RegionId, RegionLabel FROM Region WHERE $opt ORDER BY RegionLabel";
    $res = $this->core->query($sql);
    $data = array();
    foreach ($res as $row)
      $data[$row['RegionId']] = $row['RegionLabel'];
    return $data;
  }

  public function getRegionAdminList() {
    $sql = "SELECT R.RegionId AS RegionId, R.CountryIso AS CountryIso, R.RegionLabel AS RegionLabel, ".
        "RA.UserName AS UserName, R.RegionActive AS RegionActive, R.RegionPublic AS RegionPublic ".
        "FROM Region AS R, RegionAuth AS RA WHERE R.RegionUUID=RA.RegionUUID AND RA.AuthAuxValue='ADMINREGION' ".
        "ORDER BY RegionLabel";
    $data = array();
    $res = $this->core->query($sql);
    foreach ($res as $row)
      $data[$row['RegionId']] = array($row['CountryIso'], $row['RegionLabel'], 
            $row['UserName'], $row['RegionActive'], $row['RegionPublic']);
    return $data;
  }
  // DI82
  public function getVirtualRegInfo($vreg) {
    $sql = "SELECT * FROM VirtualRegion WHERE VirtualRegId='".
           $vreg ."'";
    $res = $this->core->query($sql);
    return $res;
  }
  // DI82
  public function getVirtualRegItems($vreg) {
    $sql = "SELECT RegionId FROM VirtualRegionItem WHERE VirtualRegId='".
           $vreg ."' ORDER BY RegionId";
    $data = array();
    $res = $this->core->query($sql);
    foreach ($res as $row)
      $data[] = $row['RegionId'];
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
    //$datedb = $this->getDateRange();
    foreach ($dat as $k=>$v) {
      $k = str_replace(":", ".", $k);
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
        else if ($k == "D.DisasterSerial_" && $v == "NOT")
          $qp = $v;
        else if ($k == "D.DisasterSerial" && strlen($v) > 0) {
          if (isset($qp) && $qp == "NOT")
            $log = "AND $qp";
          else
            $log = "AND ";
          // Must to take some serial..
          if (strlen($v) > 0) {
            $serial = " $log(";
            foreach (explode(" ", $v) as $i)
              $serial .= "D.DisasterSerial='$i' OR ";
            $serial .= " 1!=1)";
          }
        }
        else if (is_array($v)) {
          if ($k == "D.DisasterBeginTime") {
            $aa = !empty($v[0])? $v[0] : "0000"; //substr($datedb[0], 0, 4);
            $mm = !empty($v[1])? $v[1] : "00";
            $dd = !empty($v[2])? $v[2] : "00";
            $begt = sprintf("%04d-%02d-%02d", $aa, $mm, $dd);
          }
          else if ($k == "D.DisasterEndTime") {
            $aa = !empty($v[0])? $v[0] : "9999"; //substr($datedb[1], 0, 4);
            $mm = !empty($v[1])? $v[1] : "12";
            $dd = !empty($v[2])? $v[2] : "31";
            $endt = sprintf("%04d-%02d-%02d", $aa, $mm, $dd);
          }
          else if ($k == "D.EventId" || $k == "D.CauseId") {
            $e[$k] = "(";
            foreach ($v as $i) {
              $e[$k] .= "$k = '$i' OR ";
            }
            $e[$k] .= "1!=1)";
          }
          else if ($k == "D.DisasterGeographyId") {
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
          else if ((substr($k, 2, 6) == "Effect" || substr($k, 2, 6) == "Sector") && isset($v[0])) {
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
        }
        // all minus DC hidden fields _MyField
        elseif (substr($k, 0, 1) != "_") 
          $e['Item'] .= "$k LIKE '%$v%' AND ";
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
    $e['Item'] .= "1=1";
    foreach ($e as $i)
      $sql .= "$i AND ";
    $sql .= "D.DisasterId = E.DisasterId $serial ";
    //echo $sql;
    return ($sql);
  }

  /* Counter results */
  public function genSQLSelectCount($whr) {
    $sql = "SELECT COUNT(D.DisasterId) as counter FROM Disaster AS D, EEData AS E ";
    if ($this->chkSQLWhere($whr))
      return ($sql . $whr);
    return false;
  }

  /* Generate SQL to data lists */
  public function genSQLSelectData ($dat, $fld, $order) {
    /* Process fields to show */
    $sql = "SELECT ". $fld ." FROM Disaster AS D, EEData AS E ";
    if ($this->chkSQLWhere($dat)) {
      $sql .= $dat;
      if (!empty($order))
        $sql .= "ORDER BY $order";
      //echo $sql;
      return ($sql);
    }
    else
      return false;
  }

  /* Generate Special SQL with grouped fields */
  public function genSQLProcess ($dat, $opc) {
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
            $sel[$j] = "";
            $grp[$j] = "";
            $per = explode("-", $gp[0]);
            foreach ($per as $period) {
              if (!empty($period)) {
              	// 2009-02-02 (jhcaiced) This should add a leading zero
              	// to month and day names in labels for graphs, so the 
              	// labels equal length and ordered correctly.
/*              	$iLabelLength = 2;
                if ($period == 'YEAR')
                  $iLabelLength = 4;
                $sPeriod = $period;
                if ($sPeriod == "WEEK")
                  $sPeriod = "WEEKOFYEAR";
                $sel[$j] .= "RIGHT(CONCAT('0'," . "$sPeriod(". $gp[1] .")),$iLabelLength),'-',";
                $grp[$j] .= "$sPeriod(". $gp[1] ."), ";*/
                $sel[$j] .= "STRFTIME('$period',". $gp[1] .") ";
                $grp[$j] .= "STRFTIME('$period',". $gp[1] .") ";
              }
            }
            $sel[$j] .= " AS ". substr($gp[1],2) ."_". substr($period, 1, 1); // delete last ,'-',
//            $grp[$j] = substr($grp[$j], 0, -2); // delete last ", "
          }
          else {
            $sel[$j] = $gp[1];
            $grp[$j] = $gp[1];
          }
          // 100, 10, 5, 1 years, month
          // $sta = explode("|", $opc['Stat']);
        break;
        case "D.DisasterGeographyId":
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
      }
      $j++;
    }
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
      }
      else {
        // Count Reports
        $sel[$j] = "COUNT(". $fl[0] .") AS ". substr($fl[0],2) ."_";
        // Counts Reports with "Hay"
        if ($fl[1] == "=")
          $whr[$j] = "OR (". $fl[0] . $fl[1] . $fl[2] .
                              " OR ". $fl[0] .">0)";
      }
      $j++;
    }
    // Code Select
    if ($this->chkSQLWhere($dat)) {
      $selec = implode(", ", $sel);
      $where = implode(" ", $whr);
      if (!empty($whr))
        $where = "AND (1!=1 $where)";
      $group = implode(", ", $grp);
      $sql = $this->genSQLSelectData ($dat, $selec, "");
      $sql .= "$where GROUP BY $group";
      //echo $sql; 
      return ($sql);
    }
    else
      return false;
  }

  /* Reformat array setting to arr[X1] = array {a, b, c, d..} */
  function prepareGraphic ($dl) {
    $res = array();
    $j = 0;
    foreach ($dl as $it) {
      foreach ($it as $k=>$i) {
        if ($j == 0)
          $res[$k] = array();
        $val = $i;
        if (substr($k,0,7) == "EventId")
          $val =  $this->getObjectNameById($i, DI_EVENT);
        if (substr($k,0,7) == "CauseId")
          $val = $this->getObjectNameById($i, DI_CAUSE);
        if (substr($k,0,19) == "DisasterGeographyId")
          $val = $this->getGeoNameById($i);
        array_push($res[$k], $val);
      }
      $j++;
    }
    return $res;
  }

  function prepareMaps ($dl) {
    $res = array();
    $j = 0;
    foreach ($dl as $it) {
      foreach ($it as $k=>$i) {
        if ($j == 0)
          $res[$k] = array();
        $val = $i;
        if (substr($k,0,19) == "DisasterGeographyId")
          $val = $this->getObjectNameById($i, DI_GEOGRAPHY);
        array_push($res[$k], $val);
      }
      $j++;
    }
    return $res;
  }
  
	/* Print results like associative array or csv */
	function printResults ($dl, $exp) {
		$csv = "";
		// Get results
		if (!empty($dl)) {
			$j = 0;
			foreach ($dl as $k=>$i) {
				//$dl[$j]["DATACARD"] = http_build_query($i);
				if (isset($dl[$j]["EventId"]))
					$dl[$j]["EventId"] = $this->getObjectNameById($i["EventId"], DI_EVENT);
				if (isset($dl[$j]["CauseId"]))
					$dl[$j]["CauseId"] = $this->getObjectNameById($i["CauseId"], DI_CAUSE);
				if (isset($dl[$j]["DisasterGeographyId"]))
					$dl[$j]["DisasterGeographyId"] = $this->getGeoNameById($i["DisasterGeographyId"]);
				// uhmm ugly...
				if (isset($dl[$j]["DisasterGeographyId_0"]))
					$dl[$j]["DisasterGeographyId_0"] = $this->getGeoNameById($i["DisasterGeographyId_0"]);
				if (isset($dl[$j]["DisasterGeographyId_1"]))
					$dl[$j]["DisasterGeographyId_1"] = $this->getGeoNameById($i["DisasterGeographyId_1"]);
				if (isset($dl[$j]["DisasterGeographyId_2"]))
					$dl[$j]["DisasterGeographyId_2"] = $this->getGeoNameById($i["DisasterGeographyId_2"]);
				if ($exp) {
					foreach (array_values($dl[$j]) as $vals) {
						if ($vals == -1)
							$myv = "YES";
						else
							$myv = $vals;
						$csv .= '"'. $myv .'",';
					} //foreach
					$csv .= "\n";
				} //if
				$j++;
			} //foreach
		} //if
		if ($exp)
			return $csv;
		else
			return $dl;
	} //function
  
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
        }
        else 
          $js[$ky] .= "'$k': '$v', ";
      }
      $js[$ky] .= "'_REG': '". $this->regid ."'}";
    }
    return $js;
  }
  
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
      $val = "MapOpt". $fd0 ."_";
      if (isset($dic[$val][0]))
        $info['TITLE'] = $dic[$val][0];
      else {
        $val = $fd0;
        if (isset($dic[$val][0]))
          $info['TITLE'] = $dic[$val][0];
      }
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
      if ($k == "DisasterGeographyId") {
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
      elseif ($k == "DisasterSource" && !empty($v))
        $info['SOU'] = $v;
      elseif ($k == "DisasterSerial" && !empty($v))
        $info['SER'] = $v;
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
  function queryLabelsFromGroup($labgrp, $langID) {
  	$dictio = '';
    $sql = "select g.LGName as lgn, g.LabelName as lbn, DictTranslation, ".
            "DictTechHelp, DictBasDesc, DictFullDesc from Dictionary d,".
            " LabelGroup g where (g.LGName like '". $labgrp ."%') and ".
            "(d.LangIsoCode='". $langID ."') and (d.DictLabelID = g.DictLabelID) ".
            "order by g.LGorder";
    foreach ($this->base->query($sql) as $row) {
      $grp = explode("|", $row['lgn']);
      $dictio[$grp[0].$row['lbn']] = array(
          $row['DictTranslation'],//utf8_encode($row['DicTranslation']), 
          $row['DictTechHelp'],//utf8_encode($row['DicTechHelp']),
          str2js($row['DictBasDesc']), $row['DictFullDesc']);
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

  function loadAllLang() {
    $sql = "select LangIsoCode, LangIsoName, LangLocalName, LangStatus from Language";
    foreach ($this->base->query($sql) as $row) {
      $lang[$row['LangIsoCode']] = array($row['LangLocalName'],
        $row['LangIsoName'], $row['LangStatus']);
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
  
  // Check
  function rebuildCore($fcore) {
    return true;
  }

} // end class

</script>
