<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 ***********************************************/
    
require_once('../include/loader.php');
require_once('../include/query.class.php');
require_once('../include/region.class.php');
require_once('../include/didisaster.class.php');

/* Convert Post Form to DesInventar Disaster Table struct 
 * Insert		 	(1) create DisasterId. 
 * Update     (2) keep RecordCreation and RecordAuthor 
 */
function form2disaster($form, $icmd) {
  $data = array();
  $geogid = "";
  foreach ($form as $k=>$i) {
    if (($icmd == CMD_NEW) || ($icmd == CMD_UPDATE)) {
      if ((substr($k, 0, 1)  != '_') &&
          (substr($k, 0, 12) != 'RecordAuthor') &&
          (substr($k, 0, 19) != 'DisasterGeographyId') &&
          (substr($k, 0, 3)  != 'EEF'))
        $data[$k] = $i;
      else if (substr($k, 0, 19) == 'DisasterGeographyId')
        $geogid = $i;
    }
  }
  // New Disaster
  if ($icmd == CMD_NEW) {
    $data['DisasterId'] = uuid();
    $data['RecordCreation'] = date("Y-m-d H:i:s");
  }
  // On Update
  elseif ($icmd == CMD_UPDATE) {
    $data['DisasterId'] = $form['DisasterId'];
    $data['RecordCreation'] = $form['RecordCreation'];
  }
  $data['RecordAuthor'] = $form['RecordAuthor'];
  $data['RecordLastUpdate'] = date("Y-m-d H:i:s");
  $c = "";
  // Disaster date
  $aaaa = $form[$c .'DisasterBeginTime'][0];
  if (!empty($form[$c .'DisasterBeginTime'][1]))
    $mm = $form[$c .'DisasterBeginTime'][1];
  else
    $mm = "00";
  if (!empty($form[$c .'DisasterBeginTime'][2]))
    $dd = $form[$c .'DisasterBeginTime'][2];
  else
    $dd = "00";
  $data['DisasterBeginTime'] = sprintf("%04d-%02d-%02d", $aaaa, $mm, $dd);
  // Disaster Geography
  $data['DisasterGeographyId'] = $geogid;
  return $data;
}

function form2eedata($form) {
  $eedat['DisasterId'] = $form['DisasterId'];
  foreach ($form as $k=>$i) {
    if (substr($k, 0, 3) == 'EEF')
      $eedat[$k] = $i;
  }
  return $eedat;
}
/* Convert DesInventar Disaster Table struct to Post Form. 
 * Only for DICard in JSON Search..
function disaster2form($dicard) {
  $data = array ();
  foreach ($dicard as $k=>$i) {
    if ((substr($k, 0, 17) != 'DisasterBeginTime') &&
        (substr($k, 0, 19) != 'DisasterGeographyId'))
      $data[$k] = $i;
    else if (substr($k, 0, 17) == 'DisasterBeginTime') {
      $date = explode('-', $i);
      $data['DisasterBeginTime'] = isset($date[0]) ? $date[0] : "";
      $data['DisasterBeginTime'] = isset($date[1]) ? $date[1] : "";
      $data['DisasterBeginTime'] = isset($date[2]) ? $date[2] : "";
    }
    else if (substr($k, 0, 19) == 'DisasterGeographyId') {
      $levn = (strlen($i) / 5);
      for ($n = 0; $n < $levn; $n++) {
        $len = 5 * ($n + 1);
        $data['DisasterGeographyId'. $n] = substr($i, 0, $len);
      }
    }
  }
  return $data;
}
*/

if (isset($_POST['_REG']) && !empty($_POST['_REG'])) {
	$sRegionId = $_POST['_REG'];
	$us->open($sRegionId);
} elseif (isset($_GET['r']) && !empty($_GET['r'])) {
	$sRegionId = $_GET['r'];
	$us->open($sRegionId);
} else {
	// Use Region Information from UserSession...
	$sRegionId = $us->sRegionId;
}
	
//else
//  exit();

// UPDATER: If user is still connected, awake session so it will not expire
if (isset($_GET['u'])) {
  $t->assign ("ctl_updater", true);
  $res = $us->awake();
  if (!iserror($res))
    $status = "green";
  else
    $status = "red";
  $t->assign ("stat", $status);
}
else {
  $r = new Region($sRegionId);
  $q = new query($sRegionId);
  // Get Geography elements 
  if (isset($_GET['cmd'])) {
    if ($_GET['cmd'] == "list") {
      $lev = $q->getNextLev($_GET['GeographyId']);
      $t->assign ("lev", $lev);
      $t->assign ("levmax", $q->getMaxGeoLev());
      $t->assign ("levname", $q->loadGeoLevById($lev));
      $t->assign ("geol", $q->loadGeoChilds($_GET['GeographyId']));
      $t->assign ("opc", isset($_GET['opc']) ? $_GET['opc'] : '');
      $t->assign ("ctl_geolist", true);
    }
    if ($_GET['cmd'] == "chkdiserial") {
      $chk = $q->isvalidObjectName($_GET['DisasterId'], $_GET['DisasterSerial'], DI_DISASTER);
      if ($chk && !empty($_GET['DisasterSerial']))
        echo "FREE";
      else
        echo "BUSY";
    }
    if ($_GET['cmd'] == "chklocked") {
      // check if datacard isblocked!
      $ra_ser = array($_SESSION['sessionid'], $_GET['DisasterId']);
      $dcl = callRpcDICore('RpcRegionOperations.isDatacardLocked', $ra_ser);
      if ($dcl == 0) {
        // reserve datacard
        $ra_ser = array($_SESSION['sessionid'], $_GET['DisasterId']);
        $dca = callRpcDICore('RpcRegionOperations.acquireDatacardLock', $ra_ser);
        echo "RESERVED";
      }
      else
        echo "BLOCKED";
    }
    if ($_GET['cmd'] == "chkrelease") {
      $ra_ser = array($_SESSION['sessionid'], $_GET['DisasterId']);
      $dcr = callRpcDICore('RpcRegionOperations.releaseDatacardLock', $ra_ser);
    }
  }
  /*
  // Find with DisasterID, return a JSON object with Disaster Form values.
  else if (isset($_GET['action']) && $_GET['action'] == "findDIId") {
    $diser = $_GET["data"];
    $diser = str_replace("\\\"","\"", $diser);
    $dip = $q->getDisasterBySerial($diser);
    if (!iserror($dip)) {
      header('Content-type: text/json');
      $dform = disaster2form($dip);
      echo json_safe_encode($dform);
    }
    else
      echo showerror($dip);
  }
  */
  // Check values of _CMD: search | addDICard | updDICard
  else {
    if (isset($_POST['_CMD'])) {
      // Insert DICard in Database through DICORE
      // First release datacard
      $ra_ser = array($_SESSION['sessionid'], $_POST['DisasterId']);
      $dcr = callRpcDICore('RpcRegionOperations.releaseDatacardLock', $ra_ser);
      // Let duplicate serial...
      //if ($q->isvalidObjectName($_POST['DisasterId'], $_POST['DisasterSerial'], DI_DISASTER)) {
        if ($_POST['_CMD'] == "insertDICard") {
          $data = form2disaster($_POST, CMD_NEW);
          echo "<!--"; print_r($data); echo "-->\n";
          $rpcargs = array($_SESSION['sessionid'], DI_DISASTER, CMD_NEW, $data);
          $dip = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
          $t->assign ("statusmsg", "insertok");
        }
        // Update DICard in Database through DICORE
        else if ($_POST['_CMD'] == "updateDICard") {
          $data = form2disaster($_POST, CMD_UPDATE);
          echo "<!--"; print_r($data); echo "-->\n";
          if ($data['RecordStatus'] == "DELETED")
            $rpcargs = array($_SESSION['sessionid'], DI_DISASTER, CMD_DELETE, $data);
          else
            $rpcargs = array($_SESSION['sessionid'], DI_DISASTER, CMD_UPDATE, $data);
          $dip = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
          $t->assign ("statusmsg", "updateok");
        }
        if (!iserror($dip)) {
          $t->assign ("diserial", $data['DisasterSerial']);
          // If Datacard is valid, update EEData Table..
          $eedat = form2eedata($_POST);
          // uhmm, dicore will send DisasterId when him create this..
          $eedat['DisasterId'] = $data['DisasterId'];
          echo "<!--"; print_r($eedat); echo "-->\n";
          $rpcarg2 = array($_SESSION['sessionid'], DI_EEDATA, CMD_UPDATE, $eedat);
          $dee = callRpcDICore('RpcDIServer.saveDIObject', $rpcarg2);
        }
        else
          $t->assign ("statusmsg", showerror($dip));
      //}
      //else
      //  $t->assign ("statusmsg", "duplicate");
      $t->assign ("dipub", $q->getNumDisasterByStatus("PUBLISHED"));
      $t->assign ("direa", $q->getNumDisasterByStatus("READY"));
      $t->assign ("ctl_result", true);
    }
    else {
      // Default view of DesInventar
      $t->assign ("usr", $us->sUserName);
      $rinfo = $q->getDBInfo();
      $t->assign ("regname",  $rinfo['RegionLabel']);
      $role = $us->getUserRole($sRegionId);
      $t->assign ("role", $role);
      $dic = $q->queryLabelsFromGroup('DB', $lg);
      if ($role == "ADMINREGION") {
        $t->assign ("showconfig", true);
        $dicrole = $dic['DBRoleAdmin'][0];
      }
      elseif ($role=="OBSERVER") {
        $t->assign ("showconfig", true);
        $t->assign ("ro", "disabled");
        $dicrole = $dic['DBRoleObserver'][0];
      }
      elseif ($role=="SUPERVISOR") {
        $dicrole = $dic['DBRoleSupervisor'][0];
      }
      else {
        $dicrole = $dic['DBRoleUser'][0];
      }
      $t->assign ("dicrole", $dicrole);
      
      if ($role=="USER" || $role=="SUPERVISOR" || $role=="ADMINREGION" || $role=="OBSERVER") {
        $t->assign ("ctl_effects", true);
        $t->assign ("dis", $q->queryLabelsFromGroup('Disaster', $lg));
        $t->assign ("rc1", $q->queryLabelsFromGroup('Record|1', $lg));
        $t->assign ("rc2", $q->queryLabelsFromGroup('Record|2', $lg));
        $t->assign ("eve", $q->queryLabelsFromGroup('Event', $lg));
        $t->assign ("cau", $q->queryLabelsFromGroup('Cause', $lg));
        $t->assign ("ef1", $q->queryLabelsFromGroup('Effect|People', $lg));
        $t->assign ("ef2", $q->queryLabelsFromGroup('Effect|Economic', $lg));
        $t->assign ("ef3", $q->queryLabelsFromGroup('Effect|Affected', $lg));
        $t->assign ("sc3", $q->querySecLabelFromGroup('Effect|Affected', $lg));
        $t->assign ("ef4", $q->queryLabelsFromGroup('Effect|More', $lg));
        $t->assign ("sec", $q->queryLabelsFromGroup('Sector', $lg));
  //      $t->assign ("rcsl", $q->queryLabelsFromGroup('RecordStatus', $lg));
        $t->assign ("dmg", $q->queryLabelsFromGroup('MetGuide', $lg));
        $t->assign ("levl", $q->loadGeoLevels(""));
        $lev = 0;
        $t->assign ("lev", $lev);
        $t->assign ("levmax", $q->getMaxGeoLev());
        $t->assign ("levname", $q->loadGeoLevById($lev));
        $t->assign ("geol", $q->loadGeography($lev));
        $t->assign ("ctl_geolist", true);
        $t->assign ("evel", $q->loadEvents(null, "active", $lg));
        $t->assign ("caul", $q->loadCauses(null, "active", $lg));
        $t->assign ("eefl", $q->getEEFieldList("True"));
        if ($role=="SUPERVISOR" || $role=="ADMINREGION")
          $t->assign ("ctl_rcsl", true);
        // get first and last datacard
        $fst = $q->hash2json($q->getDisasterById($q->getFirstDisasterid()));
        $lst = $q->hash2json($q->getDisasterById($q->getLastDisasterid()));
        if (isset($fst[0])) $t->assign ("fst", $fst[0]);
        if (isset($lst[0])) $t->assign ("lst", $lst[0]);
      }
    }
  }
  $t->assign ("reg", $sRegionId);
}
$t->display ("index.tpl");

</script>
