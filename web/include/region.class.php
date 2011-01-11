<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

/* Class Region manage local databases in SQLITE / DICORE-XML.
   To operate require: loader.php and some functions query.php
 */ 

class Region {
  var $regid;
  var $q;

  function Region($region) {
    $this->regid = $region;
    $this->q = new Query($region);
    if (!empty($region)) {
      $rpcarg = array(session_id(), $region);
      //$or = callRpcDICore('RpcRegionOperations.openRegion', $rpcarg);
      //if (iserror($or))
      //  return false;
    }
  }

/*** EVENTS FUNCTIONS ***/
  function insertEvent($id, $name, $desc, $active) {
    $data['EventId'] = uuid();
    $data['EventName'] = $name;
    $data['EventDesc'] = $desc;
    $data['EventActive'] = $active;
    $data['EventPreDefined'] = false;
    $data['EventCreationDate'] = date("Y-m-d H:i:s");
    if ($this->q->isvalidObjectName($data['EventId'], $data['EventName'], DI_EVENT)) {
      $rpcargs = array($_SESSION['sessionid'], DI_EVENT, CMD_NEW, $data);
      $ev = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    else
      $ev = ERR_OBJECT_EXISTS;
    return $ev;
  }

  /* Update event in local and remote DB. If fail in remote return error
   */
  function updateEvent($id, $name, $desc, $active, $predef) {
    $data['EventId'] = $id;
    $data['EventName'] = $name;
    $data['EventDesc'] = $desc;
    $data['EventActive'] = $active;
    $data['EventPreDefined'] = $predef;
    $data['EventCreationDate'] = date("Y-m-d H:i:s");
    if (!$this->q->isvalidObjectName($id, $name, DI_EVENT))
      $ev = ERR_OBJECT_EXISTS;
    else if (!$active && !$this->q->isvalidObjectToInactivate($id, DI_EVENT))
      $ev = ERR_CONSTRAINT_FAIL;
    else {
      $rpcargs = array($_SESSION['sessionid'], DI_EVENT, CMD_UPDATE, $data);
      $ev = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    return $ev;
  }

/*** CAUSES FUNCTIONS ***/
  function insertCause($id, $name, $desc, $active) {
    $data['CauseId'] = uuid();
    $data['CauseName'] = $name;
    $data['CauseDesc'] = $desc;
    $data['CauseActive'] = $active;
    $data['CausePreDefined'] = false;
    $data['CauseCreationDate'] = date("Y-m-d H:i:s");
    if ($this->q->isvalidObjectName($data['CauseId'], $data['CauseName'], DI_CAUSE)) {
      $rpcargs = array($_SESSION['sessionid'], DI_CAUSE, CMD_NEW, $data);
      $ca = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    else
      $ca = ERR_OBJECT_EXISTS;
    return $ca;
  }

  /* Update cause in local and remote DB. If fail in remote return error
   */
  function updateCause($id, $name, $desc, $active, $predef) {
    $data['CauseId'] = $id;
    $data['CauseName'] = $name;
    $data['CauseDesc'] = $desc;
    $data['CauseActive'] = $active;
    $data['CausePreDefined'] = $predef;
    $data['CauseCreationDate'] = date("Y-m-d H:i:s");
    if (!$this->q->isvalidObjectName($id, $name, DI_CAUSE))
      $ca = ERR_OBJECT_EXISTS;
    else if (!$active && !$this->q->isvalidObjectToInactivate($id, DI_CAUSE))
      $ca = ERR_CONSTRAINT_FAIL;
    else {
      $rpcargs = array($_SESSION['sessionid'], DI_CAUSE, CMD_UPDATE, $data);
      $ca = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    return $ca;
  }

/*** GEOGRAPHY FUNCTIONS ***/
  function insertGeoItem($fid, $code, $name, $active) {
    $level = $this->q->getNextLev($fid);
    $myid = $this->q->buildGeographyId($fid, $level);
    $data['GeographyId'] = $myid;
    $data['GeographyCode'] = $code;
    $data['GeographyName'] = $name;
    $data['GeographyActive'] = $active;
    $data['GeographyLevel'] = $level;
    if ($this->q->isvalidObjectName($myid, $code, DI_GEOGRAPHY)) {
      $rpcargs = array($_SESSION['sessionid'], DI_GEOGRAPHY, CMD_NEW, $data);
      $gi = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    else
      $gi = ERR_OBJECT_EXISTS;
    return $gi;
  }

  function updateGeoItem($id, $code, $name, $active) {
    $level = $this->q->getNextLev($id) - 1;
    $data['GeographyId'] = $id;
    $data['GeographyCode'] = $code;
    $data['GeographyName'] = $name;
    $data['GeographyActive'] = $active;
    $data['GeographyLevel'] = $level;
    // if inactive only if not set..
    if (!$this->q->isvalidObjectName($id, $code, DI_GEOGRAPHY))
      $gi = ERR_OBJECT_EXISTS;
    else if (!$active && !$this->q->isvalidObjectToInactivate($id, DI_GEOGRAPHY))
      $gi = ERR_CONSTRAINT_FAIL;
    else {
      $rpcargs = array($_SESSION['sessionid'], DI_GEOGRAPHY, CMD_UPDATE, $data);
      $gi = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    return $gi;
  }
  
// obsolete?
  function isMapDefined() {
    $stat = false;
    $lg = $this->q->loadGeoLevels('', -1, false);
    foreach ($lg as $k=>$i) {
      if (!empty($i[2]) && !empty($i[3]) && !empty($i[4]))
        $stat = true;
    }
    return $stat;
  }

  function insertGeoLevel($name, $desc, $layer, $laycode, $layname) {
    $id = $this->q->getMaxGeoLev();
    $data['GeoLevelId'] = $id + 1;
    $data['GeoLevelName'] = $name;
    $data['GeoLevelDesc'] = $desc;
    $data['GeoLevelLayerFile'] = $layer;
    $data['GeoLevelLayerCode'] = $laycode;
    $data['GeoLevelLayerName'] = $layname;
    if ($this->q->isvalidObjectName($data['GeoLevelId'], $data['GeoLevelName'], DI_GEOLEVEL)) {
      $rpcargs = array($_SESSION['sessionid'], DI_GEOLEVEL, CMD_NEW, $data);
      $gl = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    else
      $gl = ERR_OBJECT_EXISTS;
    return $gl;
  }

  /* Update event in local and remote DB. If fail in remote return error
   */
  function updateGeoLevel($id, $name, $desc, $layer, $laycode, $layname) {
    $data['GeoLevelId'] = (int)$id;
    $data['GeoLevelName'] = $name;
    $data['GeoLevelDesc'] = $desc;
    $data['GeoLevelLayerFile'] = $layer;
    $data['GeoLevelLayerCode'] = $laycode;
    $data['GeoLevelLayerName'] = $layname;
    if ($this->q->isvalidObjectName($data['GeoLevelId'], $data['GeoLevelName'], DI_GEOLEVEL)) {
      $rpcargs = array($_SESSION['sessionid'], DI_GEOLEVEL, CMD_UPDATE, $data);
      $gl = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    else
      $gl = ERR_OBJECT_EXISTS;
    return $gl;
  }

/***** REGION-INFO & QUERIES FUNCTIONS *****/
  function updateDBInfo ($ruuid, $label, $desc, $desc2, $lang,
        $date1, $date2, $dateo, $minx, $miny, $maxx, $maxy) {
    $data['RegionUUID'] = $ruuid;
    $data['RegionLabel'] = $label;
    $data['RegionDesc'] = $desc;
    $data['RegionDescEN'] = $desc2;
    $data['RegionLangCode'] = $lang;
    $data['RegionStructLastUpdate'] = date("Y-m-d H:i:s");
    $data['PredefEventLastUpdate'] = "0001-01-01 00:00:00";
    $data['PredefCauseLastUpdate'] = "0001-01-01 00:00:00";
    if (empty($date1))
      $data['PeriodBeginDate'] = "0001-01-01 00:00:00";
    else
      $data['PeriodBeginDate'] = $date1;
    if (empty($date2))
      $data['PeriodEndDate'] = "9999-12-31 12:00:00";
    else
      $data['PeriodEndDate'] = $date2;
    $data['OptionOutOfPeriod'] = $dateo;
    $data['GeoLimitMinX'] = $minx;
    $data['GeoLimitMinY'] = $miny;
    $data['GeoLimitMaxX'] = $maxx;
    $data['GeoLimitMaxY'] = $maxy;
    $rpcargs = array(session_id(), DI_DBINFO, CMD_UPDATE, $data);
    $di = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    return $di;
  }

/**** STANDARDS FUNCTION TO GET GENERAL LIST. DON'T NEED OPEN A REGION ****/
  //change to interface
/*
  function getRegionList ($country) {
    $rpcarg= array($_SESSION['sessionid'], $country);
    $dbs = callRpcDICore('RpcRegionOperations.getRegionList', $rpcarg);
    return $dbs;
  }

  function getRegionAdminList ($ruuid) {
    $cnt = $this->getCountryList();
    $ral = array();
    foreach ($cnt as $k=>$v) {
      $reg = $this->getRegionList($k);
      foreach ($reg as $k2=>$v2) {
        $dat = array($k, $v2);
        $rpcarg= array($_SESSION['sessionid'], $k2);
        $rol = callRpcDICore('RpcUserOperations.getUserRoleByRegion', $rpcarg);
        if (!iserror($rol)) {
          if (is_array($rol)) {
            foreach ($rol as $us=>$rl)
              if ($rl == "ADMINREGION")
                array_push($dat, $us);
          }
          else
            array_push($dat, '');
        }
        $act = $this->getRegionByID($k2, "RegionActive");
        array_push($dat, $act[$k2]);
        $pub = $this->getRegionByID($k2, "RegionPublic");
        array_push($dat, $pub[$k2]);
        $ral[$k2] = $dat;
      }
    }
    return $ral;
  }

  function getRegionByID ($ruuid, $field) {
    $rpcarg= array($_SESSION['sessionid'], $ruuid, $field);
    $res = callRpcDICore('RpcRegionOperations.getRegionByID', $rpcarg);
    return $res;
  }
  // COUNTRIES FUNCTIONS
  function getCountryByCode ($country) {
    $rpcarg= array($_SESSION['sessionid'], $country);
    $cnt = callRpcDICore('RpcRegionOperations.getCountryByCode', $rpcarg);
    if (!iserror($cnt)) {
//	use current and key functions...
      $cname = array_keys($cnt);
      $cison = array_values($cnt);
      $data = array();
      if (is_array($cname) && is_array($cison)) {
        $data[0] = $cname[0];
        $data[1] = $cison[0];
      }
      return $data;
    }
    return $cnt;
  }

  function getCountryList () {
    $rpcarg= array($_SESSION['sessionid']);
    $cnt = callRpcDICore('RpcRegionOperations.getCountryList', $rpcarg);
    return $cnt;
  }
*/
  /* MANAGE REGION */
  function insertRegion ($ruuid, $label, $cnt, $act, $pub) {
    $data['RegionUUID'] 		= $ruuid;
    $data['RegionLabel'] 		= $label;
    $data['CountryIsoCode']	= $cnt;
    $data['RegionStructLastUpdate'] = date("Y-m-d H:i:s");
    $data['RegionActive'] 	= $act;
    $data['RegionPublic'] 	= $pub;
    $rpcargs = array($_SESSION['sessionid'], $data);
    $rg = callRpcDICore('RpcRegionOperations.createRegion', $rpcargs);
    return $rg;
  }

  function updateRegion ($ruuid, $label, $cnt, $act, $pub) {
    $data['RegionUUID'] 		= $ruuid;
    $data['RegionLabel'] 		= $label;
    $data['CountryIsoCode']	= $cnt;
    $data['RegionStructLastUpdate'] = date("Y-m-d H:i:s");
    $data['RegionActive'] 	= $act;
    $data['RegionPublic'] 	= $pub;
    $rpcargs = array($_SESSION['sessionid'], DI_REGION, CMD_UPDATE, $data);
    $rg = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    return $rg;
  }
  
/**** LOG FUNCTIONS ****/
/*
  function getRegLogList() {
    $rpcarg= array($_SESSION['sessionid']);
    $log = callRpcDICore('RpcRegionOperations.getLogList', $rpcarg);
    if (!iserror($log)) {
      $data = array();
      foreach ($log as $k=>$v)
        $data[$k] = explode("|", str2js($v));
      return $data;
    }
    return $log;
  }
 */ 
  function insertRegLog($type, $note) {
    $data['DBLogDate'] = date("Y-m-d H:i:s");
    $data['DBLogType'] = $type;
    $data['DBLogNotes'] = $note;
    $data['DBLogUserId'] = $_SESSION['UserId'];
    $data['DBLogDisasterIdList'] = "";
    $rpcargs = array($_SESSION['sessionid'], DI_DBLOG, CMD_NEW, $data);
    $lo = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    return $lo;
  }

  /* Update log in remote DB. If fail return error
   */
  function updateRegLog($date, $type, $note) {
    $data['DBLogDate'] = $date;
    $data['DBLogType'] = $type;
    $data['DBLogNotes'] = $note;
    $data['DBLogUserId'] = $_SESSION['UserId'];
    $data['DBLogDisasterIdList'] = "";
    $rpcargs = array($_SESSION['sessionid'], DI_DBLOG, CMD_UPDATE, $data);
    $lo = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    return $lo;
  }

/**** EXTRA EFFECTS FUNCTIONS ****/
  function insertEEField($label, $desc, $type, $size, $act, $pub) {
    $id = "EEF" . $this->q->getEEFieldSeries();
    $data['EEFieldId'] 		= $id;
    $data['EEGroupId'] 		= "";
    $data['EEFieldLabel'] = $label;
    $data['EEFieldDesc'] 	= $desc;
    $data['EEFieldType'] 	= $type;
    $data['EEFieldSize'] 	= (int)$size;
    $data['EEFieldOrder'] = 0;
    $data['EEFieldActive']= $act;
    $data['EEFieldPublic']= $pub;
    // validate for duplicates labels
    if ($this->q->isvalidObjectName($data['EEFieldId'], $data['EEFieldLabel'], DI_EEFIELD)) {
      $rpcargs = array($_SESSION['sessionid'], DI_EEFIELD, CMD_NEW, $data);
      $ee = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    else
      $ee = ERR_OBJECT_EXISTS;
    return $ee;
  }

  // Update ExtraEffects field 
  function updateEEField($id, $label, $desc, $type, $size, $act, $pub) {
    $data['EEFieldId'] 		= $id;
    $data['EEGroupId']    = "";
    $data['EEFieldLabel'] = $label;
    $data['EEFieldDesc'] 	= $desc;
    $data['EEFieldType'] 	= $type;
    $data['EEFieldSize'] 	= (int)$size;
    $data['EEFieldOrder'] = 0;
    $data['EEFieldActive']= $act;
    $data['EEFieldPublic']= $pub;
    // validate for duplicates labels
    if ($this->q->isvalidObjectName($data['EEFieldId'], $data['EEFieldLabel'], DI_EEFIELD)) {
      $rpcargs = array($_SESSION['sessionid'], DI_EEFIELD, CMD_UPDATE, $data);
      $ee = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    }
    else
      $ee = ERR_OBJECT_EXISTS;
    return $ee;
  }

}//end Class

</script>
