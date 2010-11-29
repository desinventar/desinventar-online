<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/dievent.class.php');

function form2event($form) {
	$data = array ();
	if (isset($form['EventId']) && !empty($form['EventId']))
		$data['EventId'] = $form['EventId'];
	else
		$data['EventId'] = '';
	if (isset($form['EventName']))
		$data['EventName'] = $form['EventName'];
	if (isset($form['EventDesc']))
		$data['EventDesc'] = $form['EventDesc'];
	if (isset($form['EventActive']) && $form['EventActive'] == 'on')
		$data['EventActive'] = 1;
	else
		$data['EventActive'] = 0;
	if (isset($form['EventPreDefined']) && $form['EventPreDefined'] == '1')
		$data['EventPreDefined'] = 1;
	else
		$data['EventPreDefined'] = 0;
	return $data;
}

function showResult($stat, &$tp) {
	if (!iserror($stat))
		$tp->assign('ctl_msgupdeve', true);
	else {
		$tp->assign('ctl_errupdeve', true);
		$tp->assign('updstateve', $stat);
		$tp->assign('ctl_chkname', true);
		$tp->assign('ctl_chkstatus', true);
		if ($stat != ERR_OBJECT_EXISTS)
			$tp->assign('chkname', true);
		if ($stat != ERR_CONSTRAINT_FAIL)
			$tp->assign('chkstatus', true);
	}
}

$get = $_POST;

$RegionId = getParameter('r', getParameter('RegionId'));
if ($RegionId == '') {
	exit();
}

$us->open($RegionId);
$cmd = getParameter('cmd','');
$dat = form2event($_POST);
switch ($cmd) {
	case 'insert':
		$o = new DIEvent($us);
		$o->setFromArray($dat);
		$o->set('EventId', uuid());
		$o->set('EventPredefined', 0);
		$i = $o->insert();
		showResult($i, $t);
	break;
	case 'update':
		$o = new DIEvent($us, $dat['EventId']);
		//$o->set('EventId', $dat['EventId']);
		//$o->load();
		$o->setFromArray($dat);
		$i = $o->update();
		showResult($i, $t);
	break;
	case 'list':
		$prmType = getParameter('predef');
		if ($prmType == '1') {
			$t->assign('ctl_evepred', true);
			$t->assign('evepredl', $us->q->loadEvents('PREDEF', null, $lg));
		} else {
			$t->assign('ctl_evepers', true);
			$t->assign('eveuserl', $us->q->loadEvents('USER', null, $lg));
		}
	break;
	case 'chkname':
		$t->assign('ctl_chkname', true);
		$EventId = getParameter('EventId');
		$EventName = getParameter('EventName');
		if ($us->q->isvalidObjectName($EventId, $EventName, DI_EVENT)) {
			$t->assign('chkname', true);
		}
	break;
	case 'chkstatus':
		$t->assign('ctl_chkstatus', true);
		$EventId = getParameter('EventId');
		if ($us->q->isvalidObjectToInactivate($EventId, DI_EVENT)) {
			$t->assign('chkstatus', true);
		}
	break;
	case 'cmdDBInfoEvent':
		$t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
		$urol = $us->getUserRole($reg);
		if ($urol == 'OBSERVER') {
			$t->assign('ro', 'disabled');
		}
		$t->assign('ctl_show', true);
		$t->assign('ctl_evepred', true);
		$t->assign('evepredl', $us->q->loadEvents('PREDEF', null, $lg));
		$t->assign('ctl_evepers', true);
		$t->assign('eveuserl', $us->q->loadEvents('USER', null, $lg));
	break;
	default:
	break;
} // switch
$t->assign('reg', $reg);
$t->display('events.tpl');
</script>
