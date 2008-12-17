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

function form2event ($form) {
  $data = array ();
  if (isset($form['EventId']) && !empty($form['EventId']))
    $data['EventId'] = $form['EventId'];
  else
    $data['EventId'] = "";
  if (isset($form['EventLocalName']))
    $data['EventLocalName'] = $form['EventLocalName'];
  if (isset($form['EventLocalDesc']))
    $data['EventLocalDesc'] = $form['EventLocalDesc'];
  else if (isset($form['EventLocalDesc2']))
    $data['EventLocalDesc'] = $form['EventLocalDesc2'];
  if (isset($form['EventActive']) && $form['EventActive'] == "on")
    $data['EventActive'] = true;
  else
    $data['EventActive'] = false;
  if (isset($form['EventPreDefined']) && $form['EventPreDefined'] == "1")
    $data['EventPreDefined'] = true;
  else
    $data['EventPreDefined'] = false;
  return $data;
}

function showResult($stat, &$tp) {
  if (!iserror($stat))
    $tp->assign ("ctl_msgupdeve", true);
  else {
    $tp->assign ("ctl_errupdeve", true);
    $tp->assign ("updstateve", $stat);
    $tp->assign ("ctl_chkname", true);
    $tp->assign ("ctl_chkstatus", true);
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
  $dat = form2event($_GET);
//  print_r($dat);
  switch ($_GET['cmd']) {
    case "insert":
      $ev = $r->insertEvent($dat['EventId'], $dat['EventLocalName'],
              $dat['EventLocalDesc'], $dat['EventActive']);
      showResult($ev, $t);
    break;
    case "update":
      $ev = $r->updateEvent($dat['EventId'], $dat['EventLocalName'],
              $dat['EventLocalDesc'], $dat['EventActive'], $dat['EventPreDefined']);
      showResult($ev, $t);
    break;
    // reload list from local SQLITE
    case "list":
      if ($_GET['predef'] == "1") {
        $t->assign ("ctl_evepred", true);
        $t->assign ("evepredl", $q->loadEvents("PREDEF", null, $lg));
      }
      else {
        $t->assign ("ctl_evepers", true);
        $t->assign ("eveuserl", $q->loadEvents("USER", null, $lg));
      }
    break;
    case "chkname":
      $t->assign ("ctl_chkname", true);
      if ($q->isvalidObjectName($_GET['EventId'], $_GET['EventLocalName'], DI_EVENT))
        $t->assign ("chkname", true);
    break;
    case "chkstatus":
      $t->assign ("ctl_chkstatus", true);
      if ($q->isvalidObjectToInactivate($_GET['EventId'], DI_EVENT))
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
  $t->assign ("ctl_evepred", true);
  $t->assign ("evepredl", $q->loadEvents("PREDEF", null, $lg));
  $t->assign ("ctl_evepers", true);
  $t->assign ("eveuserl", $q->loadEvents("USER", null, $lg));
}
$t->assign ("reg", $reg);
$t->display ("events.tpl");
</script>
