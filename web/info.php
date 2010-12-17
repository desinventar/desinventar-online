<script language="php">
/*
  DesInventar - http://www.desinventar.org  
  (c) 1998-2010 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/diobject.class.php');
require_once('include/diregion.class.php');

function getRAPermList($lst)
{
	$dat = array();
	foreach ($lst as $k=>$v)
	{
		if ($v=='NONE' || $v=='USER' || $v=='OBSERVER' || $v=='SUPERVISOR')
		{
			$dat[$k] = $v;
		}
	}
	return $dat;
}

function form2data($form)
{
	$dat = array();
	foreach ($form as $key=>$value)
	{
		$k = explode('_', $key);
		if (count($k) == 1)
		{
			$dat[$key] = $value;
		}
		elseif (count($k) == 2)
		{
			if (empty($k[1]))
			{
				$dat[$k[0]] = $value;
			}
			elseif (empty($k[0]))
			{
				$dat[$k[1]] = $value;
			}
		}
	}
	return $dat;
}

$post = $_POST;
$get = $_GET;
$RegionId = getParameter('_REG', getParameter('r', getParameter('RegionId','')));
$cmd  = getParameter('_infocmd', getParameter('cmd',''));
if ($RegionId == '')
{
	exit();
}
$us->open($RegionId);
switch($cmd)
{
	case 'cmdDBInfoUpdate':
		// EDIT REGION: Form to Create and assign regions
		$ifo = 0;
		$data = form2data($post);
		$data = $_POST['RegionInfo'];

		// Load Current info.xml data
		$r = new DIRegion($us, $RegionId);
		$LangIsoCode = $r->get('LangIsoCode');
		$r->setFromArray($_POST);
		// Set Translated Info
		$tf = $r->getTranslatableFields();
		foreach($tf as $FieldName => $FieldType)
		{
			$r->set($FieldName, $data[$LangIsoCode][$FieldName], $LangIsoCode);
			$r->set($FieldName, $data['eng'][$FieldName],'eng');
		}
		$ifo = $r->update();
		if (!iserror($ifo))
		{
			$t->assign('ctl_msgupdinfo', true);
			if (isset($_FILES['logofile']) && $_FILES['logofile']['error'] == UPLOAD_ERR_OK)
			{
				move_uploaded_file($_FILES['logofile']['tmp_name'], VAR_DIR . '/database/' . $RegionId . '/logo.png');
			}
		}
		else
		{
			$t->assign('ctl_errupdinfo', true);
			$t->assign('updstatinfo', $ifo);
		}
	break;
	case 'cmdDBInfoEdit':
		$UserRole = $us->getUserRole($RegionId);
		// DISPLAY REGION INFO
		if ($UserRole == 'OBSERVER')
		{
			$t->assign('ro', 'disabled');
		}
		$r = new DIRegion($us, $RegionId);
		$lang = array();
		$lang[0] = $r->get('LangIsoCode');
		$lang[1] = 'eng';
		foreach ($lang as $lng)
		{
			$info[$lng]['InfoCredits'] 		= array($r->get('InfoCredits'    , $lng), 'TEXT');
			$info[$lng]['InfoGeneral'] 		= array($r->get('InfoGeneral'    , $lng), 'TEXT');
			$info[$lng]['InfoSources'] 		= array($r->get('InfoSources'    , $lng), 'TEXT');
			$info[$lng]['InfoSynopsis'] 	= array($r->get('InfoSynopsis'   , $lng), 'TEXT');
			$info[$lng]['InfoObservation']	= array($r->get('InfoObservation', $lng), 'TEXT');
			$info[$lng]['InfoGeography']	= array($r->get('InfoGeography'  , $lng), 'TEXT');
			$info[$lng]['InfoCartography']	= array($r->get('InfoCartography', $lng), 'TEXT');
			$info[$lng]['InfoAdminURL']		= array($r->get('InfoAdminURL'   , $lng), 'VARCHAR');
		}
		$t->assign('info', $info);
		$sett['RegionLabel']    = array($r->get('RegionLabel'), 'TEXT');
		$sett['GeoLimitMinX']	= array($r->get('GeoLimitMinX'), 'NUMBER');
		$sett['GeoLimitMinY']	= array($r->get('GeoLimitMinY'), 'NUMBER');
		$sett['GeoLimitMaxX']	= array($r->get('GeoLimitMaxX'), 'NUMBER');
		$sett['GeoLimitMaxY']	= array($r->get('GeoLimitMaxY'), 'NUMBER');
		$sett['PeriodBeginDate']= array($r->get('PeriodBeginDate'), 'DATE');
		$sett['PeriodEndDate']	= array($r->get('PeriodEndDate'), 'DATE');
		$t->assign('sett', $sett);
		$UserList = $us->getUsersList('');
		$UserRoleList = $us->getRegionRoleList($RegionId);
		$t->assign('usr', $UserList);
		$t->assign('rol', $UserRoleList);
		$t->assign('log', $us->q->getRegLogList());
		$t->assign('ctl_adminreg', true);
		$t->assign('ctl_rollist', true);
		$t->assign('ctl_loglist', true);
	break;
	case 'cmdDBInfoRoleList':
		// reload list of roles for this database
		$t->assign('rol', $us->getRegionRoleList($RegionId));
		$t->assign('ctl_rollist', true);
	break;
	case 'cmdDBInfoRoleInsert':
	case 'cmdDBInfoRoleUpdate':
		// Set Role in RegionAuth
		$rol = $us->setUserRole($get['UserId'], $RegionId, $get['AuthAuxValue']);
		if (!iserror($rol))
		{
			$t->assign('ctl_msgupdrole', true);
		}
		else
		{
			$t->assign('ctl_errupdrole', true);
			$t->assign('updstatrole', showerror($rol));
		}
	break;
	case 'cmdDBInfoLogList':
		$t->assign('log', $us->q->getRegLogList());
		$t->assign('ctl_loglist', true);
	break;
	case 'cmdDBInfoLogInsert':
		$stat = 1;
		//2009-07-06 (jhcaiced) Replace this with another class...
		//$stat = $r->insertRegLog($get['DBLogType'], $get['DBLogNotes']);
		if (!iserror($stat))
		{
			$t->assign('ctl_msginslog', true);
		}
		else
		{
			$t->assign('ctl_errinslog', true);
			$t->assign('insstatlog', $stat);
		}
	break;
	case 'cmdDBInfoLogUpdate':
		$stat = 1;
		// 2009-07-06 (jhcaiced) Replace this with another class...
		//$stat = $r->updateRegLog($get['DBLogDate'], $get['DBLogType'], $get['DBLogNotes']);
		if (!iserror($stat))
		{
			$t->assign('ctl_msgupdlog', true);
		}
		else
		{
			$t->assign('ctl_errupdlog', true);
			$t->assign('updstatlog', showerror($stat));
		}
	break;
} //select
$t->assign('reg', $RegionId);
$t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
$t->assign('usern', $us->UserId);
$t->display('info.tpl');
</script>
