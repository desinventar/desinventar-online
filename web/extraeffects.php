<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/dieefield.class.php');

function getRAPermList($lst) {
	$dat = array();
	foreach ($lst as $k=>$v)
		if ($v=="NONE" || $v=="USER" || $v=="OBSERVER" || $v=="SUPERVISOR")
			$dat[$k] = $v;
	return $dat;
}
$get = $_GET;

$RegionId = getParameter('r','');
if ($RegionId == '') {
	exit();
}
$cmd = getParameter('cmd','');
$us->open($RegionId);
switch($cmd) {
	case 'insert':
	case 'update':
		if (isset($get['EEFieldActive']) && $get['EEFieldActive'] == "on") {
			$status |= CONST_REGIONACTIVE;
		} else {
			$status &= ~CONST_REGIONACTIVE;
		}
		if (isset($get['EEFieldPublic']) && $get['EEFieldPublic'] == "on") {
			$status |= CONST_REGIONPUBLIC;
		} else {
			$status &= ~CONST_REGIONPUBLIC;
		}
		$data = array('EEFieldId'     => $get['EEFieldId'],
		              'EEFieldLabel'  => $get['EEFieldLabel'],
		              'EEFieldDesc'   => $get['EEFieldDesc'], 
		              'EEFieldType'   => $get['EEFieldType'], 
		              'EEFieldSize'   => $get['EEFieldSize'],
		              'EEFieldStatus' => $status);
		$o = new DIEEField($us, $get['EEFieldId']);
		$EEFieldId = $o->get('EEFieldId');
		$o->setFromArray($data);
		$o->set('EEFieldId', $EEFieldId);
		$o->set('RegionId', $RegionId);
		if ($cmd == "insert") {
			$stat = $o->insert();
		} elseif ($cmd == "update") {
			$stat = $o->update();
		}
		if (!iserror($stat)) {
			$t->assign ("ctl_msgupdeef", true);
		} else {
			$t->assign ("ctl_errupdeef", true);
			$t->assign ("updstateef", showerror($stat));
		}
		break;
	case 'list':
		// reload list from local SQLITE
		$t->assign ("eef", $us->q->getEEFieldList(""));
		$t->assign ("ctl_eeflist", true);
		break;
	default:
		$urol = $us->getUserRole($RegionId);
		if ($urol == "OBSERVER") {
			$t->assign ("ro", "disabled");
		}
		$t->assign ("ctl_admineef", true);
		$eef =  $us->q->getEEFieldList("");
		$t->assign ("eef", $eef);
		$t->assign ("ctl_eeflist", true);
		break;
} //switch

$t->assign ("reg", $RegionId);
$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
$t->display ("extraeffects.tpl");

</script>
