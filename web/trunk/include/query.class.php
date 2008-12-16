<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

/* Construct and Apply queries 
 */ 

class Query extends mysqli
{
  public $regid = "";

  function __construct($region) {
    @parent::__construct('localhost', USR, PSW, DTB);
    if (mysqli_connect_errno())
      die(sprintf("Can't connect to database. Error: %s", mysqli_connect_error()));
    else {
      $this->set_charset("utf8");
      $this->regid = $region;
    }
  }
  
  public function __destruct() {
    if (!mysqli_connect_errno())
      $this->close();
  }

  public function getassoc($query) {
    if (!empty($query)) {
      $result = parent::query($query);
      if (mysqli_error($this))
        throw new Exception("Query exception:\n". mysqli_error($this));
      $data = array();
      while ($row = $result->fetch_assoc())
        $data[] = $row;
      $result->close();
      return $data;
    }
    else {
      echo "Query Empty";
      return false;
    }
  }

  public function getresult($query) {
    $result = parent::query($query);
    if (mysqli_error($this))
      throw new Exception("Query exception:\n". mysqli_error($this));
    return $result->fetch_assoc();
  }

  public function getnumrows($query) {
    $result = parent::query($query);
    if (mysqli_error($this))
      throw new Exception("Query exception:\n". mysqli_error($this));
    return $result->num_rows;
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
    $sql = "SELECT EventId, EventLocalName, EventLocalDesc FROM DIEvent ".
            "WHERE EventId!='UNKNOWN' AND EventLangCode='$lg' ORDER BY EventLocalName";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['EventId']] = array($row['EventLocalName'], $row['EventLocalDesc']);
    return $data;
  }
  
  public function getBasicCauseList($lg) {
    $sql = "SELECT CauseId, CauseLocalName, CauseLocalDesc FROM DICause ".
            "WHERE CauseLangCode='$lg' ORDER BY CauseLocalName";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['CauseId']] = array($row['CauseLocalName'], $row['CauseLocalDesc']);
    return $data;
  }

  public function getRegionEventList($type, $status, $lang) {
    if ($type == "PREDEF")
      $sqlt = "EventPreDefined = TRUE";
    else if ($type == "USER")
      $sqlt = "EventPreDefined = FALSE";
    else
      $sqlt = "'1=1'";	// all
    if ($status == "active")
      $sqls = "EventActive=TRUE";
    else
      $sqls = "'1=1'"; // all
    $sql = "SELECT * FROM ". $this->regid ."_Event WHERE ". $sqls ." AND ". 
        $sqlt ." ORDER BY EventLocalName";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['EventId']] = array($row['EventLocalName'], str2js($row['EventLocalDesc']), $row['EventActive']);
    return $data;
  }

  public function getRegionCauseList($type, $status, $lang) {
    if ($type == "PREDEF")
      $sqlt = "CausePreDefined = TRUE";
    else if ($type == "USER")
      $sqlt = "CausePreDefined = FALSE";
    else
      $sqlt = "'1=1'";	// all
    if ($status == "active")
      $sqls = "CauseActive=TRUE";
    else
      $sqls = "'1=1'"; // all
    $sql = "SELECT * FROM ". $this->regid ."_Cause WHERE ". $sqls ." AND ". 
        $sqlt ." ORDER BY CauseLocalName";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['CauseId']] = array($row['CauseLocalName'], str2js($row['CauseLocalDesc']), $row['CauseActive']);
    return $data;
  }

  /***** READ OBJECTS :: EVENT, CAUSE, GEOGRAPHY, GEOLEVEL READ *****/
  public function isvalidObjectToInactivate($id, $obj) {
    switch ($obj) {
      case DI_EVENT:			$whr = "EventId='$id'";		break;
      case DI_CAUSE:			$whr = "CauseId='$id'";		break;
      case DI_GEOGRAPHY:	$whr = "DisasterGeographyId like '$id%'";		break;
    }
    $sql = "SELECT COUNT(DisasterId) AS counter FROM ". $this->regid .
            "_Disaster WHERE $whr ";
    $res = $this->getresult($sql);
    if ($res['counter'] > 0)
      return false;
    else
      return true;
  }

  public function isvalidObjectName($id, $sugname, $obj) {
    $table = $this->regid;
    switch ($obj) {
      case DI_EVENT:			$name = "EventLocalName";	$table .= "_Event";			$fld = "EventId";			break;
      case DI_CAUSE:			$name = "CauseLocalName";	$table .= "_Cause";			$fld = "CauseId";			break;
      case DI_GEOGRAPHY:	$name = "GeographyCode";	$table .= "_Geography"; $fld = "GeographyId";	break;
      case DI_GEOLEVEL:		$name = "GeoLevelName";		$table .= "_GeoLevel"; 	$fld = "GeoLevelId";	break;
      case DI_EEFIELD:		$name = "EEFieldLabel";		$table .= "_EEField"; 	$fld = "EEFieldId";		break;
      case DI_DISASTER:		$name = "DisasterSerial";	$table .= "_Disaster"; 	$fld = "DisasterId";	break;
      case DI_REGION:			$name = "RegionUUID";			$table  = "Region"; 		$fld = "RegionUUID";	break;
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
      case DI_EVENT:			$name = "EventLocalName";	$table = "Event";			$fld = "EventId";		break;
      case DI_CAUSE:			$name = "CauseLocalName";	$table = "Cause";			$fld = "CauseId";		break;
      case DI_GEOGRAPHY:	$name = "GeographyCode";	$table = "Geography"; $fld = "GeographyId";	break;
      case DI_GEOLEVEL:		$name = "GeoLevelName";		$table = "GeoLevel"; 	$fld = "GeoLevelId";	break;
      default:						return null; 		break;
    }
    $sql = "SELECT $name FROM ". $this->regid . "_$table WHERE $fld = '$id'";
    $res = $this->getresult($sql);
    if (isset($res[$name]))
      return $res[$name];
    else
      return null;
  }

/*** GEOGRAPHY & GEO-LEVELS QUERIES  ***/
  function buildGeographyId($fid, $lev) {
    $sql = "SELECT MAX(GeographyId) AS max FROM ". $this->regid . "_Geography WHERE GeographyId ".
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
    $sql = "SELECT GeographyName FROM ". $this->regid . "_Geography WHERE 1!=1";
    $levn = (strlen($geoid) / 5);
    for ($n = 0; $n < $levn; $n++) {
      $len = 5 * ($n + 1);
      $geo = substr($geoid, 0, $len);
      $sql .= " OR GeographyId='". $geo ."'";
    }
    $data = "";
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data .= $row['GeographyName'] . "/";
    return $data;
  }

  /*** GEOGRAPHY ***/
  function loadGeography($level) {
    if (!is_numeric($level) && $level >= 0)
      return null;
    $sql = "SELECT * FROM ". $this->regid . "_Geography WHERE GeographyLevel = " . $level . 
           " AND GeographyId NOT LIKE '%00000' ORDER BY GeographyName";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['GeographyId']] = array($row['GeographyCode'], str2js($row['GeographyName']), $row['GeographyActive']);
    return $data;
  }

  function loadGeoChilds($geoid) {
    $level = $this->getNextLev($geoid);
    $sql = "SELECT * FROM ". $this->regid . "_Geography WHERE GeographyId LIKE '". $geoid .
           "%' AND GeographyLevel = " . $level . " ORDER BY GeographyName";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['GeographyId']] = array($row['GeographyCode'], $row['GeographyName'], $row['GeographyActive']);
    return $data;
  }

  function getNextLev($geoid) {
    return (strlen($geoid) / 5);
  }

  function loadGeoLevels($mapping) {
    $opc = "";
    if ($mapping == "map")
      $opc = "WHERE GeoLevelLayerFile != '' AND GeoLevelLayerCode != '' AND GeoLevelLayerName != ''";
    $sql = "SELECT * FROM ". $this->regid . "_GeoLevel $opc ORDER BY GeoLevelId";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['GeoLevelId']] = array(str2js($row['GeoLevelName']), str2js($row['GeoLevelDesc']), 
                  $row['GeoLevelLayerFile'], $row['GeoLevelLayerCode'], $row['GeoLevelLayerName']);
    return $data;
  }

  function getMaxGeoLev() {
    $sql = "SELECT MAX(GeoLevelId) AS max FROM ". $this->regid . "_GeoLevel";
    $res = $this->getresult($sql);
    if (isset($res['max']))
      return $res['max'];
    return -1;
  }

// uhmm check
  function loadGeoLevById($geolevid) {
    if (!is_numeric($geolevid))
      return null;
    $sql = "SELECT * FROM ". $this->regid . "_GeoLevel WHERE GeoLevelId=". $geolevid;
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data = array(str2js($row['GeoLevelName']), str2js($row['GeoLevelDesc']));
    return $data;
  }

  function getEEFieldList($act) {
    $sql = "SELECT * FROM ". $this->regid . "_EEField";
    if ($act != "")
      $sql .= " WHERE EEFieldActive=1";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['EEFieldId']] = array($row['EEFieldLabel'], str2js($row['EEFieldDesc']), 
          $row['EEFieldType'], $row['EEFieldSize'], $row['EEFieldActive'], $row['EEFieldPublic']);
    return $data;
  }
  
  function getEEFieldSeries() {
    $sql = "SELECT COUNT(EEFieldId) as count FROM ". $this->regid . "_EEField";
    $res = $this->getresult($sql);
    if (isset($res['count']))
      return sprintf("%03d", $res['count']);
    return -1;
  }

  /* GET DISASTERS INFO: DATES, DATACARDS NUMBER, ETC */
  function getDBInfo() {
    $sql = "SELECT * FROM Region WHERE RegionUUID='". $this->regid ."'";
    $res = $this->getresult($sql);
    return $res;
  }

  public function getDateRange() {
    $sql = "SELECT MIN(DisasterBeginTime) AS datemin, MAX(DisasterBeginTime) AS datemax FROM ". 
            $this->regid ."_Disaster WHERE RecordStatus='PUBLISHED'";
    $res = $this->getresult($sql);
    return array($res['datemin'], $res['datemax']);
  }

  public function getDisasterFld() {
    $sql = "DESCRIBE ". $this->regid ."_Disaster";
    $res = $this->getassoc($sql);
    foreach ($res as $it)
      $fld[] = $it['Field'];
    return $fld;
  }
  
  public function getDisasterBySerial($diser) {
    $sql = "SELECT * FROM ". $this->regid ."_Disaster WHERE DisasterSerial='$diser'";
    $res = $this->getassoc($sql);
    return $res;
  }

  public function getDisasterById($diid) {
    $sql = "SELECT * FROM ". $this->regid ."_Disaster WHERE DisasterId='$diid'";
    $res = $this->getassoc($sql);
    return $res;
  }

  // Get number of datacards by status: PUBLISHED, DRAFT, ..
  public function getNumDisasterByStatus($status) {
    $sql = "SELECT COUNT(DisasterId) AS counter FROM ".
             $this->regid ."_Disaster WHERE RecordStatus='$status'";
    $res = $this->getresult($sql);
    return $res['counter'];
  }
  
  public function getLastUpdate() {
    $sql = "SELECT MAX(RecordLastUpdate) AS lastupdate FROM ". 
            $this->regid ."_Disaster";
    $res = $this->getresult($sql);
    return substr($res['lastupdate'],0,10);
  }

  public function getFirstDisasterid() {
    $sql = "SELECT MIN(DisasterId) AS first FROM ". $this->regid ."_Disaster";
    $res = $this->getresult($sql);
    return $res['first'];
  }
  
  public function getPrevDisasterId($id) {
    $sql = "SELECT DisasterId AS prev FROM ". $this->regid .
        "_Disaster WHERE DisasterId < '$id' ORDER BY RecordLastUpdate,DisasterId DESC LIMIT 1;";
    $res = $this->getresult($sql);
    return $res['prev'];
  }
  
  public function getNextDisasterId($id) {
    $sql = "SELECT DisasterId AS next FROM ". $this->regid .
        "_Disaster WHERE DisasterId > '$id' ORDER BY RecordLastUpdate,DisasterId ASC LIMIT 1;";
    $res = $this->getresult($sql);
    return $res['next'];
  }

  public function getLastDisasterId() {
    $sql = "SELECT MAX(DisasterId) AS last FROM ". $this->regid ."_Disaster";
    $res = $this->getresult($sql);
    return $res['last'];
  }
  
  public function getRegLogList() {
    $sql = "SELECT DBLogDate, DBLogType, DBLogNotes FROM ". $this->regid ."_DatabaseLog ORDER BY DBLogDate DESC";
    $data = array();
    $res = $this->getassoc($sql);
    foreach($res as $row)
      $data[$row['DBLogDate']] = array($row['DBLogType'], str2js($row['DBLogNotes'])); 
    return $data;
  }

  /* GENERAL COUNTRIES, REGIONS AND VIRTUAL REGIONS FUNCTIONS */
  function getCountryByCode($idcnt) {
    $sql = "SELECT CountryName FROM Country WHERE CountryIsoCode = '$idcnt'";
    $res = $this->getresult($sql);
    return $res['CountryName'];
  }

  function getCountryList() {
    $sql = "SELECT CountryIsoCode, CountryName FROM Country ORDER BY CountryIsoName";
    $data = array();
    $res = $this->getassoc($sql);
    foreach ($res as $row)
      $data[$row['CountryIsoCode']] = $row['CountryName'];
    return $data;
  }

  function getRegionFieldByID($ruuid, $field) {
    $sql = "SELECT RegionUUID, $field FROM Region WHERE RegionUUID = '$ruuid'";
    $res = $this->getresult($sql);
    $data[$res['RegionUUID']] = $res[$field];
    return $data;
  }

  public function getRegionList($cnt, $status) {
    if (!empty($cnt))
      $opt = " CountryIsoCode='$cnt'";
    else
      $opt = " 1=1";
    if ($status == "ACTIVE")
      $opt .= " AND RegionActive = True";
    $sql = "SELECT RegionUUID, RegionLabel FROM Region WHERE $opt ORDER BY RegionLabel";
    $data = array();
    $res = $this->getassoc($sql);
    foreach ($res as $row)
      $data[$row['RegionUUID']] = $row['RegionLabel'];
    return $data;
  }

  public function getRegionAdminList() {
    $sql = "SELECT R.RegionUUID AS RegionUUID, R.CountryIsoCode AS CountryIsoCode, R.RegionLabel AS RegionLabel, ".
        "RA.UserName AS UserName, R.RegionActive AS RegionActive, R.RegionPublic AS RegionPublic ".
        "FROM Region AS R, RegionAuth AS RA WHERE R.RegionUUID=RA.RegionUUID AND RA.AuthAuxValue='ADMINREGION' ".
        "ORDER BY RegionLabel";
    $data = array();
    $res = $this->getassoc($sql);
    foreach ($res as $row)
      $data[$row['RegionUUID']] = array($row['CountryIsoCode'], $row['RegionLabel'], 
                    $row['UserName'], $row['RegionActive'], $row['RegionPublic']);
    return $data;
  }

  public function getVirtualRegInfo($vreg) {
    $sql = "SELECT * FROM VirtualRegion WHERE VirtualRegUUID='".
           $vreg ."'";
    $res = $this->getresult($sql);
    return $res;
  }
  
  public function getVirtualRegItems($vreg) {
    $sql = "SELECT RegionUUID FROM VirtualRegionItem WHERE VirtualRegUUID='".
           $vreg ."' ORDER BY RegionUUID";
    $data = array();
    $res = $this->getassoc($sql);
    foreach ($res as $row)
      $data[] = $row['RegionUUID'];
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
    //print_r($dat);
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
    $sql = "SELECT COUNT(D.DisasterId) as counter FROM ". $this->regid ."_Disaster AS D, ". $this->regid ."_EEData AS E ";
    if ($this->chkSQLWhere($whr))
      return ($sql . $whr);
    return false;
  }

  /* Generate SQL to data lists */
  public function genSQLSelectData ($dat, $fld, $order) {
    /* Process fields to show */
    $sql = "SELECT ". $fld ." FROM ". $this->regid ."_Disaster AS D, ". $this->regid ."_EEData AS E ";
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
            $sel[$j] = "CONCAT(";
            $grp[$j] = "";
            $per = explode("-", $gp[0]);
            foreach ($per as $period) {
              if (!empty($period)) {
                $sel[$j] .= "$period(". $gp[1] ."),'-',";
                $grp[$j] .= "$period(". $gp[1] ."), ";
              }
            }
            $sel[$j] = substr($sel[$j], 0, -5) .") AS ". substr($gp[1],2) ."_$period"; // delete last ,'-',
            $grp[$j] = substr($grp[$j], 0, -2); // delete last ", "
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
    $j = 0;
    $csv = "";
    // Get results
    if (!empty($dl)) {
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
          }
          $csv .= "\n";
        }
        $j++;
      }
    }
    if ($exp)
      return $csv;
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
  
} // end class

</script>
