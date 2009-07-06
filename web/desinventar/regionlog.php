<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

require_once('../include/loader.php');

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

if (isset($_GET))
	$get = $_GET;

// EDIT REGION: Form to Create and assign regions
if (isset($get['logcmd'])) {
	$mod = "log";
	$cmd = $get['logcmd']; 
	if ($cmd == "insert") {
		$stat = 1;
		//2009-07-06 (jhcaiced) Replace this with another class...
		//$stat = $r->insertRegLog($get['DBLogType'], $get['DBLogNotes']);
		if (!iserror($stat)) 
			$t->assign ("ctl_msginslog", true);
		else {
			$t->assign ("ctl_errinslog", true);
			$t->assign ("insstatlog", $stat);
		}
	} elseif ($cmd == "update") {
		$stat = 1;
		// 2009-07-06 (jhcaiced) Replace this with another class...
		//$stat = $r->updateRegLog($get['DBLogDate'], $get['DBLogType'], $get['DBLogNotes']);
		if (!iserror($stat)) 
			$t->assign ("ctl_msgupdlog", true);
		else {
			$t->assign ("ctl_errupdlog", true);
			$t->assign ("updstatlog", showerror($stat));
		}
	} elseif ($cmd == "list") {
		// reload list from local SQLITE
		if ($mod == "log") {
			$t->assign ("log", $us->q->getRegLogList());
			$t->assign ("ctl_loglist", true);
		}
	}
} else {
	$urol = $us->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_adminreg", true);
	$t->assign ("usr", $us->getUserFullName(''));
	$t->assign ("ctl_loglist", true);
	$t->assign ("log", $us->q->getRegLogList());
}
$t->assign ("reg", $reg);
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->display ("regionlog.tpl");
</script>
