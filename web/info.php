<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1998-2009 Corporacion OSSO
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
	return $dat;
}

$post = $_POST;
$get = $_GET;

$RegionId = getParameter('_REG', getParameter('r', ''));
$infocmd = getParameter('_infocmd', NULL);

if ($RegionId == '') {
	exit();
}
$us->open($RegionId);

if (isset($infocmd)) {
	// EDIT REGION: Form to Create and assign regions
	$ifo = 0;
	$data = form2data($post);
	$r = new DIRegion($us, $data['RegionId']);
	$LangIsoCode = $r->get('LangIsoCode');
	$r->setFromArray($data);
	// Set Translated Info
	$r->set('InfoCredits', $data['InfoCredits'], $LangIsoCode);
	$ifo = $r->update();
	if (!iserror($ifo)) {
		$t->assign ("ctl_msgupdinfo", true);
		if (isset($_FILES['logofile']) && $_FILES['logofile']['error'] == UPLOAD_ERR_OK)
			move_uploaded_file($_FILES['logofile']['tmp_name'], VAR_DIR ."/database/". $RegionId . "/logo.png");
	}
	else {
		$t->assign ("ctl_errupdinfo", true);
		$t->assign ("updstatinfo", $ifo);
	}
} elseif (isset($get['rolecmd'])) {
	// EDIT ROLE: Form to Create and assign role
	$cmd = $get['rolecmd'];
	if (($cmd == "insert") || ($cmd == "update")) {
		// Set Role in RegionAuth
		$rol = $us->setUserRole($get['UserId'], $RegionId, $get['AuthAuxValue']);
		if (!iserror($rol)) 
			$t->assign ("ctl_msgupdrole", true);
		else {
			$t->assign ("ctl_errupdrole", true);
			$t->assign ("updstatrole", showerror($rol));
		}
	}
	// reload list from local SQLITE
	else if ($cmd == "list") {
		$t->assign ("rol", $us->getRegionRoleList($RegionId));
		$t->assign ("ctl_rollist", true);
	}
} elseif (isset($get['logcmd'])) {
	// EDIT REGION: Form to Create and assign regions
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
} else {
	// DISPLAY REGION INFO
	if ($urol == "OBSERVER") {
		$t->assign ("ro", "disabled");
	}
	$lang[0] = ''; //$_SESSION['lang'];
	$lang[1] = 'eng';
	//$inf = $us->q->getDBInfo();
	$r = new DIRegion($us, $RegionId);
	foreach ($lang as $lng) {
		$info[$lng]['InfoCredits'] 		= array($r->get('InfoCredits'    , $lng), "TEXT");
		$info[$lng]['InfoGeneral'] 		= array($r->get('InfoGeneral'    , $lng), "TEXT");
		$info[$lng]['InfoSources'] 		= array($r->get('InfoSources'    , $lng), "TEXT");
		$info[$lng]['InfoSynopsis'] 	= array($r->get('InfoSynopsis'   , $lng), "TEXT");
		$info[$lng]['InfoObservation']	= array($r->get('InfoObservation', $lng), "TEXT");
		$info[$lng]['InfoGeography']	= array($r->get('InfoGeography'  , $lng), "TEXT");
		$info[$lng]['InfoCartography']	= array($r->get('InfoCartography', $lng), "TEXT");
		$info[$lng]['InfoAdminURL']		= array($r->get('InfoAdminURL'   , $lng), "VARCHAR");
	}
	$t->assign ("info", $info);
	$sett['GeoLimitMinX']	= array($r->get('GeoLimitMinX'), "NUMBER");
	$sett['GeoLimitMinY']	= array($r->get('GeoLimitMinY'), "NUMBER");
	$sett['GeoLimitMaxX']	= array($r->get('GeoLimitMaxX'), "NUMBER");
	$sett['GeoLimitMaxY']	= array($r->get('GeoLimitMaxY'), "NUMBER");
	$sett['PeriodBeginDate']= array($r->get('PeriodBeginDate'), "DATE");
	$sett['PeriodEndDate']	= array($r->get('PeriodEndDate'), "DATE");
	$t->assign ("sett", $sett);
	$urol = $us->getUserRole($RegionId);
	//$t->assign ("usr", $us->getUserFullName(''));
	$t->assign ("usr", $us->getUsersList(''));
	$t->assign ("rol", $us->getRegionRoleList($RegionId));
	$t->assign ("log", $us->q->getRegLogList());
	$t->assign ("ctl_adminreg", true);
	$t->assign ("ctl_rollist", true);
	$t->assign ("ctl_loglist", true);
}
$t->assign ("reg", $RegionId);
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->assign ("usern", $us->UserId);
$t->display ("info.tpl");

</script>
