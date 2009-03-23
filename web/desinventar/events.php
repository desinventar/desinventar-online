<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');
require_once('../include/query.class.php');
require_once('../include/usersession.class.php');
require_once('../include/dievent.class.php');

if (isset($_GET['r']) && !empty($_GET['r']))
	$reg = $_GET['r'];
else
	exit();

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

$q = new Query($reg);

if (isset($_GET['cmd'])) {
	switch ($_GET['cmd']) {
	case "insert":
		$o = new DIEvent($us);
		$o->setFromArray($_GET);
		$o->set('EventId', $o->get('EventName'));
		$i = $o->insert();
		showResult($i, $t);
		break;
	case "update":
		$o = new DIEvent($us);
		$o->set('EventId', $_GET['EventId']);
		$o->load();
		$o->setFromArray($_GET);
		$i = 0;
		if ($o->get("EventPreDefined") == 0) {
			// Update only non PreDefined Events
			$i = $o->update();
		}
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
		print "chkname EventId : " . $_GET['EventId'] . "<br>";
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
