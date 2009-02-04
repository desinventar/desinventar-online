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
require_once('../include/dictionary.class.php');

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

$d = new Dictionary(VAR_DIR);
$u = new User('', '', '');
$q = new Query($reg);

// EDIT REGION: Form to Create and assign regions
if (isset($_GET['cmd'])) {
	$cmd = $_GET['cmd'];
	if (($cmd == "insert") || ($cmd == "update")) {
		if (isset($_GET['EEFieldActive']) && $_GET['EEFieldActive'] == "on")
			$active = true;
		else
			$active = false;
		if (isset($_GET['EEFieldPublic']) && $_GET['EEFieldPublic'] == "on")
			$public = true;
		else
			$public = false;
		$r = new Region($reg);
		if ($cmd == "insert")
			$stat = $r->insertEEField($_GET['EEFieldLabel'], $_GET['EEFieldDesc'], 
					$_GET['EEFieldType'], $_GET['EEFieldSize'], $active, $public);
		else if ($cmd == "update")
			$stat = $r->updateEEField($_GET['EEFieldId'], $_GET['EEFieldLabel'], $_GET['EEFieldDesc'], 
					$_GET['EEFieldType'], $_GET['EEFieldSize'], $active, $public);
		if (!iserror($stat)) 
			$t->assign ("ctl_msgupdeef", true);
		else {
			$t->assign ("ctl_errupdeef", true);
			$t->assign ("updstateef", showerror($stat));
		}
	}
  // reload list from local SQLITE
  else if ($cmd == "list") {
		$t->assign ("eef", $q->getEEFieldList(""));
		$t->assign ("ctl_eeflist", true);
  }
}
else {
	$urol = $u->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_admineef", true);
	$t->assign ("eef", $q->getEEFieldList(""));
	$t->assign ("ctl_eeflist", true);
}

$t->assign ("reg", $reg);
$t->assign ("dic", $d->queryLabelsFromGroup('DB', $lg));
$t->display ("extraeffects.tpl");

</script>
