<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1998-2010 Corporacion OSSO
 ***********************************************/
    
require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/didisaster.class.php');
require_once('include/dieedata.class.php');

/* Convert Post Form to DesInventar Disaster Table struct 
 * Insert  (1) create DisasterId. 
 * Update  (2) keep RecordCreation and RecordAuthor 
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
	if (!empty($form[$c .'DisasterBeginTime'][1])) {
		$mm = $form[$c .'DisasterBeginTime'][1];
	} else {
		$mm = "00";
	}
	if (!empty($form[$c .'DisasterBeginTime'][2])) {
		$dd = $form[$c .'DisasterBeginTime'][2];
	} else {
		$dd = "00";
	}
	$data['DisasterBeginTime'] = sprintf("%04d-%02d-%02d", $aaaa, $mm, $dd);
	// Disaster Geography
	$data['GeographyId'] = $geogid;
	return $data;
} // function

function form2eedata($form) {
	$eedat['DisasterId'] = $form['DisasterId'];
	foreach ($form as $k=>$i) {
		if (substr($k, 0, 3) == 'EEF') {
			$eedat[$k] = $i;
		}
	}
	return $eedat;
}

$RegionId = getParameter('RegionId', getParameter('r',''));
if ( ($RegionId == '') || ($RegionId == 'undefined') ) {
	if ($us->RegionId != 'core') {
		$RegionId = $us->RegionId;
	}
}

if ($RegionId == '') {
	exit(0);
} else {
	$us->open($RegionId);
	$t->assign('RegionId', $RegionId);
}

// 2009-08-07 (jhcaiced) Validate if Database Exists...
if (! file_exists($us->q->getDBFile($RegionId))) {
	print "<h3>Requested Region doesn't exist<br>";
	exit();
}

// UPDATER: If user is still connected, awake session so it will not expire
if (isset($_GET['u'])) {
	$res = $us->awake();
	if (!iserror($res)) {
		$status = "green";
	} else {
		$status = "red";
	}
	$t->assign("stat", $status);
	$t->display('cards_updater.tpl');
} else {
	$t->assign('RegionId', $RegionId);
	$cmd = getParameter('cmd','');
	$value = getParameter('value','');
	if ($cmd != '') {
		// Commands in GET mode: lists, checkings..
		switch ($cmd) {
			case "list":
				$lev = $us->q->getNextLev($_GET['GeographyId']);
				$levmax = $us->q->getMaxGeoLev();
				$levname = $us->q->loadGeoLevById($lev);
				$geol = $us->q->loadGeoChilds($_GET['GeographyId']);
				$t->assign("lev", $lev);
				$t->assign("levmax", $levmax);
				$t->assign("levname", $levname);
				$t->assign("geol", $geol);
				$t->assign("opc", isset($_GET['opc']) ? $_GET['opc'] : '');
				$t->display("cards_geolist.tpl");
			break;
			case "getNextSerial":
				$ser = $us->q->getNextDisasterSerial($value);
				echo json_encode(array('Status' => 'OK', 'DisasterSerial' => $ser));
			break;
			case "getDisasterIdPrev":
				$answer = $us->getDisasterIdPrev($value);
				echo json_encode($answer);
			break;
			case "getDisasterIdNext":
				$answer = $us->getDisasterIdNext($value);
				echo json_encode($answer);
			break;
			case 'getDisasterIdFirst':
				$answer = $us->getDisasterIdFirst();
				echo json_encode($answer);
			break;
			case 'getDisasterIdLast':
				$answer = $us->getDisasterIdLast();
				echo json_encode($answer);
			break;
			case "getDisasterIdFromSerial":
				$answer = $us->getDisasterIdFromSerial($value);
				echo json_encode($answer);
			break;
			case 'existDisasterSerial':
				$DisasterSerial = getParameter('DisasterSerial');
				$answer = $us->existDisasterSerial($DisasterSerial);
				print json_encode($answer);
			break;
			case "chklocked":
				// check if datacard is locked by some user
				$answer = array('Status' => 'OK','DatacardStatus' => '');
				$reserv = $us->isDatacardLocked($_GET['DisasterId']);
				if ($reserv == '') {
					// reserve datacard
					$us->lockDatacard($_GET['DisasterId']);
					$answer['DatacardStatus'] = 'RESERVED';
				} else {
					$answer['DatacardStatus'] = 'BLOCKED';
				}
				print json_encode($answer);
			break;
			case "chkrelease":
				$us->releaseDatacard($_GET['DisasterId']);
			break;
			default:
			break;
		}
	} elseif (isset($_GET['DisasterId']) && !empty($_GET['DisasterId'])) {
		// Read Datacard Info and return in JSON
		$DisasterId = $_GET['DisasterId'];
		$d = new DIDisaster($us, $DisasterId);
		$e = new DIEEData($us, $DisasterId);
		$dcard = array_merge($d->oField['info'],$e->oField['info']);
		$dcard['DisasterBeginTime[0]'] = substr($dcard['DisasterBeginTime'], 0, 4);
		$dcard['DisasterBeginTime[1]'] = substr($dcard['DisasterBeginTime'], 5, 2);
		$dcard['DisasterBeginTime[2]'] = substr($dcard['DisasterBeginTime'], 8, 2);
		echo json_encode($dcard);
	} elseif (isset($_POST['_CMD'])) {
		// Commands in POST mode: insert, update, search.. datacards.. 
		$us->releaseDatacard($_POST['DisasterId']);
		if ($_POST['_CMD'] == "insertDICard") {
			// Insert New Datacard
			$data = form2disaster($_POST, CMD_NEW);
			$o = new DIDisaster($us, $data['DisasterId']);
			$o->setFromArray($data);
			$o->set('RecordCreation', gmdate('c'));
			$o->set('RecordUpdate', gmdate('c'));
			$i = $o->insert();
			$t->assign("statusmsg", "insertok");
			if (!iserror($i)) {
				// Save EEData ....
				$t->assign("diserial", $data['DisasterSerial']);
				// If Datacard is valid, update EEData Table..
				$eedat = form2eedata($_POST);
				$eedat['DisasterId'] = $data['DisasterId'];
				$o = new DIEEData($us, $eedat['DisasterId']);
				$o->setFromArray($eedat);
				$i = $o->insert();
			} else {
				$t->assign("statusmsg", showerror($i));
			}
		} elseif ($_POST['_CMD'] == "updateDICard") {
			// Update Existing Datacard
			$data = form2disaster($_POST, CMD_UPDATE);
			$o = new DIDisaster($us, $data['DisasterId']);
			$o->setFromArray($data);
			$o->set('RecordUpdate', gmdate('c'));
			$i = $o->update();
			$t->assign("statusmsg", "updateok");
			if (!iserror($i)) {
				// Save EEData ....
				$t->assign("diserial", $data['DisasterSerial']);
				// If Datacard is valid, update EEData Table..
				$eedat = form2eedata($_POST);
				$eedat['DisasterId'] = $data['DisasterId'];
				$o = new DIEEData($us, $eedat['DisasterId']);
				$o->setFromArray($eedat);
				$i = $o->update();
			} else {
				$t->assign("statusmsg", showerror($i));
			}
		}
		$t->assign("dipub", $us->q->getNumDisasterByStatus("PUBLISHED"));
		$t->assign("direa", $us->q->getNumDisasterByStatus("READY"));
		$t->display("cards_result.tpl");
		// End _CMD Block
	} else {
		//if ($us->UserId == '' || $us->getUserRole($RegionId == '')) {}
		// Default view of DesInventar
		$t->assign("usr", $us->UserId);
		$t->assign("regname", $us->q->getDBInfoValue('RegionLabel'));
		$UserRole = $us->getUserRole($RegionId);
		$UserRoleValue = $us->getUserRoleValue($RegionId);
		
		// Validate if user has permission to access database
		$dic = $us->q->queryLabelsFromGroup('DB', $lg);
		switch ($UserRole) {
			case "ADMINREGION":
				$t->assign("showconfig", true);
				$dicrole = $dic['DBRoleAdmin'][0];
			break;
			case "OBSERVER":
				$t->assign("showconfig", true);
				$t->assign("ro", "disabled");
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
		$t->assign("dicrole", $dicrole);
		$t->assign("ctl_effects", true);
		$dis = $us->q->queryLabelsFromGroup('Disaster', $lg);
		$dis = array_merge($dis, $us->q->queryLabelsFromGroup('Geography', $lg));
		$t->assign("dis", $dis);
		$t->assign("rc1", $us->q->queryLabelsFromGroup('Record|1', $lg));
		$t->assign("rc2", $us->q->queryLabelsFromGroup('Record|2', $lg));
		$t->assign("eve", $us->q->queryLabelsFromGroup('Event', $lg));
		$t->assign("cau", $us->q->queryLabelsFromGroup('Cause', $lg));
		$t->assign("ef1", $us->q->queryLabelsFromGroup('Effect|People', $lg));
		$t->assign("ef2", $us->q->queryLabelsFromGroup('Effect|Economic', $lg));
		$t->assign("ef3", $us->q->queryLabelsFromGroup('Effect|Affected', $lg));
		$t->assign("sc3", $us->q->querySecLabelFromGroup('Effect|Affected', $lg));
		$t->assign("ef4", $us->q->queryLabelsFromGroup('Effect|More', $lg));
		$t->assign("sec", $us->q->queryLabelsFromGroup('Sector', $lg));
		//$t->assign("rcsl", $us->q->queryLabelsFromGroup('RecordStatus', $lg));
		$t->assign("dmg", $us->q->queryLabelsFromGroup('MetGuide', $lg));
		
		// Geography Levels
		$GeoLevelList = $us->getGeoLevels();
		$t->assign("GeoLevelList", $GeoLevelList);
		$lev = 0;
		$t->assign("lev", $lev);
		$t->assign("levmax", $us->q->getMaxGeoLev());
		$t->assign("levname", $us->q->loadGeoLevById($lev));
		$t->assign("geol", $us->q->loadGeography($lev));
		$t->assign("evel", $us->q->loadEvents(null, "active", $lg));
		$t->assign("caul", $us->q->loadCauses(null, "active", $lg));
		$t->assign("eefl", $us->q->getEEFieldList("True"));

		$t->assign('RegionId', $RegionId);
		$t->assign('UserRole', $UserRole);
		$t->assign('UserRoleValue', $UserRoleValue);

		$t->display("cards.tpl");
	}
}
</script>
