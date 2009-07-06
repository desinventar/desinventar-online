<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');
require_once('../include/query.class.php');
require_once('../include/region.class.php');
require_once('../include/dicause.class.php');

if (isset($_GET['r']) && !empty($_GET['r']))
	$reg = $_GET['r'];
else
	exit();

function form2cause ($form) {
	$data = array ();
  if (isset($form['CauseId']) && !empty($form['CauseId']))
    $data['CauseId'] = $form['CauseId'];
  else
    $data['CauseId'] = "";
  if (isset($form['CauseName']))
    $data['CauseName'] = $form['CauseName'];
  if (isset($form['CauseDesc']))
    $data['CauseDesc'] = $form['CauseDesc'];
  else if (isset($form['CauseDesc2']))
    $data['CauseDesc'] = $form['CauseDesc2'];
  if (isset($form['CauseActive']) && $form['CauseActive'] == "on")
    $data['CauseActive'] = true;
  else
    $data['CauseActive'] = false;
  if (isset($form['CausePreDefined']) && $form['CausePreDefined'] == "1")
    $data['CausePreDefined'] = true;
  else
    $data['CausePreDefined'] = false;
  return $data;
}

function showResult($stat, &$tp) {
	if (!iserror($stat))
		$tp->assign ("ctl_msgupdcau", true);
	else {
		$tp->assign ("ctl_errupdcau", true);
		$tp->assign ("updstatcau", $stat);
		$tp->assign ("ctl_chkname", true);
		if ($stat != ERR_OBJECT_EXISTS)
			$tp->assign ("chkname", true);
		if ($stat != ERR_CONSTRAINT_FAIL)
			$tp->assign ("chkstatus", true);
	}
}

//$r = new Region($reg);
//$q = new Query($reg);

if (isset($_GET['cmd'])) {
	$dat = form2cause($_GET);
	switch ($_GET['cmd']) {
	case "insert":
		$o = new DICause($us);
		$o->setFromArray($_GET);
		$o->set('CauseId', $uuid());
		$o->set('CausePredefined', 0);
		$i = $o->insert();
		showResult($i, $t);
		break;
	case "update";
		$o = new DICause($us);
		$o->set('CauseId', $_GET['CauseId']);
		$o->load();
		$o->setFromArray($_GET);
		$i = $o->update();
		showResult($i, $t);
		break;
	case "list":
		// reload list from local SQLITE
		if ($_GET['predef'] == "1") {
			$t->assign ("ctl_caupred", true);
			$t->assign ("caupredl", $us->q->loadCauses("PREDEF", null, $lg));
		} else {
			$t->assign ("ctl_caupers", true);
			$t->assign ("cauuserl", $us->q->loadCauses("USER", null, $lg));
		}
		break;
	case "chkname":
		$t->assign ("ctl_chkname", true);
		if ($us->q->isvalidObjectName($_GET['CauseId'], $_GET['CauseName'], DI_CAUSE))
			$t->assign ("chkname", true);
		break;
	case "chkstatus":
		$t->assign ("ctl_chkstatus", true);
		if ($us->q->isvalidObjectToInactivate($_GET['CauseId'], DI_CAUSE))
			$t->assign ("chkstatus", true);
		break;
	default: break;
	} //switch
} else {
	$t->assign ("dic", $us->q->queryLabelsFromGroup('DB', $lg));
	$urol = $us->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_show", true);
	$t->assign ("ctl_caupred", true);
	$t->assign ("caupredl", $us->q->loadCauses("PREDEF", "active", $lg));
	$t->assign ("ctl_caupers", true);
	$t->assign ("cauuserl", $us->q->loadCauses("USER", "active", $lg));
}

$t->assign ("reg", $reg);
$t->display ("causes.tpl");

</script>
