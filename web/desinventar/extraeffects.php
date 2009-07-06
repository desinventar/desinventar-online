<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 ***********************************************/

require_once('../include/loader.php');
require_once('../include/query.class.php');
require_once('../include/dieefield.class.php');


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
		$data = array('EEFieldId'     => $_GET['EEFieldId'],
		              'EEFieldLabel'  => $_GET['EEFieldLabel'],
		              'EEFieldDesc'   => $_GET['EEFieldDesc'], 
		              'EEFieldType'   => $_GET['EEFieldType'], 
		              'EEFieldSize'   => $_GET['EEFieldSize'],
		              'EEFieldStatus' => $active);
		$o = new DIEEField($us, $_GET['EEFieldId']);
		$o->setFromArray($data);
		if ($cmd == "insert")
			$stat = $o->insert();
		else if ($cmd == "update")
			$stat = $o->update();
		if (!iserror($stat)) 
			$t->assign ("ctl_msgupdeef", true);
		else {
			$t->assign ("ctl_errupdeef", true);
			$t->assign ("updstateef", showerror($stat));
		}
	}
  // reload list from local SQLITE
  else if ($cmd == "list") {
		$t->assign ("eef", $us->q->getEEFieldList(""));
		$t->assign ("ctl_eeflist", true);
  }
} else {
	$urol = $us->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_admineef", true);
	$t->assign ("eef", $us->q->getEEFieldList(""));
	$t->assign ("ctl_eeflist", true);
}

$t->assign ("reg", $reg);
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->display ("extraeffects.tpl");

</script>
