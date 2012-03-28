<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/dieefield.class.php');

function getRAPermList($lst)
{
	$dat = array();
	foreach ($lst as $k=>$v)
		if ($v=='NONE' || $v=='USER' || $v=='OBSERVER' || $v=='SUPERVISOR')
			$dat[$k] = $v;
	return $dat;
}
$get = $_POST;
$RegionId = getParameter('RegionId',getParameter('r',''));
if ($RegionId == '')
{
	exit();
}
$cmd = getParameter('cmd','');
$us->open($RegionId);
switch($cmd)
{
	case 'cmdEEFieldInsert':
	case 'cmdEEFieldUpdate':
		$status = 0;
		if ($get['EEField']['EEFieldActive'] == 'on')
		{
			$status |= CONST_REGIONACTIVE;
		}
		else
		{
			$status &= ~CONST_REGIONACTIVE;
		}
		if ($get['EEField']['EEFieldPublic'] == 'on')
		{
			$status |= CONST_REGIONPUBLIC;
		}
		else
		{
			$status &= ~CONST_REGIONPUBLIC;
		}
		$get['EEField']['EEFieldStatus'] = $status;
		$o = new DIEEField($us, $get['EEField']['EEFieldId']);
		$EEFieldId = $o->get('EEFieldId');
		$o->setFromArray($get['EEField']);
		$o->set('EEFieldId', $EEFieldId);
		$o->set('RegionId', $RegionId);
		if ($cmd == 'cmdEEFieldInsert')
		{
			if ($EEFieldId == '')
			{
				$EEFieldId = $o->getNextEEFieldId();
				$o->set('EEFieldId', $EEFieldId);
			}
			$stat = $o->insert();
		}
		elseif ($cmd == 'cmdEEFieldUpdate')
		{
			$stat = $o->update();
		}
		$answer = array();
		if (!iserror($stat))
		{
			$answer['Status'] = 'OK';
		}
		else
		{
			$answer['Status'] = 'ERROR';
			$answer['ErrorMsg'] = showerror($stat);
		}
		echo json_encode($answer);
	break;
	case 'cmdEEFieldList':
		// reload list from local SQLITE
		$t->assign('eef', $us->q->getEEFieldList(''));
		$t->assign('ctl_eeflist', true);
		$t->force_compile   = true; # Force this template to always compile		
		$t->display('extraeffects.tpl');
	break;
	default:
		$t->assign('reg', $RegionId);
		$t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
		$urol = $us->getUserRole($RegionId);
		$t->assign('ctl_admineef', true);
		$eef =  $us->q->getEEFieldList('');
		$t->assign('eef', $eef);
		$t->assign('ctl_eeflist', true);
		$t->force_compile   = true; # Force this template to always compile		
		$t->display('extraeffects.tpl');
	break;
} //switch

</script>
