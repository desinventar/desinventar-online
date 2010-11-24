<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/digeography.class.php');

$get = $_GET;

$RegionId = getParameter('RegionId', getParameter('r',''));
if ($RegionId == '') {
	exit();
}
$us->open($RegionId);
// EDIT REGION: Form to Create and assign regions
$cmd = getParameter('geocmd', getParameter('cmd', ''));

// Set Variables to insert or update
$dat = array();
$dat['GeographyId'] = isset($get['GeographyId']) ? $get['GeographyId']: '';
$dat['GeoParentId'] = isset($get['GeoParentId']) ? $get['GeoParentId']: '';
$dat['GeographyLevel'] = isset($get['GeographyLevel']) ? $get['GeographyLevel']: '';
$dat['GeographyCode'] = isset($get['GeographyCode']) ? $get['GeographyCode']:'';
$dat['GeographyName'] = isset($get['GeographyName']) ? $get['GeographyName']:'';
if (isset($get['GeographyActive']) && $get['GeographyActive'] == 'on') {
	$dat['GeographyActive'] = 1;
} else {
	$dat['GeographyActive'] = 0;
}
switch ($cmd) {
	case 'insert':
		$o = new DIGeography($us);
		$o->setFromArray($get);
		$o->setGeographyId($get['GeoParentId']);
		$i = $o->insert();
		if ($i > 0)
			$t->assign('ctl_msginsgeo', true);
		else {
			$t->assign('ctl_errinsgeo', true);
			$t->assign('insstatgeo', $i);
		}
	break;
	case 'update':
		$o = new DIGeography($us, $get['GeographyId']);
		$o->load();
		$o->setFromArray($get);
		$i = $o->update();
		if ($i > 0)
			$t->assign('ctl_msgupdgeo', true);
		else {
			$t->assign('ctl_errupdgeo', true);
			$t->assign('updstatgeo', $i);
		}
	break;
	case 'list':
		$lev = $us->q->getNextLev($get['GeographyId']);
		$t->assign('lev', $lev);
		$t->assign('levmax', $us->q->getMaxGeoLev());
		$t->assign('levname', $us->q->loadGeoLevById($lev));
		$t->assign('geol', $us->q->loadGeoChilds($get['GeographyId']));
		$t->assign('ctl_geolist', true);
	break;
	case 'chkcode':
		$t->assign('ctl_chkcode', true);
		if ($us->q->isvalidObjectName($get['GeographyId'], $get['GeographyCode'], DI_GEOGRAPHY))
			$t->assign('chkcode', true);
	break;
	case 'chkstatus':
		$t->assign('ctl_chkstatus', true);
		if ($us->q->isvalidObjectToInactivate($get['GeographyId'], DI_GEOGRAPHY))
			$t->assign('chkstatus', true);
	break;
	case 'cmdDBInfoGeography':
		$t->assign('ctl_admingeo', true);
		$lev = 0;
		$levmax = $us->q->getMaxGeoLev();
		$levname = $us->q->loadGeoLevById($lev);
		$geol = $us->q->loadGeography($lev);
		fb($lev);
		fb($levmax);
		fb($levname);
		fb($geol);
		$t->assign('lev', $lev);
		$t->assign('levmax', $levmax);
		$t->assign('levname', $levname);
		$t->assign('geol', $geol);
		$t->assign('ctl_geolist', true);
		$urol = $us->getUserRole($reg);
		if ($urol == 'OBSERVER') {
			$t->assign('ro', 'disabled');
		}
	break;
	default: 
	break;
} // switch
$t->assign('reg', $reg);
$t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
$t->display('geography.tpl');

</script>
