<script language="php">
/*
 **********************************************
 DesInventar8 - http://www.desinventar.org  
 (c) 1998-2009 Corporacion OSSO
 **********************************************
*/

require_once('include/loader.php');
require_once('include/diregion.class.php');
require_once('include/dievent.class.php');
require_once('include/dicause.class.php');
require_once('include/digeolevel.class.php');
require_once('include/digeocarto.class.php');
require_once('include/digeography.class.php');

function form2region ($val) {
	$dat = array();
	$dat['RegionLabel']		= $val['RegionLabel'];
	$dat['LangIsoCode']		= $val['LangIsoCode'];
	$dat['CountryIso'] 		= $val['CountryIso'];
	if (empty($val['RegionId']))
		$dat['RegionId']	= DIRegion::buildRegionId($dat['CountryIso'], $dat['RegionLabel']);
	else
		$dat['RegionId']	= $val['RegionId'];
	if (isset($val['RegionActive']) && $val['RegionActive'] == "on")
		$dat['RegionStatus'] |= CONST_REGIONACTIVE;
	else
		$dat['RegionStatus'] &= ~CONST_REGIONACTIVE;
	if (isset($val['RegionPublic']) && $val['RegionPublic'] == "on")
		$dat['RegionStatus'] |= CONST_REGIONPUBLIC;
	else
		$dat['RegionStatus'] &= ~CONST_REGIONPUBLIC;
	return $dat;
}

if (isset($_POST['cmd']) && !empty($_POST['cmd']))
	$cmd = $_POST['cmd'];
elseif (isset($_GET['cmd']) && !empty($_GET['cmd']))
	$cmd = $_GET['cmd'];

switch ($cmd) {
	case "adminreg":
		// ADMINREG: Form to Create and assign regions
		$t->assign ("cntl", $us->q->getCountryList());
		$t->assign ("usr", $us->getUsersList(''));
		$t->assign ("lglst", $us->q->loadLanguages(1));
		$t->assign ("ctl_adminreg", true);
		$t->assign ("regpa", $us->q->getRegionAdminList());
		$t->assign ("ctl_reglist", true);
	break;
	case "list":
		// ADMINREG: reload list from local SQLITE
		$t->assign ("regpa", $us->q->getRegionAdminList());
		$t->assign ("ctl_reglist", true);
	break;
	case "createRegionsFromDBDir":
		DIRegion::rebuildRegionListFromDirectory($us);
		$t->assign ("regpa", $us->q->getRegionAdminList());
		$t->assign ("ctl_reglist", true);
	break;
	case "createRegionFromZip":
		if (isset($_FILES['filereg']) && $_FILES['filereg']['error'] == UPLOAD_ERR_OK) {
			$zip = new ZipArchive;
			if ($zip->open($_FILES['filereg']['tmp_name'])) {
				//$myreg = $zip->statIndex(0);
				$myreg = $_FILES['filereg']['name'];
				$regid = substr($myreg, 0, -4);
				if (!empty($regid)) {
					if (empty($_POST['RegionLabel']))
						$myreg = $regid;
					else
						$myreg = DIRegion::buildRegionId(substr($regid, 0, 3), $_POST['RegionLabel']);
					mkdir(VAR_DIR . "/database/". $myreg, 0755);
					$zip->extractTo(VAR_DIR . "/database/". $myreg);
					$result = DIRegion::createRegionEntryFromDir($us, $myreg, $_POST['RegionLabel']);
					if (!iserror($result)) {
						$t->assign ("ctl_successfromzip", true);
						$t->assign ("cfunct", 'insert');
						$t->assign ("csetrole", true);
					}
				}
				$zip->close();
			}
		}
		$t->assign ("ctl_admregmess", true);
	break;
	default:
		// ADMINREG: insert or update region
		if (($cmd == "insert") || ($cmd == "update")) {
			$data = form2region($_GET);
			$r = new DIRegion($us, $data['RegionId']);
			$r->setFromArray($data);
			$stat = ERR_NO_DATABASE;
			$t->assign ("ctl_admregmess", true);
			$stat = 0;
			if ($cmd == "insert") {
				$stat = $r->createRegionDB();
				$t->assign ("cfunct", 'insert');
			}
			elseif ($cmd == "update") {
				$stat = $r->update();
				$t->assign ("cfunct", 'update');
			}
			$t->assign ("regid", $data['RegionId']);
			// Set Role ADMINREGION in RegionAuth: master for this region
			if (!iserror($stat))
				$rol = $us->setUserRole($_GET['RegionUserAdmin'], $data['RegionId'], "ADMINREGION");
			else {
				$t->assign ("cfunct", '');
				$rol = $stat;
			}
			if (!iserror($rol))
				$t->assign ("csetrole", true);
			else
				$t->assign ("errsetrole", $rol);
		}
	break;
} //switch
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->display ("region.tpl");

</script>