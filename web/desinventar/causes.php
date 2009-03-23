<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

require_once('../include/loader.php');
require_once('../include/dictionary.class.php');
require_once('../include/query.class.php');
require_once('../include/region.class.php');
require_once('../include/user.class.php');

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

$d = new Dictionary(VAR_DIR);
$r = new Region($reg);
$q = new Query($reg);
$u = new User('', '', '');

if (isset($_GET['cmd'])) {
  $dat = form2cause($_GET);
//  print_r($dat);
  switch ($_GET['cmd']) {
    case "insert":
      $ev = $r->insertCause($dat['CauseId'], $dat['CauseName'],
              $dat['CauseDesc'], $dat['CauseActive']);
      showResult($ev, $t);
    break;
    case "update":
      $ev = $r->updateCause($dat['CauseId'], $dat['CauseName'],
              $dat['CauseDesc'], $dat['CauseActive'], $dat['CausePreDefined']);
      showResult($ev, $t);
    break;
    // reload list from local SQLITE
    case "list":
      if ($_GET['predef'] == "1") {
        $t->assign ("ctl_caupred", true);
        $t->assign ("caupredl", $q->loadCauses("PREDEF", null, $lg));
      }
      else {
        $t->assign ("ctl_caupers", true);
        $t->assign ("cauuserl", $q->loadCauses("USER", null, $lg));
      }
    break;
    case "chkname":
      $t->assign ("ctl_chkname", true);
      if ($q->isvalidObjectName($_GET['CauseId'], $_GET['CauseName'], DI_CAUSE))
        $t->assign ("chkname", true);
    break;
    case "chkstatus":
      $t->assign ("ctl_chkstatus", true);
      if ($q->isvalidObjectToInactivate($_GET['CauseId'], DI_CAUSE))
        $t->assign ("chkstatus", true);
    break;
    default: break;
  }
}
else {
	$t->assign ("dic", $d->queryLabelsFromGroup('DB', $lg));

	$urol = $u->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
  $t->assign ("ctl_show", true);
  $t->assign ("ctl_caupred", true);
  $t->assign ("caupredl", $q->loadCauses("PREDEF", "active", $lg));
  $t->assign ("ctl_caupers", true);
  $t->assign ("cauuserl", $q->loadCauses("USER", "active", $lg));
}
$t->assign ("reg", $reg);
$t->display ("causes.tpl");

</script>
