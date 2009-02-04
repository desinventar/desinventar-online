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
$r = new Region($reg);
$q = new Query($reg);

if (isset($_GET))
	$get = $_GET;

// EDIT REGION: Form to Create and assign regions
if (isset($get['logcmd'])) {
	$mod = "log";
	$cmd = $get['logcmd']; 
	if ($cmd == "insert") {
    $stat = $r->insertRegLog($get['DBLogType'], $get['DBLogNotes']);
    if (!iserror($stat)) 
			$t->assign ("ctl_msginslog", true);
		else {
			$t->assign ("ctl_errinslog", true);
			$t->assign ("insstatlog", $stat);
		}
	}
	else if ($cmd == "update") {
  	$stat = $r->updateRegLog($get['DBLogDate'], $get['DBLogType'], $get['DBLogNotes']);
    if (!iserror($stat)) 
			$t->assign ("ctl_msgupdlog", true);
		else {
			$t->assign ("ctl_errupdlog", true);
			$t->assign ("updstatlog", showerror($stat));
		}
	}
  // reload list from local SQLITE
  else if ($cmd == "list") {
	  if ($mod == "log") {
	  	$t->assign ("log", $q->getRegLogList());
	  	$t->assign ("ctl_loglist", true);
	  }
  }
}
else {
	$urol = $u->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_adminreg", true);
	$t->assign ("usr", $u->getUsername(''));
	$t->assign ("ctl_loglist", true);
	$t->assign ("log", $q->getRegLogList());
}
$t->assign ("reg", $reg);
$t->assign ("dic", $d->queryLabelsFromGroup('DB', $lg));
$t->display ("regionlog.tpl");
</script>
