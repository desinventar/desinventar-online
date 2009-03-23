<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');
require_once('../include/query.class.php');
require_once('../include/maps.class.php');
require_once('../include/diobject.class.php');
require_once('../include/digeolevel.class.php');

if (isset($_GET['r']) && !empty($_GET['r'])) {
	$reg = $_GET['r'];
	$us->open($reg);
} else {
	$reg = $us->sRegionId;
}

if (empty($reg) || ($reg == '')) {
	exit();
}

$r = new Region($reg);
$q = new Query($reg);

$mod = 'lev';
$cmd = '';
if (isset($_GET['levcmd'])) {
	$cmd = $_GET['levcmd'];
}
if (!empty($cmd)) {
	$mod = "lev";
	$dat = array();
	$dat['GeoLevelId'] = isset($_GET['GeoLevelId']) ? $_GET['GeoLevelId'] : -1;
	$dat['GeoLevelName'] = isset($_GET['GeoLevelName']) ? $_GET['GeoLevelName']: '';
	$dat['GeoLevelDesc'] = isset($_GET['GeoLevelDesc']) ? $_GET['GeoLevelDesc']: '';
	$dat['GeoLevelLayerFile'] = isset($_GET['GeoLevelLayerFile']) ? $_GET['GeoLevelLayerFile']: '';
	$dat['GeoLevelLayerCode'] = isset($_GET['GeoLevelLayerCode']) ? $_GET['GeoLevelLayerCode']: '';
	$dat['GeoLevelLayerName'] = isset($_GET['GeoLevelLayerName']) ? $_GET['GeoLevelLayerName']: '';
	
	switch ($cmd) {
	case "insert":
		$o = new DIGeoLevel($us);
		// Update with data from FORM
		$o->setFromArray($dat);
		// Set primary key values
		$o->set('GeoLevelId', $o->getMaxGeoLevel()+1);
		// Save to database
		$gl = $o->insert();
		if (!iserror($gl)) {
			$t->assign ("ctl_msginslev", true);
			// Create selection map..
			if (!empty($dat['GeoLevelLayerFile']) &&
			    !empty($dat['GeoLevelLayerCode']) &&
			    !empty($dat['GeoLevelLayerName'])) {
			    $map = new Maps($q, $reg, 0, null, null, null, "", "SELECT");
			}
		} else {
			$t->assign ("ctl_errinslev", true);
			$t->assign ("insstatlev", $gl);
			if ($gl == ERR_OBJECT_EXISTS) {
				$t->assign ("ctl_chkname", true);
				$t->assign ("chkname", true);
			} //if
		} //else
		break;
	case "update":
		$o = new DIGeoLevel($us);
		// Set primary key values
		$o->set('GeoLevelId', $dat['GeoLevelId']);
		$o->load();
		// Update with data from FORM
		$o->setFromArray($dat);
		// Save to database
		$gl = $o->update();
		if (!iserror($gl)) {
			$t->assign ("ctl_msgupdlev", true);
			// Create selection map..
			if (!empty($dat['GeoLevelLayerFile']) &&
			    !empty($dat['GeoLevelLayerCode']) &&
			    !empty($dat['GeoLevelLayerName']))
			    	$map = new Maps($q, $reg, 0, null, null, null, "", "SELECT");
		} else {
			$t->assign ("ctl_errupdlev", true);
			$t->assign ("updstatlev", $gl);
			if ($gl == ERR_OBJECT_EXISTS) {
				$t->assign ("ctl_chkname", true);
				$t->assign ("chkname", true);
			}
		}
		break;
	case "chkname":
		$t->assign ("ctl_chkname", true);
		if ($q->isvalidObjectName($dat['GeoLevelId'], 
		                          $dat['GeoLevelName'], DI_GEOLEVEL)) {
			$t->assign ("chkname", true);
		}
		break;
	case "list":
		$t->assign ("ctl_levlist", true);
		$t->assign ("levl", $q->loadGeoLevels(""));
		break;
	default:
		break;
	} //switch
} else {
	$t->assign ("ctl_admingeo", true);
	$t->assign ("ctl_levlist", true);
	$t->assign ("levl", $q->loadGeoLevels(""));
	$lev = 0;
	$t->assign ("lev", $lev);
	$t->assign ("levmax", $q->getMaxGeoLev());
	$t->assign ("levname", $q->loadGeoLevById($lev));
	$urol = $us->getUserRole($reg);
	if ($urol == "OBSERVER") {
		$t->assign ("ro", "disabled");
	}
}

$t->assign ("reg", $reg);
$t->assign ("dic", $q->queryLabelsFromGroup('DB', $lg));
$t->display ("geolevel.tpl");

</script>
