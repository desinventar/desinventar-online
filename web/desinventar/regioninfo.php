<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 ***********************************************/

require_once('../include/loader.php');

$reg = $us->sRegionId;
if (empty($reg)) {
	exit();
}
if (isset($_GET)) {
	$get = $_GET;
}

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
} else {
	$info = $us->q->getDBInfo();
	$inf[0] = $info['RegionDesc'];
	$inf[1] = $info['RegionDescEN'];
	$inf[2] = $info['PeriodBeginDate'];
	$inf[3] = $info['PeriodEndDate'];
	$inf[4] = $info['InfoAdminURL'];
	$inf[5] = $info['RegionLabel'];
	$inf[6] = $info['GeoLimitMinX'];
	$inf[7] = $info['GeoLimitMinY'];
	$inf[8] = $info['GeoLimitMaxX'];
	$inf[9] = $info['GeoLimitMaxY'];
	$t->assign ("info", $inf);
	$urol = $us->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_adminreg", true);
	$t->assign ("usr", $us->getUserFullName(''));
}
$t->assign ("reg", $reg);
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->display ("regioninfo.tpl");

function getRAPermList($lst) {
	$dat = array();
	foreach ($lst as $k=>$v) {
		if ($v=="NONE" || $v=="USER" || $v=="OBSERVER" || $v=="SUPERVISOR") {
			$dat[$k] = $v;
		} //if
	} //foreach
	return $dat;
}

</script>
