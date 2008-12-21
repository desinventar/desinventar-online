<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

require_once('../include/loader.php');
require_once('../include/dictionary.class.php');
require_once('../include/query.class.php');
require_once('../include/region.class.php');
require_once('../include/user.class.php');

if (isset($_GET['r']) && !empty($_GET['r']))
  $reg = $_GET['r'];
else
  exit();

$d = new Dictionary(DICT_DIR);
$r = new Region($reg);
$q = new Query($reg);
$u = new User('', '', '');

// EDIT REGION: Form to Create and assign regions
if (isset($_GET['geocmd'])) {
	$mod = "geo";
	$cmd = $_GET['geocmd'];
	// Set Variables to insert or update
	$dat = array();
	$dat['GeographyId'] = isset($_GET['GeographyId']) ? $_GET['GeographyId']: "";
	$dat['GeoParentId'] = isset($_GET['GeoParentId']) ? $_GET['GeoParentId']: "";
	$dat['GeographyLevel'] = isset($_GET['GeographyLevel']) ? $_GET['GeographyLevel']: "";
	$dat['GeographyCode'] = isset($_GET['GeographyCode']) ? $_GET['GeographyCode']:"";
	$dat['GeographyName'] = isset($_GET['GeographyName']) ? $_GET['GeographyName']:"";
	if (isset($_GET['GeographyActive']) && $_GET['GeographyActive'] == "on")
		$dat['GeographyActive'] = true;
	else
		$dat['GeographyActive'] = false;
	switch ($cmd) {
		case "insert":
				$gi = $r->insertGeoItem($dat['GeoParentId'], $dat['GeographyCode'],
												$dat['GeographyName'], $dat['GeographyActive']);
				if (!iserror($gi))
					$t->assign ("ctl_msginsgeo", true);
				else {
					$t->assign ("ctl_errinsgeo", true);
					$t->assign ("insstatgeo", $gi);
				}
		break;
		case "update":
				$gi = $r->updateGeoItem($dat['GeographyId'], $dat['GeographyCode'],
												$dat['GeographyName'], $dat['GeographyActive']);
				if (!iserror($gi))
					$t->assign ("ctl_msgupdgeo", true);
				else {
					$t->assign ("ctl_errupdgeo", true);
					$t->assign ("updstatgeo", $gi);
				}
		break;
		case "list":
				$lev = $q->getNextLev($_GET['GeographyId']);
			  $t->assign ("lev", $lev);
				$t->assign ("levmax", $q->getMaxGeoLev());
				$t->assign ("levname", $q->loadGeoLevById($lev));
				$t->assign ("geol", $q->loadGeoChilds($_GET['GeographyId']));
				$t->assign ("ctl_geolist", true);
		break;
		case "chkcode":
				$t->assign ("ctl_chkcode", true);
				if ($q->isvalidObjectName($_GET['GeographyId'], $_GET['GeographyCode'], DI_GEOGRAPHY))
					$t->assign ("chkcode", true);
		break;
		case "chkstatus":
				$t->assign ("ctl_chkstatus", true);
				if ($q->isvalidObjectToInactivate($_GET['GeographyId'], DI_GEOGRAPHY))
					$t->assign ("chkstatus", true);
		break;
		default: break;
	}
}
else {
  $t->assign ("ctl_admingeo", true);
  $lev = 0;
  $t->assign ("lev", $lev);
	$t->assign ("levmax", $q->getMaxGeoLev());
	$t->assign ("levname", $q->loadGeoLevById($lev));
  $t->assign ("geol", $q->loadGeography($lev));
  $t->assign ("ctl_geolist", true);
	$urol = $u->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
}
$t->assign ("reg", $reg);
$t->assign ("dic", $d->queryLabelsFromGroup('DB', $lg));
$t->display ("geography.tpl");

</script>
