<script language="php">
/*
 DesInventar - http://www.desinventar.org  
 (c) 1998-2011 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/diregion.class.php');
require_once('include/dievent.class.php');
require_once('include/dicause.class.php');
require_once('include/digeolevel.class.php');
require_once('include/digeocarto.class.php');
require_once('include/digeography.class.php');

function form2region ($val)
{
	$dat = array();
	$dat['RegionLabel']		= $val['RegionLabel'];
	$dat['LangIsoCode']		= $val['LangIsoCode'];
	$dat['CountryIso'] 		= $val['CountryIso'];
	if (empty($val['RegionId']))
	{
		$dat['RegionId']	= DIRegion::buildRegionId($dat['CountryIso'], $dat['RegionLabel']);
	}
	else
	{
		$dat['RegionId']	= $val['RegionId'];
	}
	if (isset($val['RegionActive']) && $val['RegionActive'] == 'on')
	{
		$dat['RegionStatus'] |= CONST_REGIONACTIVE;
	}
	else
	{
		$dat['RegionStatus'] &= ~CONST_REGIONACTIVE;
	}
	if (isset($val['RegionPublic']) && $val['RegionPublic'] == 'on')
	{
		$dat['RegionStatus'] |= CONST_REGIONPUBLIC;
	}
	else
	{
		$dat['RegionStatus'] &= ~CONST_REGIONPUBLIC;
	}
	return $dat;
}

if (isset($_POST['cmd']) && !empty($_POST['cmd']))
{
	$cmd = $_POST['cmd'];
}
elseif (isset($_GET['cmd']) && !empty($_GET['cmd']))
{
	$cmd = $_GET['cmd'];
}

switch ($cmd)
{
	case 'adminreg':
		// ADMINREG: Form to Create and assign regions
		$t->assign('CountryList' , $us->q->getCountryList());
		$t->assign('usr'         , $us->getUsersList(''));
		$t->assign('LanguageList', $us->q->loadLanguages(1));
		$t->assign('ctl_adminreg', true);
		$t->assign('RegionList'  , $us->q->getRegionAdminList());
		$t->assign('ctl_reglist' , true);
	break;
	case 'list':
		// ADMINREG: reload list from local SQLITE
		$t->assign('RegionList', $us->q->getRegionAdminList());
		$t->assign('ctl_reglist', true);
	break;
	case 'createRegionsFromDBDir':
		DIRegion::rebuildRegionListFromDirectory($us);
		$t->assign('RegionList', $us->q->getRegionAdminList());
		$t->assign('ctl_reglist', true);
	break;
	default:
		// ADMINREG: insert or update region
		if (($cmd == 'insert') || ($cmd == 'update'))
		{
			$data = form2region($_GET);
			$r = new DIRegion($us, $data['RegionId']);
			$r->setFromArray($data);
			$stat = ERR_NO_DATABASE;
			$t->assign('ctl_admregmess', true);
			$stat = 0;
			if ($cmd == 'insert')
			{
				$stat = $r->createRegionDB();
				$t->assign('cfunct', 'insert');
			}
			elseif ($cmd == 'update')
			{
				$stat = $r->update();
				$t->assign('cfunct', 'update');
			}
			$t->assign('regid', $data['RegionId']);
			// Set Role ADMINREGION in RegionAuth: master for this region
			if (!iserror($stat))
			{
				$rol = $us->setUserRole($_GET['RegionUserAdmin'], $data['RegionId'], 'ADMINREGION');
			}
			else
			{
				$t->assign('cfunct', '');
				$rol = $stat;
			}
			if (!iserror($rol))
			{
				$t->assign('csetrole', true);
			}
			else
			{
				$t->assign('errsetrole', $rol);
			}
		}
	break;
} //switch
$t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
$t->display('region.tpl');

</script>
