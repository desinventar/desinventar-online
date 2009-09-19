<script language="php">
/*
 **********************************************
 DesInventar8 - http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 **********************************************
*/

require_once('include/loader.php');
require_once('include/region.class.php');
require_once('include/diregion.class.php');
$t->config_dir = 'include';

function form2region ($val) {
	$dat = array();
	if (isset($val['RegionId']))
		$dat['RegionId'] = $val['RegionId'];
	elseif (isset($val['RegionId2'])) 
		$dat['RegionId'] = $val['RegionId2'];
	elseif (isset($val['RegionUUID2']))
		$dat['RegionId'] = $val['RegionUUID2'];
	
	$dat['RegionLabel' ] = $val['RegionLabel'];
	$dat['LangIsoCode' ] = $val['RegionLangCode'];
	$dat['CountryIso'  ] = $val['CountryIsoCode'];
	$dat['RegionStatus'] = 0;
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

// REGIONS: Show databases for selected Country 
if (isset($_GET['c']) && (strlen($_GET['c']) > 0)) {
	$t->assign ("ctl_regions", true);
	$q = new Query();
	$t->assign ("cnt", $us->q->getCountryByCode($_GET['c']));
	$dbs = $us->q->getRegionList($_GET['c'], "ACTIVE");
	$t->assign ("ctl_available", true);
	$t->assign ("dbs", $dbs);
}
// REGIONINFO: Show Information about Region
elseif (isset($_GET['r']) && !empty($_GET['r']) && file_exists($us->q->getDBFile($_GET['r']))) {
	// set region
	$sRegionId = $_GET['r'];
	if (isset($_GET['view'])) {
		if ($_GET['view'] == "info")
			$t->assign ("ctl_reginfo", true);
		elseif ($_GET['view'] == "logo") {
			header("Content-type: Image/png");
			$murl = VAR_DIR . "/database/". $sRegionId . "/logo.png";
			if (!file_exists($murl))
				$murl = "images/di_logo.png";
			readfile($murl);
			exit();
		}
	}
	// Get Information to Region
	$q = new Query($sRegionId);
	$t->assign ("period", $q->getDateRange());
	$t->assign ("dtotal", $q->getNumDisasterByStatus("PUBLISHED"));
	$t->assign ("lstupd", $q->getLastUpdate());
	// Enable access only to users with a valid role in this region
	$role = $us->getUserRole($sRegionId);
	if ($role=="OBSERVER" || $role=="USER" || $role=="SUPERVISOR" || $role=="ADMINREGION") {
		$t->assign ("ctl_showdimod", true);
		$t->assign ("ctl_showdcmod", true);
	}
	$t->assign ("role", $role);
	$t->assign ("userid", $us->UserId);
	// Show active or public regions only
	$rf = $q->getRegionFieldByID($sRegionId, "RegionStatus");
	if ($rf[$sRegionId] & CONST_REGIONPUBLIC)
		$t->assign ("ctl_showdcmod", true);
	$t->assign ("ctl_showreg", true);
	$reg = $q->getDBInfo();
	$t->assign ("regname", $reg['RegionLabel|']);
	//$t->assign ("log", $q->getRegLogList());
	$lang = $reg['LangIsoCode|'];
	$t->assign ("lang", $lang);
	$t->assign ("reg", $sRegionId);
	if ($lang == $_SESSION['lang'])
		$mylg = $lang;
	else
		$mylg = "eng";
	$info['InfoSynopsis'] = $reg['InfoSynopsis|'. $mylg];
	$info['InfoCredits'] = $reg['InfoCredits|'. $mylg];
	$info['InfoGeneral'] = $reg['InfoGeneral|'. $mylg];
	$info['InfoSources'] = $reg['InfoSources|'. $mylg];
	$t->assign ("info", $info);
	//$t->assign ("dbden", str2js($reg['InfoGeneral'][1]));
} elseif (isset($_GET['cmd'])) {
	//$q = new Query();
	switch ($_GET['cmd']) {
		case "adminreg":
			// ADMINREG: Form to Create and assign regions
			$t->assign ("cntl", $us->q->getCountryList());
			$t->assign ("usr", $us->getUsersList(''));
			$t->assign ("lglst", $us->q->loadLanguages(1));
			$t->assign ("ctl_adminreg", true);
			$t->assign ("regpa", $us->q->getRegionAdminList());
			$t->assign ("ctl_reglist", true);
		break;
		/* Obsolete cmd 
		case "chkruuid":
			// ADMINREG: check if region ID already exists..
			if ($us->q->isvalidObjectName($_GET['RegionId'], $_GET['RegionId'] ,DI_REGION))
				$t->assign ("cregion", true);
			else
				$t->assign ("cregion", false);
			$t->assign ("ctl_chkruuid", true);
		break;*/
		case "list":
			// ADMINREG: reload list from local SQLITE
			$t->assign ("regpa", $us->q->getRegionAdminList());
			$t->assign ("ctl_reglist", true);
		break;
		default:
			// ADMINREG: insert or update region
			if (($_GET['cmd'] == "insert") || ($_GET['cmd'] == "update")) {
				$data = form2region($_GET);
				$r = new DIRegion($us, $data['RegionId']);
				$r->setFromArray($data);
				$stat = ERR_NO_DATABASE;
				$t->assign ("ctl_admregmess", true);
				$stat = 0;
				if ($_GET['cmd'] == "insert") {
					$stat = $r->insert();
					$t->assign ("cfunct", 'insert');
				} elseif ($_GET['cmd'] == "update") {
					$stat = $r->update();
					$t->assign ("cfunct", 'update');
				}
				$t->assign ("regid", $data['RegionId']);
				// Set Role ADMINREGION in RegionAuth: master for this region
				if (!iserror($stat)) {
					$rol = $us->setUserRole($_GET['RegionUserAdmin'], $data['RegionId'], "ADMINREGION");
				} else {
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
} else {
	$q = new Query();
	$reglst = array();
	$result = $q->core->query("SELECT RegionId, RegionLabel FROM Region WHERE RegionStatus=3 ORDER BY RegionLabel, RegionOrder");
	while ($row = $result->fetch(PDO::FETCH_OBJ)) {
		$reglst[$row->RegionId] = $row->RegionLabel;
	}
	$t->assign ("reglst", $reglst);
	$t->assign ("ctl_noregion", true);
	$t->assign ("ctl_index", true);
}

$t->display ("region.tpl");

</script>