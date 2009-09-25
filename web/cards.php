<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 ***********************************************/
    
require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/region.class.php');
require_once('include/didisaster.class.php');
require_once('include/dieedata.class.php');

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
			    (substr($k, 0, 19) != 'GeographyId') &&
			    (substr($k, 0, 3)  != 'EEF'))
			    $data[$k] = $i;
			else {
				if (substr($k, 0, 19) == 'GeographyId') {
					$geogid = $i;
				}
			}
		} //if
	} //foreach
	if ($icmd == CMD_NEW) {
		// New Disaster
		$data['DisasterId'] = uuid();
		$data['RecordCreation'] = date("Y-m-d H:i:s");
	} elseif ($icmd == CMD_UPDATE) {
		// On Update
		$data['DisasterId'] = $form['DisasterId'];
		$data['RecordCreation'] = $form['RecordCreation'];
	}
	$data['RecordAuthor'] = $form['RecordAuthor'];
	$data['RecordUpdate'] = gmdate('c');
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
	$data['GeographyId'] = $geogid;
	return $data;
} // function

function form2eedata($form) {
	$eedat['DisasterId'] = $form['DisasterId'];
	foreach ($form as $k=>$i) {
		if (substr($k, 0, 3) == 'EEF')
			$eedat[$k] = $i;
	}
	return $eedat;
}

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

// 2009-08-07 (jhcaiced) Validate if Database Exists...
if (! file_exists($us->q->getDBFile($sRegionId))) {
	print "<h3>Requested Region doesn't exist<br>";
	exit();
}

// UPDATER: If user is still connected, awake session so it will not expire
if (isset($_GET['u'])) {
	$t->assign ("ctl_updater", true);
	$res = $us->awake();
	if (!iserror($res))
		$status = "green";
	else
		$status = "red";
	$t->assign ("stat", $status);
} else {
	// Get Geography elements 
	if (isset($_GET['cmd'])) {
		// Commands in GET mode: lists, checkings..
		switch ($_GET['cmd']) {
			case "list":
				$lev = $us->q->getNextLev($_GET['GeographyId']);
				$t->assign ("lev", $lev);
				$t->assign ("levmax", $us->q->getMaxGeoLev());
				$t->assign ("levname", $us->q->loadGeoLevById($lev));
				$t->assign ("geol", $us->q->loadGeoChilds($_GET['GeographyId']));
				$t->assign ("opc", isset($_GET['opc']) ? $_GET['opc'] : '');
				$t->assign ("ctl_geolist", true);
			break;
			case "getNextSerial":
				$ser = $us->q->getNextDisasterSerial($_GET['value']);
				echo $ser;
			break;
			case "getPrevDId":
				$prv = $us->q->getPrevDisasterId($_GET['value']);
				echo $prv;
			break;
			case "getNextDId":
				$nxt = $us->q->getNextDisasterId($_GET['value']);
				echo $nxt;
			break;
			case "chklocked":
				// check if datacard is locked by some user
				$r = $us->isDatacardLocked($_GET['DisasterId']);
				if ($r == '') {
					// reserve datacard
					$us->lockDatacard($_GET['DisasterId']);
					echo "RESERVED";
				} else {
					echo "BLOCKED";
				}
			break;
			case "chkrelease":
				$us->releaseDatacard($_GET['DisasterId']);
			break;
			case "getIdfromSerial":
				$did = $us->q->getDisasterIdFromSerial($_GET['value']);
				echo $did;
			break;
			default: break;
		}
	} elseif (isset($_GET['DisasterId']) && !empty($_GET['DisasterId'])) {
		$dcard = $us->q->hash2json($us->q->getDisasterById($_GET['DisasterId']));
		if (isset($dcard[0]))
			echo $dcard[0];
	} elseif (isset($_POST['_CMD'])) {
		// Commands in POST mode: insert, update, search.. datacards.. 
		$us->releaseDatacard($_POST['DisasterId']);
		if ($_POST['_CMD'] == "insertDICard") {
			// Insert New Datacard
			$data = form2disaster($_POST, CMD_NEW);
			//echo "<!--"; print_r($data); echo "-->\n";
			$o = new DIDisaster($us, $data['DisasterId']);
			$o->setFromArray($data);
			$o->set('RecordCreation', gmdate('c'));
			$o->set('RecordUpdate', gmdate('c'));
			$i = $o->insert();
			$t->assign ("statusmsg", "insertok");
			if (!iserror($i)) {
				// Save EEData ....
				$t->assign ("diserial", $data['DisasterSerial']);
				// If Datacard is valid, update EEData Table..
				$eedat = form2eedata($_POST);
				$eedat['DisasterId'] = $data['DisasterId'];
				//echo "<!--"; print_r($eedat); echo "-->\n";
				$o = new DIEEData($us, $eedat['DisasterId']);
				$o->setFromArray($eedat);
				$i = $o->insert();
			} else {
				$t->assign ("statusmsg", showerror($i));
			}
		} elseif ($_POST['_CMD'] == "updateDICard") {
			// Update Existing Datacard
			$data = form2disaster($_POST, CMD_UPDATE);
			//echo "<!--"; print_r($data); echo "-->\n";
			$o = new DIDisaster($us, $data['DisasterId']);
			$o->load();
			$o->setFromArray($data);
			$o->set('RecordUpdate', gmdate('c'));
			$i = $o->update();
			$t->assign ("statusmsg", "updateok");
			if (!iserror($i)) {
				// Save EEData ....
				$t->assign ("diserial", $data['DisasterSerial']);
				// If Datacard is valid, update EEData Table..
				$eedat = form2eedata($_POST);
				$eedat['DisasterId'] = $data['DisasterId'];
				//echo "<!--"; print_r($eedat); echo "-->\n";
				$o = new DIEEData($us, $eedat['DisasterId']);
				$o->setFromArray($eedat);
				$i = $o->update();
			} else {
				$t->assign ("statusmsg", showerror($i));
			}
		}
		$t->assign ("dipub", $us->q->getNumDisasterByStatus("PUBLISHED"));
		$t->assign ("direa", $us->q->getNumDisasterByStatus("READY"));
		$t->assign ("ctl_result", true);
		// End _CMD Block
	} else {
		//if ($us->UserId == '' || $us->getUserRole($sRegionId == '')) {}
		// Default view of DesInventar
		$t->assign ("usr", $us->UserId);
		$t->assign ("regname", $us->q->getDBInfoValue('RegionLabel'));
		$role = $us->getUserRole($sRegionId);
		// Validate if user has permission to access database
		$dic = $us->q->queryLabelsFromGroup('DB', $lg);
		switch ($role) {
			case "ADMINREGION":
				$t->assign ("showconfig", true);
				$dicrole = $dic['DBRoleAdmin'][0];
			break;
			case "OBSERVER":
				$t->assign ("showconfig", true);
				$t->assign ("ro", "disabled");
				$dicrole = $dic['DBRoleObserver'][0];
			break;
			case "SUPERVISOR":
				$dicrole = $dic['DBRoleSupervisor'][0];
			break;
			case "USER":
				$dicrole = $dic['DBRoleUser'][0];
			break;
			default:
				$dicrole = null;
			break;
		}
		$t->assign ("dicrole", $dicrole);
		$t->assign ("ctl_effects", true);
		$dis = $us->q->queryLabelsFromGroup('Disaster', $lg);
		$dis = array_merge($dis, $us->q->queryLabelsFromGroup('Geography', $lg));
		$t->assign ("dis", $dis);
		$t->assign ("rc1", $us->q->queryLabelsFromGroup('Record|1', $lg));
		$t->assign ("rc2", $us->q->queryLabelsFromGroup('Record|2', $lg));
		$t->assign ("eve", $us->q->queryLabelsFromGroup('Event', $lg));
		$t->assign ("cau", $us->q->queryLabelsFromGroup('Cause', $lg));
		$t->assign ("ef1", $us->q->queryLabelsFromGroup('Effect|People', $lg));
		$t->assign ("ef2", $us->q->queryLabelsFromGroup('Effect|Economic', $lg));
		$t->assign ("ef3", $us->q->queryLabelsFromGroup('Effect|Affected', $lg));
		$t->assign ("sc3", $us->q->querySecLabelFromGroup('Effect|Affected', $lg));
		$t->assign ("ef4", $us->q->queryLabelsFromGroup('Effect|More', $lg));
		$t->assign ("sec", $us->q->queryLabelsFromGroup('Sector', $lg));
		//$t->assign ("rcsl", $us->q->queryLabelsFromGroup('RecordStatus', $lg));
		$t->assign ("dmg", $us->q->queryLabelsFromGroup('MetGuide', $lg));
		$t->assign ("levl", $us->q->loadGeoLevels('', -1, false));
		$lev = 0;
		$t->assign ("lev", $lev);
		$t->assign ("levmax", $us->q->getMaxGeoLev());
		$t->assign ("levname", $us->q->loadGeoLevById($lev));
		$t->assign ("geol", $us->q->loadGeography($lev));
		$t->assign ("ctl_geolist", true);
		$t->assign ("evel", $us->q->loadEvents(null, "active", $lg));
		$t->assign ("caul", $us->q->loadCauses(null, "active", $lg));
		$t->assign ("eefl", $us->q->getEEFieldList("True"));
		$t->assign ("role", $role);
		if ($role == "USER" || $role == "SUPERVISOR" || $role == "ADMINREGION")
			$t->assign ("ctl_validrole", true);
		// get first and last datacard
		$fst = $us->q->hash2json($us->q->getDisasterById($us->q->getFirstDisasterid()));
		$lst = $us->q->hash2json($us->q->getDisasterById($us->q->getLastDisasterid()));
		if (isset($fst[0]))
			$t->assign ("fst", $fst[0]);
		if (isset($lst[0]))
			$t->assign ("lst", $lst[0]);
	}
	$t->assign ("reg", $sRegionId);
}

$t->display ("cards.tpl");

</script>
