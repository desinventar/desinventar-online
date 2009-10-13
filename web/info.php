<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 ***********************************************/

require_once('include/loader.php');
require_once('include/diobject.class.php');
require_once('include/diregion.class.php');

function getRAPermList($lst) {
	$dat = array();
	foreach ($lst as $k=>$v) {
		if ($v=="NONE" || $v=="USER" || $v=="OBSERVER" || $v=="SUPERVISOR")
			$dat[$k] = $v;
	}
	return $dat;
}

function form2data($form) {
	$dat = array();
	foreach ($form as $key=>$value) {
		$k = explode('_', $key);
		if (count($k) == 1)
			$dat[$key] = $value;
		elseif (count($k) == 2) {
			if (empty($k[1]))
				$dat[$k[0]] = $value;
			elseif (empty($k[0]))
				$dat[$k[1]] = $value;
		}
	}
	//print_r($dat);
	return $dat;
}

$post = $_POST;
$get = $_GET;

if (isset($post['_REG']) && !empty($post['_REG'])) {
	$reg = $post['_REG'];
	$infocmd = $post['_infocmd'];
}
elseif (isset($get['r']) && !empty($get['r'])) {
	$reg = $get['r'];
}
else
	exit();

$us->open($reg);

// EDIT REGION: Form to Create and assign regions
if (isset($infocmd)) {
	$ifo = 0;
	$data = $post; //form2data($post);
	$r = new DIRegion($us, $data['RegionId']);
	$r->setFromArray($data);
	$ifo = $r->update();
	if (!iserror($ifo)) {
		$t->assign ("ctl_msgupdinfo", true);
		if (isset($_FILES['logofile']) && $_FILES['logofile']['error'] == UPLOAD_ERR_OK)
			move_uploaded_file($_FILES['logofile']['tmp_name'], VAR_DIR ."/database/". $reg . "/logo.png");
	}
	else {
		$t->assign ("ctl_errupdinfo", true);
		$t->assign ("updstatinfo", $ifo);
	}
}
// EDIT ROLE: Form to Create and assign role
elseif (isset($get['rolecmd'])) {
	$cmd = $get['rolecmd'];
	if (($cmd == "insert") || ($cmd == "update")) {
		// Set Role in RegionAuth
		$rol = $us->setUserRole($get['UserId'], $reg, $get['AuthAuxValue']);
		if (!iserror($rol)) 
			$t->assign ("ctl_msgupdrole", true);
		else {
			$t->assign ("ctl_errupdrole", true);
			$t->assign ("updstatrole", showerror($rol));
		}
	}
	// reload list from local SQLITE
	else if ($cmd == "list") {
		$t->assign ("rol", $us->getRegionRoleList($reg));
		$t->assign ("ctl_rollist", true);
	}
}
// EDIT REGION: Form to Create and assign regions
elseif (isset($get['logcmd'])) {
	$cmd = $get['logcmd']; 
	if ($cmd == "insert") {
		$stat = 1;
		//2009-07-06 (jhcaiced) Replace this with another class...
		//$stat = $r->insertRegLog($get['DBLogType'], $get['DBLogNotes']);
		if (!iserror($stat)) 
			$t->assign ("ctl_msginslog", true);
		else {
			$t->assign ("ctl_errinslog", true);
			$t->assign ("insstatlog", $stat);
		}
	}
	elseif ($cmd == "update") {
		$stat = 1;
		// 2009-07-06 (jhcaiced) Replace this with another class...
		//$stat = $r->updateRegLog($get['DBLogDate'], $get['DBLogType'], $get['DBLogNotes']);
		if (!iserror($stat)) 
			$t->assign ("ctl_msgupdlog", true);
		else {
			$t->assign ("ctl_errupdlog", true);
			$t->assign ("updstatlog", showerror($stat));
		}
	}
	elseif ($cmd == "list") {
		// reload list from local SQLITE
		$t->assign ("log", $us->q->getRegLogList());
		$t->assign ("ctl_loglist", true);
	}
}
else {
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$lang[0] = ''; //$_SESSION['lang'];
	$lang[1] = 'eng';
	$inf = $us->q->getDBInfo();
	foreach ($lang as $lng) {
		$info[$lng]['InfoCredits'] 		= array($inf['InfoCredits|'. $lng], "TEXT");
		$info[$lng]['InfoGeneral'] 		= array($inf['InfoGeneral|'. $lng], "TEXT");
		$info[$lng]['InfoSources'] 		= array($inf['InfoSources|'. $lng], "TEXT");
		$info[$lng]['InfoSynopsis'] 	= array($inf['InfoSynopsis|'. $lng], "TEXT");
		$info[$lng]['InfoObservation']	= array($inf['InfoObservation|'. $lng], "TEXT");
		$info[$lng]['InfoGeography']	= array($inf['InfoGeography|'. $lng], "TEXT");
		$info[$lng]['InfoCartography']	= array($inf['InfoCartography|'. $lng], "TEXT");
		$info[$lng]['InfoAdminURL']		= array($inf['InfoAdminURL|'. $lng], "VARCHAR");
	}
	$t->assign ("info", $info);
	$sett['GeoLimitMinX']	= array($inf['GeoLimitMinX|'], "NUMBER");
	$sett['GeoLimitMinY']	= array($inf['GeoLimitMinY|'], "NUMBER");
	$sett['GeoLimitMaxX']	= array($inf['GeoLimitMaxX|'], "NUMBER");
	$sett['GeoLimitMaxY']	= array($inf['GeoLimitMaxY|'], "NUMBER");
	$sett['PeriodBeginDate']= array($inf['PeriodBeginDate|'], "DATE");
	$sett['PeriodEndDate']	= array($inf['PeriodEndDate|'], "DATE");
	$t->assign ("sett", $sett);
	$urol = $us->getUserRole($reg);
	//$t->assign ("usr", $us->getUserFullName(''));
	$t->assign ("usr", $us->getUsersList(''));
	$t->assign ("rol", $us->getRegionRoleList($reg));
	$t->assign ("log", $us->q->getRegLogList());
	$t->assign ("ctl_adminreg", true);
	$t->assign ("ctl_rollist", true);
	$t->assign ("ctl_loglist", true);
}
$t->assign ("reg", $reg);
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->assign ("usern", $us->UserId);
$t->display ("info.tpl");

</script>
