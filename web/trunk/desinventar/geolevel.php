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
require_once('../include/maps.class.php');

if (isset($_GET['r']) && !empty($_GET['r']))
  $reg = $_GET['r'];
else
  exit();

$d = new Dictionary(VAR_DIR);
$r = new Region($reg);
$q = new Query($reg);
$u = new User('', '', '');

// EDIT REGION: Form to Create and assign regions
if (isset($_GET['levcmd'])) {
		$mod = "lev";
		$cmd = $_GET['levcmd'];
		$dat = array();
		$dat['GeoLevelId'] = isset($_GET['GeoLevelId']) ? $_GET['GeoLevelId'] : -1;
		$dat['GeoLevelName'] = isset($_GET['GeoLevelName']) ? $_GET['GeoLevelName']: '';
		$dat['GeoLevelDesc'] = isset($_GET['GeoLevelDesc']) ? $_GET['GeoLevelDesc']: '';
		$dat['GeoLevelLayerFile'] = isset($_GET['GeoLevelLayerFile']) ? $_GET['GeoLevelLayerFile']: '';
		$dat['GeoLevelLayerCode'] = isset($_GET['GeoLevelLayerCode']) ? $_GET['GeoLevelLayerCode']: '';
		$dat['GeoLevelLayerName'] = isset($_GET['GeoLevelLayerName']) ? $_GET['GeoLevelLayerName']: '';
		switch ($cmd) {
			case "insert":
				$gl = $r->insertGeoLevel($dat['GeoLevelName'], $dat['GeoLevelDesc'], 
						$dat['GeoLevelLayerFile'], $dat['GeoLevelLayerCode'], $dat['GeoLevelLayerName']);
      	if (!iserror($gl)) {
					$t->assign ("ctl_msginslev", true);
					// Create selection map..
					if (!empty($dat['GeoLevelLayerFile']) &&
							!empty($dat['GeoLevelLayerCode']) &&
							!empty($dat['GeoLevelLayerName']))
						$map = new Maps($q, $reg, 0, null, null, null, "", "SELECT");
				}
				else {
					$t->assign ("ctl_errinslev", true);
					$t->assign ("insstatlev", $gl);
					if ($gl == ERR_OBJECT_EXISTS) {
						$t->assign ("ctl_chkname", true);
						$t->assign ("chkname", true);
					}
				}
			break;
			case "update":
				$gl = $r->updateGeoLevel($dat['GeoLevelId'], $dat['GeoLevelName'], $dat['GeoLevelDesc'], 
						$dat['GeoLevelLayerFile'], $dat['GeoLevelLayerCode'], $dat['GeoLevelLayerName']);
				if (!iserror($gl)) {
					$t->assign ("ctl_msgupdlev", true);
					// Create selection map..
					if (!empty($dat['GeoLevelLayerFile']) &&
							!empty($dat['GeoLevelLayerCode']) &&
							!empty($dat['GeoLevelLayerName']))
						$map = new Maps($q, $reg, 0, null, null, null, "", "SELECT");
				}
				else {
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
				if ($q->isvalidObjectName($dat['GeoLevelId'], $dat['GeoLevelName'], DI_GEOLEVEL))
					$t->assign ("chkname", true);
			break;
			case "list":
				$t->assign ("ctl_levlist", true);
				$t->assign ("levl", $q->loadGeoLevels(""));
			break;
			default: break;
		}
}
else {
  $t->assign ("ctl_admingeo", true);
  $t->assign ("ctl_levlist", true);
  $t->assign ("levl", $q->loadGeoLevels(""));
  $lev = 0;
  $t->assign ("lev", $lev);
	$t->assign ("levmax", $q->getMaxGeoLev());
	$t->assign ("levname", $q->loadGeoLevById($lev));
	$urol = $u->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
}
$t->assign ("reg", $reg);
$t->assign ("dic", $d->queryLabelsFromGroup('DB', $lg));
$t->display ("geolevel.tpl");

</script>
