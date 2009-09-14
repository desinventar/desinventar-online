<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 ***********************************************/

require_once('include/loader.php');

function getRAPermList($lst) {
	$dat = array();
	foreach ($lst as $k=>$v) {
		if ($v=="NONE" || $v=="USER" || $v=="OBSERVER" || $v=="SUPERVISOR") {
			$dat[$k] = $v;
		}
	}
	return $dat;
}

$reg = $us->sRegionId;
if (empty($reg)) {
	exit();
}
$get = $_GET;

// EDIT REGION: Form to Create and assign regions
if (isset($get['infocmd'])) {
	$mod = "info";
	$cmd = $get['infocmd'];
	if (isset($get['OptionOutOfPeriod']) && $get['OptionOutOfPeriod'] == "on")
		$optout = true;
	else
		$optout = false;
	
	$ifo = 0;
	// Replace this call with a new class (2009-07-06) (jhcaiced)
	/*
	$ifo = $r->updateDBInfo($reg, $get['RegionLabel'], $get['RegionDesc'], $get['RegionDescEN'], 
							$get['RegionLangCode'], $get['PeriodBeginDate'], $get['PeriodEndDate'], $optout, 
							$get['GeoLimitMinX'], $get['GeoLimitMinY'], $get['GeoLimitMaxX'], $get['GeoLimitMaxY']);
	*/					
	if (!iserror($ifo)) 
		$t->assign ("ctl_msgupdinfo", true);
	else {
		$t->assign ("ctl_errupdinfo", true);
		$t->assign ("updstatinfo", $ifo);
	}
}
// EDIT ROLE: Form to Create and assign role
elseif (isset($get['rolecmd'])) {
	$mod = "role";
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
	$mod = "log";
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
	} elseif ($cmd == "update") {
		$stat = 1;
		// 2009-07-06 (jhcaiced) Replace this with another class...
		//$stat = $r->updateRegLog($get['DBLogDate'], $get['DBLogType'], $get['DBLogNotes']);
		if (!iserror($stat)) 
			$t->assign ("ctl_msgupdlog", true);
		else {
			$t->assign ("ctl_errupdlog", true);
			$t->assign ("updstatlog", showerror($stat));
		}
	} elseif ($cmd == "list") {
		// reload list from local SQLITE
		if ($mod == "log") {
			$t->assign ("log", $us->q->getRegLogList());
			$t->assign ("ctl_loglist", true);
		}
	}
}
else {
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$lang = $_SESSION['lang'];
	$inf = $us->q->getDBInfo();
	$info['InfoCredits'] 	= array($inf['InfoCredits|'. $lang], "TEXT");
	$info['InfoGeneral'] 	= array($inf['InfoGeneral|'. $lang], "TEXT");
	$info['InfoSources'] 	= array($inf['InfoSources|'. $lang], "TEXT");
	$info['InfoSynopsis'] 	= array($inf['InfoSynopsis|'. $lang], "TEXT");
	$info['InfoObservation']= array($inf['InfoObservation|'. $lang], "TEXT");
	$info['InfoGeography']	= array($inf['InfoGeography|'. $lang], "TEXT");
	$info['InfoCartography']= array($inf['InfoCartography|'. $lang], "TEXT");
	$info['InfoAdminURL']	= array($inf['InfoAdminURL|'. $lang], "VARCHAR");
	$info['GeoLimitMinX']	= array($inf['GeoLimitMinX|'. $lang], "NUMBER");
	$info['GeoLimitMinY']	= array($inf['GeoLimitMinY|'. $lang], "NUMBER");
	$info['GeoLimitMaxX']	= array($inf['GeoLimitMaxX|'. $lang], "NUMBER");
	$info['GeoLimitMaxY']	= array($inf['GeoLimitMaxY|'. $lang], "NUMBER");
	$info['PeriodBeginDate']= array($inf['PeriodBeginDate|'. $lang], "NUMBER");
	$info['PeriodEndDate']	= array($inf['PeriodEndDate|'. $lang], "NUMBER");
	$t->assign ("info", $info);
	$urol = $us->getUserRole($reg);
	$t->assign ("usr", $us->getUserFullName(''));
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
