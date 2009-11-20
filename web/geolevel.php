<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/maps.class.php');
require_once('include/diobject.class.php');
require_once('include/digeolevel.class.php');
require_once('include/digeocarto.class.php');

function form2data($frm, $RegionId) {
	$dat = array();
	$dat['GeoLevelId'] = isset($frm['GeoLevelId']) ? $frm['GeoLevelId'] : -1;
	$dat['GeoLevelName'] = isset($frm['GeoLevelName']) ? $frm['GeoLevelName']: '';
	$dat['GeoLevelDesc'] = isset($frm['GeoLevelDesc']) ? $frm['GeoLevelDesc']: '';
	$cartoFile = $RegionId ."_adm0". $dat['GeoLevelId'];
	$cartoPath = VAR_DIR ."/database/". $RegionId . "/" . $cartoFile;
	// Replace files
	if (isset($_FILES['GeoLevelFileSHP']) && $_FILES['GeoLevelFileSHP']['error'] == UPLOAD_ERR_OK &&
		isset($_FILES['GeoLevelFileSHX']) && $_FILES['GeoLevelFileSHX']['error'] == UPLOAD_ERR_OK &&
		isset($_FILES['GeoLevelFileDBF']) && $_FILES['GeoLevelFileDBF']['error'] == UPLOAD_ERR_OK) {
			move_uploaded_file($_FILES['GeoLevelFileSHP']['tmp_name'], $cartoPath .".shp");
			move_uploaded_file($_FILES['GeoLevelFileSHX']['tmp_name'], $cartoPath .".shx");
			move_uploaded_file($_FILES['GeoLevelFileDBF']['tmp_name'], $cartoPath .".dbf");
	}
	// Check if exists files of map..
	elseif (!file_exists($cartoPath .".shp") || !file_exists($cartoPath .".shx") || !file_exists($cartoPath .".dbf"))
		$cartoFile = "";
	$dat['GeoLevelLayerFile'] = $cartoFile;
	$dat['GeoLevelLayerCode'] = isset($frm['GeoLevelLayerCode']) ? $frm['GeoLevelLayerCode']: '';
	$dat['GeoLevelLayerName'] = isset($frm['GeoLevelLayerName']) ? $frm['GeoLevelLayerName']: '';
	return $dat;
}

$post = $_POST;
$get = $_GET;

if (isset($post['_REG']) && !empty($post['_REG'])) {
	$reg = $post['_REG'];
	$cmd = $post['cmd'];
	$data = form2data($post, $reg);
}
elseif (isset($get['r']) && !empty($get['r'])) {
	$reg = $get['r'];
	$cmd = $get['cmd'];
}
else
	exit();

$us->open($reg);

if (!empty($cmd)) {
	switch ($cmd) {
		case "insert":
			// Create new GeoLevel and GeoCarto Objects
			$o = new DIGeoLevel($us);
			$o->setFromArray($data);
			$levid = $o->getMaxGeoLevel();
			if ($levid > 0)
				$levid ++;
			$o->set('GeoLevelId', $levid);
			$c = new DIGeoCarto($us);
			$c->setFromArray($data);
			$c->set('GeoLevelId', $o->get('GeoLevelId'));
			// Save to database
			$gl = $o->insert();
			$gl = $c->insert();
			if (!iserror($gl)) {
				$t->assign ("ctl_msginslev", true);
				/* Create selection map.. -> disabled method
				if (!empty($data['GeoLevelLayerFile']) && !empty($data['GeoLevelLayerCode']) && !empty($data['GeoLevelLayerName'])) {
				    $map = new Maps($us->q, $reg, 0, null, null, null, "", null, "SELECT");
				}*/
			}
			else {
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
			$c = new DIGeoCarto($us);
			// Set primary key values
			$o->set('GeoLevelId', $data['GeoLevelId']);
			$o->load();
			$c->set('GeoLevelId', $data['GeoLevelId']);
			$c->load();
			// Update with data from FORM
			$o->setFromArray($data);
			$c->setFromArray($data);
			// Save to database
			$gl = $o->update();
			$gl = $c->update();
			if (!iserror($gl)) {
				$t->assign ("ctl_msgupdlev", true);
				/* Create selection map..
				if (!empty($data['GeoLevelLayerFile']) && !empty($data['GeoLevelLayerCode']) && !empty($data['GeoLevelLayerName']))
					$map = new Maps($us->q, $reg, 0, null, null, null, "", "SELECT");*/
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
			if ($us->q->isvalidObjectName($get['GeoLevelId'], $get['GeoLevelName'], DI_GEOLEVEL)) {
				$t->assign ("chkname", true);
			}
		break;
		case "list":
			$t->assign ("ctl_levlist", true);
			$t->assign ("levl", $us->q->loadGeoLevels('', -1, false));
		break;
		default:
		break;
	} //switch
}
else {
	$t->assign ("ctl_admingeo", true);
	$t->assign ("ctl_levlist", true);
	$t->assign ("levl", $us->q->loadGeoLevels('', -1, false));
	/*$lev = 0;
	$t->assign ("lev", $lev);
	$t->assign ("levmax", $us->q->getMaxGeoLev());
	$t->assign ("levname", $us->q->loadGeoLevById($lev));*/
	if ($us->getUserRole($reg) == "OBSERVER")
		$t->assign ("ro", "disabled");
}

$t->assign ("reg", $reg);
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->display ("geolevel.tpl");

</script>
