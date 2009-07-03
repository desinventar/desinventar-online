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

$d = new Query();
$u = new UserSession('', '', '');
$r = new Region($reg);

// EDIT ROLE: Form to Create and assign role
if (isset($_GET['rolecmd'])) {
	$mod = "role";
	$cmd = $_GET['rolecmd'];
	if (($cmd == "insert") || ($cmd == "update")) {
		// Set Role in RegionAuth
		$rol = $u->setUserRole($_GET['UserName'], $reg, $_GET['AuthAuxValue']);
		if (!iserror($rol)) 
			$t->assign ("ctl_msgupdrole", true);
		else {
			$t->assign ("ctl_errupdrole", true);
			$t->assign ("updstatrole", showerror($rol));
		}
	}
  // reload list from local SQLITE
  else if ($cmd == "list") {
  	$t->assign ("rol", getRAPermList($u->getUserRoleByRegion($reg, '')));
		$t->assign ("ctl_rollist", true);
  }
}
else {
	$urol = $u->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_adminreg", true);
	$t->assign ("usr", $u->getUsername(''));
	$t->assign ("ctl_rollist", true);
	$t->assign ("rol", getRAPermList($u->getUserRoleByRegion($reg, '')));
}
$t->assign ("reg", $reg);
$t->assign ("dic", $d->queryLabelsFromGroup('DB', $lg));
$t->display ("regionrol.tpl");
</script>
