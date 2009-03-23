<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');
require_once('../include/query.class.php');
require_once('../include/region.class.php');
require_once('../include/usersession.class.php');
require_once('../include/dievent.class.php');

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
	if (isset($form['EvenName']))
		$data['EventName'] = $form['EventName'];
	if (isset($form['EventDesc']))
		$data['EventDesc'] = $form['EventDesc'];
	else if (isset($form['EventDesc2']))
		$data['EventDesc'] = $form['EventDesc2'];
	if (isset($form['EventActive']) && $form['EventActive'] == "on")
		$data['EventActive'] = true;
	else
		$data['EventActive'] = false;
	if (isset($form['EventPreDefined']) && $form['EventPreDefined'] == "1")
		$data['EventPreDefined'] = true;
	else
		$data['EventPreDefined'] = false;
	return $data;
} //function

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
	} //else
} //function

$r = new Region($reg);
$q = new Query($reg);

if (isset($_GET['cmd'])) {
	$dat = form2event($_GET);
	switch ($_GET['cmd']) {
	case "insert":
		$o = new DIEvent($us);
		$o->setFromArray($dat);
		$i = $o->insert();
		/*
		$i = $r->insertEvent($dat['EventId'], $dat['EventName'],
		                      $dat['EventDesc'], $dat['EventActive']);
		*/
		showResult($i, $t);
		break;
	case "update":
		$o = new DIEvent($us);
		$o->set('EventId', $dat['EventId']);
		$o->load();
		$o->setFromArray($dat);
		$i = 0;
		if ($o->get("EventPreDefined") == 0) {
			// Update only non PreDefined Events
			$i = $o->update();
		}
		/*
		$i = $r->updateEvent($dat['EventId'], $dat['EventName'],
		                     $dat['EventDesc'], $dat['EventActive'], 
		                     $dat['EventPreDefined']);
		*/
		showResult($i, $t);
		break;
	case "list":
		// reload list from local SQLITE
		if ($_GET['predef'] == "1") {
			$t->assign ("ctl_evepred", true);
			$t->assign ("evepredl", $q->loadEvents("PREDEF", null, $lg));
		} else {
			$t->assign ("ctl_evepers", true);
			$t->assign ("eveuserl", $q->loadEvents("USER", null, $lg));
		}
		break;
	case "chkname":
		$t->assign ("ctl_chkname", true);
		if ($q->isvalidObjectName($_GET['EventId'], $_GET['EventName'], DI_EVENT))
			$t->assign ("chkname", true);
		break;
	case "chkstatus":
		$t->assign ("ctl_chkstatus", true);
		if ($q->isvalidObjectToInactivate($_GET['EventId'], DI_EVENT))
			$t->assign ("chkstatus", true);
		break;
	default: break;
	} // switch
} else {
	$t->assign ("dic", $q->queryLabelsFromGroup('DB', $lg));
	$urol = $us->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_show", true);
	$t->assign ("ctl_evepred", true);
	$t->assign ("evepredl", $q->loadEvents("PREDEF", null, $lg));
	$t->assign ("ctl_evepers", true);
	$t->assign ("eveuserl", $q->loadEvents("USER", null, $lg));
} //else

$t->assign ("reg", $reg);
$t->display ("events.tpl");

</script>
