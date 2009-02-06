<script language="php">
/*
 **********************************************
 DesInventar8 - http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 **********************************************
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/region.class.php');
require_once('include/usersession.class.php');
$t->config_dir = 'include';

function form2region ($val) {
  $dat = array();
  if (isset($val['RegionUUID']))
    $dat['RegionUUID'] = $val['RegionUUID'];
  else
    $dat['RegionUUID'] = isset($val['RegionUUID2']) ? $val['RegionUUID2'] : "NOREG";
  $dat['RegionLabel'] = $val['RegionLabel'];
  $dat['CountryIsoCode'] = $val['CountryIsoCode'];
  if (isset($val['RegionActive']) && $val['RegionActive'] == "on")
    $dat['RegionActive'] = true;
  else
    $dat['RegionActive'] = false;
  if (isset($val['RegionPublic']) && $val['RegionPublic'] == "on")
    $dat['RegionPublic'] = true;
  else
    $dat['RegionPublic'] = false;
  return $dat;
}
// REGIONS: Show databases for selected Country from left menu
if (isset($_GET['c']) && (strlen($_GET['c']) > 0)) {
  $t->assign ("ctl_regions", true);
  $q = new Query('');
  $t->assign ("cnt", $q->getCountryByCode($_GET['c']));
  $dbs = $q->getRegionList($_GET['c'], "ACTIVE");
  $t->assign ("ctl_available", true);
  $t->assign ("dbs", $dbs);
}

// REGIONINFO: Show Information about Region
if (isset($_GET['r']) && (strlen($_GET['r']) > 0)) {
  // set region
  $ruuid = $_GET['r'];
  if (isset($_GET['v']) && $_GET['v'] == "true") {
    // Get Information to VRegion
    $q = new Query('');
    $vri = $q->getVirtualRegInfo($ruuid);
    $regname = $vri['VirtualRegLabel'];
    $dbdes = nl2br($vri['VirtualRegDesc']);
    $dbden = ""; //nl2br($vri['VirtualRegDescEN']);
    // Show active or public regions only
    if ($vri['VirtualRegActive'] && $vri['VirtualRegPublic'])
      $t->assign ("ctl_showdcmod", true);
    $t->assign ("isvreg", "true");
    $t->assign ("ctl_showreg", true);
  }
  else {
    // Get Information to Region
    $q = new Query($ruuid);
    $t->assign ("period", $q->getDateRange());
    $t->assign ("dtotal", $q->getNumDisasterByStatus("PUBLISHED"));
    $t->assign ("lstupd", $q->getLastUpdate());
    
	// Enable access only to users with a valid role in this region
	$role = $us->getUserRole($ruuid);
    if ($role=="OBSERVER" || $role=="USER" || 
        $role=="SUPERVISOR" || $role=="ADMINREGION") {
      $t->assign ("ctl_showdimod", true);
      $t->assign ("ctl_showdcmod", true);
    }
    // Show active or public regions only
    $act = $q->getRegionFieldByID($ruuid, "RegionActive");
    $pub = $q->getRegionFieldByID($ruuid, "RegionPublic");
    if ($pub[$ruuid])
      $t->assign ("ctl_showdcmod", true);
    $t->assign ("ctl_showreg", true);
    $reg = $q->getDBInfo();
    $regname = $reg['RegionLabel'];
    $dbdes = nl2br($reg['RegionDesc']);
    $dbden = nl2br($reg['RegionDescEN']);
    $t->assign ("log", $q->getRegLogList());
    $t->assign ("lang", $reg['RegionLangCode']);
    $t->assign ("dbadm", $reg['OptionAdminURL']);
  }
  if (isset($_GET['cmd']) && $_GET['cmd'] == "info")
    $t->assign ("ctl_reginfo", true);
  $t->assign ("reg", $ruuid);
  $t->assign ("regname", $regname);
  $t->assign ("dbdes", $dbdes);
  $t->assign ("dbden", str2js($dbden));
}
else if (isset($_GET['cmd'])) {
	$q = new Query('');
	switch ($_GET['cmd']) {
	  // ADMINREG: Form to Create and assign regions
	  case "adminreg":
      $t->assign ("cntl", $q->getCountryList());
      $t->assign ("usr", $us->getUserFullName(''));
      $t->assign ("ctl_adminreg", true);
      $t->assign ("regpa", $q->getRegionAdminList());
      $t->assign ("ctl_reglist", true);
	  break;
    // ADMINREG: check if region ID already exists..
    case "chkruuid":
      if ($q->isvalidObjectName($_GET['RegionUUID'], $_GET['RegionUUID'] ,DI_REGION))
        $t->assign ("cregion", true);
      else
        $t->assign ("cregion", false);
      $t->assign ("ctl_chkruuid", true);
    break;
    // ADMINREG: reload list from local SQLITE
    case "list":
      $t->assign ("regpa", $q->getRegionAdminList());
      $t->assign ("ctl_reglist", true);
    break;
    default:
      // ADMINREG: insert or update region
      $r = new Region('');
      if (($_GET['cmd'] == "insert") || ($_GET['cmd'] == "update")) {
        $data = form2region($_GET);
        $stat = ERR_NO_DATABASE;
        $t->assign ("ctl_admregmess", true);
        if ($_GET['cmd'] == "insert") {
          $stat = $r->insertRegion($data['RegionUUID'], $data['RegionLabel'], $data['CountryIsoCode'], 
                $data['RegionActive'], $data['RegionPublic']);
          $t->assign ("cfunct", 'insert');
        }
        else if ($_GET['cmd'] == "update") {
          $stat = $r->updateRegion($data['RegionUUID'], $data['RegionLabel'], $data['CountryIsoCode'], 
                $data['RegionActive'], $data['RegionPublic']);
          $t->assign ("cfunct", 'update');
        }
        $t->assign ("regid", $data['RegionUUID']);
        // Set Role ADMINREGION in RegionAuth: master for this region
        if (!iserror($stat))
          $rol = $us->setUserRole($_GET['RegionUserAdmin'], $data['RegionUUID'], "ADMINREGION");
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
  }
}
else
  $t->assign ("regname", "Undefined Region!");

$t->display ("region.tpl");

</script>
