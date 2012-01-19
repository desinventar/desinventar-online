<script language="php">
/*
 DesInventar - http://www.desinventar.org  
 (c) 1998-2012 Corporacion OSSO
*/
    
require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/diregion.class.php');
require_once('include/didisaster.class.php');
$RegionId = getParameter('RegionId', getParameter('r',''));
if ($RegionId == '')
{
	exit(0);
}

// 2009-08-07 (jhcaiced) Validate if Database Exists...
if (! file_exists($us->q->getDBFile($RegionId)))
{
	echo '<h3>Requested Region not found<br />';
	exit();
}

$us->open($RegionId);
$t->assign('RegionId', $RegionId);

// UPDATER: If user is still connected, awake session so it will not expire
if (isset($_GET['u']))
{
	$res = $us->awake();
	if (!iserror($res))
	{
		$status = 'green';
	}
	else
	{
		$status = 'red';
	}
	$t->assign('stat', $status);
	$t->display('cards_updater.tpl');
}
else
{
	$cmd = getParameter('cmd',getParameter('DatacardCommand',''));
	$value = getParameter('value','');
	// Commands in GET mode: lists, checkings..
	switch ($cmd)
	{
		case 'list':
			$lev = $us->q->getNextLev($_GET['GeographyId']);
			$levmax = $us->q->getMaxGeoLev();
			$levname = $us->q->loadGeoLevById($lev);
			$geol = $us->q->loadGeoChilds($_GET['GeographyId']);
			$t->assign('lev', $lev);
			$t->assign('levmax', $levmax);
			$t->assign('levname', $levname);
			$t->assign('geol', $geol);
			$t->assign('opc', isset($_GET['opc']) ? $_GET['opc'] : '');
			$t->display('cards_geolist.tpl');
		break;
		case 'getNextSerial':
			$ser = $us->q->getNextDisasterSerial($value);
			echo json_encode(array('Status' => 'OK', 'DisasterSerial' => $ser));
		break;
		case 'getDisasterIdPrev':
			$answer = $us->getDisasterIdPrev($value);
			echo json_encode($answer);
		break;
		case 'getDisasterIdNext':
			$answer = $us->getDisasterIdNext($value);
			echo json_encode($answer);
		break;
		case 'getDisasterIdFirst':
			$answer = $us->getDisasterIdFirst();
			echo json_encode($answer);
		break;
		case 'getDisasterIdLast':
			$answer = $us->getDisasterIdLast();
			echo json_encode($answer);
		break;
		case 'getDisasterIdFromSerial':
			$answer = $us->getDisasterIdFromSerial($value);
			echo json_encode($answer);
		break;
		case 'existDisasterSerial':
			$DisasterSerial = getParameter('DisasterSerial');
			$answer = $us->existDisasterSerial($DisasterSerial);
			echo json_encode($answer);
		break;
		case 'chklocked':
			// check if datacard is locked by some user
			$answer = array('Status' => 'OK','DatacardStatus' => '');
			$reserv = $us->isDatacardLocked($_GET['DisasterId']);
			if ($reserv == '')
			{
				// reserve datacard
				$us->lockDatacard($_GET['DisasterId']);
				$answer['DatacardStatus'] = 'RESERVED';
			}
			else
			{
				$answer['DatacardStatus'] = 'BLOCKED';
			}
			echo json_encode($answer);
		break;
		case 'chkrelease':
			$us->releaseDatacard($_GET['DisasterId']);
		break;
		case 'getDatacard':
			// Read Datacard Info and return in JSON
			$DisasterId = getParameter('DisasterId','');
			$d = new DIDisaster($us, $DisasterId);
			$dcard = $d->oField['info'];
			$dcard['DisasterBeginTime[0]'] = substr($dcard['DisasterBeginTime'], 0, 4);
			$dcard['DisasterBeginTime[1]'] = '';
			$dcard['DisasterBeginTime[2]'] = '';
			if (strlen($dcard['DisasterBeginTime']) > 4)
			{
				$dcard['DisasterBeginTime[1]'] = substr($dcard['DisasterBeginTime'], 5, 2);
			}
			if (strlen($dcard['DisasterBeginTime']) > 7)
			{
				$dcard['DisasterBeginTime[2]'] = substr($dcard['DisasterBeginTime'], 8, 2);
			}
			$gItems = $us->getGeographyItemsById($dcard['GeographyId']);
			$dcard['GeographyItems'] = $gItems;
			echo json_encode($dcard);
		break;
		case 'insertDICard':
		case 'updateDICard':
			// Update Existing Datacard
			$answer = array();
			$answer['Status']     = 'OK';
			$answer['StatusCode'] = 'UPDATEOK';
			if ($cmd == 'insertDICard')
			{
				$answer['StatusCode'] = 'INSERTOK';
			}
			$answer['StatusMsg']  = '';
			$answer['ErrorCode']  = ERR_NO_ERROR;
			$us->releaseDatacard($_POST['DisasterId']);
			if ($cmd == 'insertDICard')
			{
				$data = form2disaster($_POST, CMD_NEW);
			}
			else
			{
				$data = form2disaster($_POST, CMD_UPDATE);
			}
			$o = new DIDisaster($us, $data['DisasterId']);
			$o->setFromArray($data);
			if ($cmd == 'insertDICard')
			{
				$o->set('RecordCreation', gmdate('c'));
			}
			$o->set('RecordUpdate', gmdate('c'));
			if ($cmd == 'insertDICard')
			{
				$i = $o->insert();
			}
			else
			{
				$i = $o->update();
			}

			if ($i < 0)
			{
				$answer['Status']     = 'ERROR';
				$answer['StatusCode'] = 'ERROR';
				$answer['StatusMsg']  = showerror($i) . '(' . $i . ')';
				$answer['ErrorCode']  = $i;				
			}

			if ($answer['Status'] == 'OK')
			{
				$answer['DisasterId']      = $o->get('DisasterId');
				$answer['DisasterSerial']  = $o->get('DisasterSerial');
				$answer['RecordPublished'] = $us->q->getNumDisasterByStatus('PUBLISHED');
				$answer['RecordReady']     = $us->q->getNumDisasterByStatus('READY');
			}

			if ($cmd == 'insertDICard')
			{
				$answer['RecordCount'] = $us->getDisasterCount();
			}
			echo json_encode($answer);
		break;
		default:
		break;
	} //switch
}

// Convert Post Form to DesInventar Disaster Table struct 
// Insert  (1) create DisasterId. 
// Update  (2) keep RecordCreation and RecordAuthor 
function form2disaster($form, $icmd)
{
	$data = array();
	$geogid = '';
	foreach ($form as $k=>$i)
	{
		if (($icmd == CMD_NEW) || ($icmd == CMD_UPDATE))
		{
			if ((substr($k, 0, 1)  != '_') &&
			    (substr($k, 0, 12) != 'RecordAuthor') &&
			    (substr($k, 0, 19) != 'GeographyId')
			   )
			{
			    $data[$k] = $i;
			}
			else
			{
				if (substr($k, 0, 19) == 'GeographyId')
				{
					$geogid = $i;
				}
			}
		} //if
	} //foreach
	// On Update
	$data['DisasterId'] = $form['DisasterId'];
	$data['RecordCreation'] = $form['RecordCreation'];
	if ($icmd == CMD_NEW)
	{
		// New Disaster
		if ($data['DisasterId'] == '')
		{
			$data['DisasterId'] = uuid();
		}
		$data['RecordCreation'] = date('Y-m-d H:i:s');
	}
	$data['RecordAuthor'] = $form['RecordAuthor'];
	$data['RecordUpdate'] = gmdate('c');
	$c = '';

	// Disaster date
	$str = sprintf('%04d', $form['DisasterBeginTime'][0]);
	if (!empty($form[$c .'DisasterBeginTime'][1]))
	{
		$str .= '-' . sprintf('%02d', $form['DisasterBeginTime'][1]);
		if (!empty($form[$c .'DisasterBeginTime'][2]))
		{
			$str .= '-' . sprintf('%02d', $form['DisasterBeginTime'][2]);
		}
	}
	$data['DisasterBeginTime'] = $str;
	// Disaster Geography
	$data['GeographyId'] = $geogid;
	return $data;
} // function

function form2eedata($form)
{
	$eedat['DisasterId'] = $form['DisasterId'];
	foreach ($form as $k=>$i)
	{
		if (substr($k, 0, 3) == 'EEF')
		{
			$eedat[$k] = $i;
		}
	}
	return $eedat;
}
</script>
