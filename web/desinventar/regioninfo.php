<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

require_once('../include/loader.php');
require_once('../include/query.class.php');
require_once('../include/region.class.php');
require_once('../include/user.class.php');
require_once('../include/dictionary.class.php');

if (isset($_GET['r']) && !empty($_GET['r']))
  $reg = $_GET['r'];
else
  exit();

function getRAPermList($lst) {
	$dat = array();
	foreach ($lst as $k=>$v)
		if ($v=="NONE" || $v=="USER" || $v=="OBSERVER" || $v=="SUPERVISOR")
			$dat[$k] = $v;
	return $dat;
}

$d = new Dictionary(VAR_DIR);
$r = new Region($reg);
$q = new Query($reg);
$u = new User('', '', '');

if (isset($_GET))
	$get = $_GET;

// EDIT REGION: Form to Create and assign regions
if (isset($get['infocmd'])) {
	$mod = "info";
	$cmd = $get['infocmd'];
	if (isset($get['OptionOutOfPeriod']) && $get['OptionOutOfPeriod'] == "on")
		$optout = true;
	else
		$optout = false;
	$ifo = $r->updateDBInfo($reg, $get['RegionLabel'], $get['RegionDesc'], $get['RegionDescEN'], 
							$get['RegionLangCode'], $get['PeriodBeginDate'], $get['PeriodEndDate'], $optout, 
							$get['GeoLimitMinX'], $get['GeoLimitMinY'], $get['GeoLimitMaxX'], $get['GeoLimitMaxY']);
	if (!iserror($ifo)) 
		$t->assign ("ctl_msgupdinfo", true);
	else {
		$t->assign ("ctl_errupdinfo", true);
		$t->assign ("updstatinfo", $ifo);
	}
}
else {
	$info = $q->getDBInfo();
	$inf[0] = $info['RegionDesc'];
	$inf[1] = $info['RegionDescEN'];
	$inf[2] = $info['PeriodBeginDate'];
	$inf[3] = $info['PeriodEndDate'];
	$inf[4] = $info['OptionAdminURL'];
	$inf[5] = $info['RegionLabel'];
	$inf[6] = $info['GeoLimitMinX'];
	$inf[7] = $info['GeoLimitMinY'];
	$inf[8] = $info['GeoLimitMaxX'];
	$inf[9] = $info['GeoLimitMaxY'];
	$t->assign ("info", $inf);
	$urol = $u->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_adminreg", true);
	$t->assign ("usr", $u->getUsername(''));
}
$t->assign ("reg", $reg);
$t->assign ("dic", $d->queryLabelsFromGroup('DB', $lg));
$t->display ("regioninfo.tpl");

</script>
