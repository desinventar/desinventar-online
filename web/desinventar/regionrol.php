<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 ***********************************************/

require_once('../include/loader.php');

if (isset($_GET['r']) && !empty($_GET['r']))
  $reg = $_GET['r'];
else
  exit();

// EDIT ROLE: Form to Create and assign role
if (isset($_GET['rolecmd'])) {
	$mod = "role";
	$cmd = $_GET['rolecmd'];
	if (($cmd == "insert") || ($cmd == "update")) {
		// Set Role in RegionAuth
		$rol = $us->setUserRole($_GET['UserId'], $reg, $_GET['AuthAuxValue']);
		if (!iserror($rol)) 
			$t->assign ("ctl_msgupdrole", true);
		else {
			$t->assign ("ctl_errupdrole", true);
			$t->assign ("updstatrole", showerror($rol));
		}
	}
	// reload list from local SQLITE
	else if ($cmd == "list") {
		$t->assign ("rol", $us->getRegionRoleList($reg));
		$t->assign ("ctl_rollist", true);
	}
}
else {
	$urol = $us->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_adminreg", true);
	$t->assign ("usr", $us->getUsersList(''));
	$t->assign ("rol", $us->getRegionRoleList($reg));
	$t->assign ("ctl_rollist", true);
}
$t->assign ("usern", $us->UserId);
$t->assign ("reg", $reg);
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->display ("regionrol.tpl");
</script>
