<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/dicause.class.php');

function form2cause ($form)
{
	$data = array ();
	if (isset($form['CauseId']) && !empty($form['CauseId']))
		$data['CauseId'] = $form['CauseId'];
	else
		$data['CauseId'] = '';
	if (isset($form['CauseName']))
		$data['CauseName'] = $form['CauseName'];
	if (isset($form['CauseDesc']))
		$data['CauseDesc'] = $form['CauseDesc'];
	if (isset($form['CauseActive']) && $form['CauseActive'] == 'on')
		$data['CauseActive'] = 1;
	else
		$data['CauseActive'] = 0;
	if (isset($form['CausePreDefined']) && $form['CausePreDefined'] == '1')
		$data['CausePreDefined'] = 1;
	else
		$data['CausePreDefined'] = 0;
	return $data;
}

function showResult($stat, &$tp)
{
	if (!iserror($stat))
	{
		$tp->assign('ctl_msgupdcau', true);
	}
	else
	{
		$tp->assign('ctl_errupdcau', true);
		$tp->assign('updstatcau', $stat);
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
if ($RegionId == '')
{
	exit();
}
$us->open($RegionId);
$cmd = getParameter('cmd','');

$dat = form2cause($get);
switch($cmd)
{
	case 'insert':
		$o = new DICause($us);
		$o->setFromArray($dat);
		$o->set('CauseId', uuid());
		$o->set('CausePredefined', 0);
		$o->set('RegionId', $RegionId);
		$i = $o->insert();
		showResult($i, $t);
		break;
	case 'update';
		$o = new DICause($us, $dat['CauseId']);
		$o->setFromArray($dat);
		$o->set('RegionId', $RegionId);
		$i = $o->update();
		showResult($i, $t);
		break;
	case 'list':
		// reload list from local SQLITE
		$prmType = getParameter('predef');
		if ($prmType == '1')
		{
			$t->assign('ctl_caupred', true);
			$t->assign('caupredl', $us->q->loadCauses('PREDEF', null, $lg));
		}
		else
		{
			$t->assign('ctl_caupers', true);
			$t->assign('cauuserl', $us->q->loadCauses('USER', null, $lg));
		}
		break;
	case 'chkname':
		$t->assign('ctl_chkname', true);
		$CauseId = getParameter('CauseId');
		$CauseName = getParameter('CauseName');
		if ($us->q->isvalidObjectName($CauseId, $CauseName, DI_CAUSE))
		{
			$t->assign('chkname', true);
		}
		break;
	case 'chkstatus':
		$t->assign('ctl_chkstatus', true);
		$CauseId = getParameter('CauseId');
		if ($us->q->isvalidObjectToInactivate($CauseId, DI_CAUSE))
		{
			$t->assign('chkstatus', true);
		}
	break;
	case 'cmdDBInfoCause':
		$t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
		$urol = $us->getUserRole($RegionId);
		if ($urol == 'OBSERVER')
			$t->assign('ro', 'disabled');
		$t->assign('ctl_show', true);
		$t->assign('ctl_caupred', true);
		$t->assign('caupredl', $us->q->loadCauses('PREDEF', null, $lg));
		$t->assign('ctl_caupers', true);
		$t->assign('cauuserl', $us->q->loadCauses('USER', null, $lg));
	break;
	default:
	break;
} //switch
$t->assign('reg', $RegionId);
$t->display('causes.tpl');

</script>
