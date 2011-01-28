<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2011 Corporacion OSSO
*/

//ob_start( 'ob_gzhandler' );
require_once('include/loader.php');
require_once('include/diregion.class.php');
require_once('include/diregiondb.class.php');
require_once('include/diregionrecord.class.php');

$post = $_POST;
$get  = $_GET;

$cmd = getParameter('cmd', getParameter('_CMD',''));
if ($cmd == '')
{
	if (isset($_POST['prmQuery']['Command']))
	{
		$cmd = $_POST['prmQuery']['Command'];
	}
}

$RegionId = getParameter('r', getParameter('RegionId', getParameter('_REG'),''));
$RegionLabel = '';
if ($cmd == '' && $RegionId == '')
{
	$cmd = 'main';
}
// Default Template Values
$t->assign('desinventarRegionId', $RegionId);
if (!empty($RegionId))
{
	$us->open($RegionId);
	$r = new DIRegion($us, $RegionId);
	$RegionLabel = $r->getRegionInfoValue('RegionLabel');
}
$t->assign('desinventarRegionLabel', $RegionLabel);

// 2010-07-23 (jhcaiced) When uploaded file with SWFUpload is bigger than 
// post__max_size in php.ini the script is called with emtpy POST/FILES 
// parameters, here we try to detect that case and call the appropiate command.
if ( (substr($_SERVER['CONTENT_TYPE'],0,19) == 'multipart/form-data') &&
     ($_SERVER['HTTP_USER_AGENT'] == 'Shockwave Flash') )
{
     $cmd = 'fileupload';
}
switch ($cmd)
{
	case 'getversion':
		print VERSION;
	break;
	case 'cmdSessionAwake':
		$iReturn = $us->awake();
		$answer = array();
		$answer['Status'] = $iReturn;
		echo json_encode($answer);
	break;
	case 'cmdRegionBuildRegionId':
		$answer = array();
		$answer['Status']     = ERR_NO_ERROR;
		$answer['CountryIso'] = getParameter('CountryIso');
		$answer['RegionId']   = DIRegion::buildRegionId($answer['CountryIso']);
		echo json_encode($answer);
	break;
	case 'cmdRegionCreate':
	case 'cmdRegionUpdate':
		$iReturn = ERR_NO_ERROR;
		if ($us->UserId != 'root')
		{
			$iReturn = ERR_ACCESS_DENIED;
		}
		if ($iReturn > 0)
		{
			$RegionId = $_POST['RegionInfo']['RegionId'];
			$RegionCmd = $_POST['RegionInfo']['cmd'];

			$r = new DIRegionRecord($us, $RegionId);
			$iReturn = $r->setFromArray($_POST['RegionInfo']);
		}
		if ($iReturn > 0)
		{
			if ($r->get('RegionId') == '')
			{
				$r->set('RegionId', DIRegion::buildRegionId($r->get('CountryIso')));
			}
			$RegionId = $r->get('RegionId');
			if ($RegionCmd == 'cmdRegionCreate')
			{
				$iReturn = $r->insert();
				$r2 = new DIRegionDB($us, $RegionId);
				$r2->createRegionDB();
			}
			else
			{
				$iReturn = $r->update();
			}
		}
		if ($iReturn > 0)
		{
			// Set Role ADMINREGION in RegionAuth: master for this region
			$r->removeRegionUserAdmin();
			$iReturn = $us->setUserRole($_POST['RegionInfo']['RegionUserAdmin'], 
			                            $r->get('RegionId'),
			                            'ADMINREGION');
		}
		$answer = array();
		$answer['Status'] = $iReturn;
		$answer['RegionId'] = $r->get('RegionId');
		echo json_encode($answer);
	break;
	case 'dbzipimport' : 
		$answer = array();
		$iReturn = ERR_NO_ERROR;
		if ($us->UserId != 'root')
		{
			$iReturn = ERR_ACCESS_DENIED;
		}
		if ($iReturn > 0)
		{
			$RegionId = $_POST['RegionInfo']['RegionId'];
			// Use the parameters to create a new database from zip file...
			$Filename = TMP_DIR . '/di8file_' . $us->sSessionId . '_' . $_POST['RegionInfo']['Filename'];
			$iReturn = DIRegionDB::createRegionDBFromZip($us,
			             $_POST['RegionInfo']['Mode'],
			             $RegionId,
			             $_POST['RegionInfo']['RegionLabel'],
			             $Filename);
			if ($iReturn > 0)
			{
				$r = new DIRegion($us, $RegionId);
				if (DIRegion::existRegion($us, $RegionId) == STATUS_NO)
				{
					$r->insert();
				}
				else
				{
					$r->update();
				}
			}
		}
		$answer['Status'] = $iReturn;
		echo json_encode($answer);		
	break;
	case 'fileupload':
		// This command is called directly by SWFUpload, so be aware that it runs 
		// under his own session and cannot be debug in the browser.
		$iReturn = ERR_NO_ERROR;
		$answer = array('Filename' => '');
		if ($iReturn > 0)
		{
			if (! array_key_exists('Filedata', $_FILES))
			{
				// If $_FILES is empty, usually the PHP post_max_size parameter 
				// needs configuration in php.ini
				$iReturn = ERR_UPLOAD_FAILED;
			}
		}
		
		if ($iReturn > 0)
		{
			$answer['Filename'] = $_FILES['Filedata']['name'];
			$FilenameOld = $_FILES['Filedata']['tmp_name'];
			$Filename = TMP_DIR . '/di8file_' . $_POST['SessionId'] . '_' . $_FILES['Filedata']['name'];
			rename($FilenameOld, $Filename);		
			// Open ZIP File, extract info.xml and return values...
			$zip = new ZipArchive();
			$res = $zip->open($Filename);
			if ($res == TRUE)
			{
				$zip->extractTo(TMP_DIR, 'info.xml');
				$zip->close();
				$r = new DIRegion($us, '', TMP_DIR . '/info.xml');
				$info = array();
				$info['RegionId']    = $r->get('RegionId');
				$info['RegionLabel'] = $r->get('RegionLabel');
				$info['LangIsoCode'] = $r->get('LangIsoCode');
				$info['CountryIso']  = $r->get('CountryIso');
				$answer['Info'] = $info;
				$answer['DBExist'] = DIRegion::existRegion($us, $info['RegionId']);
			}
			else
			{
				$iReturn = ERR_UNKNOWN_ERROR;
			}
		}
		$answer['Status'] = $iReturn;
		// This command fileupload is called by swfupload directly so 
		// this session can be removed...
		$us->delete();
		echo json_encode($answer);
		// fb debug doesn't work in this code... why ?
		/*
		ob_start();
		print_r($answer);
		//print_r($_GET);
		print_r($_POST);
		//print_r($_FILES);
		//print_r($_SERVER);
		$out = ob_get_contents();
		ob_end_clean();		
		$fp = fopen('/tmp/fileupload.log', 'w+');
		fwrite($fp, $out);
		fclose($fp);
		*/		
	break;
	case 'start':
		$t->assign('lg', $lg);
		$t->assign('LanguageList', $us->q->loadLanguages(1));
		$listdb = $us->listDB();
		// unique database, choose than
		if (count($listdb) == 1)
		{
			$t->assign('option', 'r='. key($listdb));
		}
		else
		{
			$t->assign('option', 'cmd=main');
		}
		$t->display('main_start.tpl');
	break;
	case 'main':
		// Direct access returns a list of public regions on this server
		$t->assign('lg', $lg);
		$t->assign('LanguageList', $us->q->loadLanguages(1));
		$t->assign('CountryList', $us->q->getCountryList());
		$t->assign('regionlist', $us->listDB());
		$t->assign('ctl_noregion', true);
		$t->display('index.tpl');
	break;
	case 'listdb':
		// Direct access returns a list of public regions on this server
		$t->assign('regionlist', $us->listDB());
		$t->display('database_list.tpl');
	break;
	case 'searchdb':
		$searchdbquery = getParameter('searchdbquery', '');
		$searchbycountry = getParameter('searchbycountry', '');
		$reglst = $us->searchDB($searchdbquery, $searchbycountry);
		$RoleList = array();
		$t->assign('regionlist', $reglst);
		print json_encode(array('Status'     => 'OK', 
		                        'RoleList'   => $RoleList,
		                        'RegionList' => $reglst));
	break;
	case 'getCountryName':
		$CountryIso = getParameter('CountryIso','');
		$CountryName = $us->q->getCountryName($CountryIso);
		print $CountryName;
	break;
	case 'getRegionLogo':
		header('Content-type: Image/png');
		$murl = VAR_DIR . '/database/'. $RegionId . '/logo.png';
		if (!file_exists($murl))
		{
			$murl = 'images/di_logo.png';
		}
		readfile($murl);
		exit();
	break;
	case 'getRegionBasicInfo':
		$r = new DIRegion($us, $RegionId);
		$RegionInfo = array();
		$RegionInfo['RegionId'] = $RegionId;
		$a = $r->getDBInfo($lg);
		$a['NumDatacards'] = $us->q->getNumDisasterByStatus('PUBLISHED');
		$t->assign('RegionInfo', $a);
		$t->display('regionbasicinfo.tpl');
		break;
	case 'getRegionTechInfo':
		$r = new DIRegion($us, $RegionId);
		$RegionInfo = array();
		$RegionInfo['RegionId'] = $RegionId;
		$t->assign('RegionInfo', $r->getDBInfo($lg));
		$labels = $us->q->queryLabelsFromGroup('DB', $lg, false);
		$t->assign('Labels', $labels);
		$t->display('regiontechinfo.tpl');
	break;
	case 'getRegionInfo':
		$LangIsoCode = getParameter('LangIsoCode', $lg);
		$answer = array();
		$answer['Status'] = ERR_NO_ERROR;
		if (isset($RegionId) && $RegionId != '')
		{
			$t->assign('reg', $RegionId);
			$r = new DIRegion($us, $RegionId);
			$a = $r->getDBInfo($LangIsoCode);
			$a['NumDatacards'] = $us->q->getNumDisasterByStatus('PUBLISHED');
			$answer['RegionInfo'] = $a;
		}
		else
		{
			$answer['Status'] = ERR_NO_DATABASE;
			$answer['RegionInfo'] = array();
		}
		echo json_encode($answer);
	break;
	case 'getRegionFullInfo':
		if (isset($RegionId) && $RegionId != '')
		{
			$t->assign('reg', $RegionId);
			$r = new DIRegion($us, $RegionId);
			$a = $r->getDBInfo($lg);
			$a['NumDatacards'] = $us->q->getNumDisasterByStatus('PUBLISHED');
			$t->assign('RegionInfo', $a);
		}
		$labels = $us->q->queryLabelsFromGroup('DB', $lg, false);
		$t->assign('Labels', $labels);
		$t->display('regionfullinfo.tpl');
	break;
	case 'getRegionRecordCount' :
		$RecordCount = $us->getDisasterCount();
		echo json_encode(array('Status' => 'OK', 'RecordCount' => $RecordCount));
	break;
	case 'getGraphParameters':
		$t->display('graphparameters.tpl');
	break;
	case 'doDatabaseBackup':
		$BackupFileName = WWWDIR  . '/data/' . $SessionId . '/di8backup_' . $RegionId . '.zip';
		$BackupURL      = WWWDATA . '/data/' . $SessionId . '/di8backup_' . $RegionId . '.zip';
		$answer = array('Status'   => 'ERROR');
		$iReturn = DIRegion::createRegionBackup($us, $BackupFileName);
		if ($iReturn > 0)
		{
			$answer['Status'] = 'OK';
			$answer['BackupFileName'] = $BackupFileName;
			$answer['BackupURL'     ] = $BackupURL;
		}
		else
		{
			$answer['Status'] = 'ERROR';
		}
		echo json_encode($answer);
	break;
	case 'savequery':
	case 'cmdQuerySave':
		// Save XML file query
		fixPost($post);
		// Do not save _CMD...
		unset($post['_CMD']);
		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename=Query_' . str_replace(' ', '', $RegionId) . '.xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
		echo '<DIQuery />' . base64_encode(serialize($post));
		exit();
	break;
	default:
		// Open XML file query
		if (isset($_FILES['qry']))
		{
			// Open file, decode and assign saved query..
			$myfile = $_FILES['qry']['tmp_name'];
			$handle = fopen($myfile, 'r');
			$cq = fread($handle, filesize($myfile));
			fclose($handle);
			$xml = '<DIQuery />';
			$pos = strpos($cq, $xml);
			if (!empty($cq) &&  $pos != false)
			{
				$qy = substr($cq, $pos + strlen($xml));
				$qd = unserialize(base64_decode($qy));
			}
			else
			{
				exit();
			}
			$RegionId = $qd['_REG'];
			$t->assign('qd', $qd);
		}
		elseif (isset($get['r']) && !empty($get['r']))
		{
			$RegionId = $get['r'];
		}
		// 2009-08-07 (jhcaiced) Validate if Database Exists...
		if (!empty($RegionId) && file_exists($us->q->getDBFile($RegionId)))
		{
			// Accessing a region with some operation
			$us->open($RegionId);
			if (isset($get['lang']) && !empty($get['lang']))
			{
				$_SESSION['lang'] = $get['lang'];
			}
			// Direct access returns a list of public regions on this server
			$t->assign('LanguageList', $us->q->loadLanguages(1));
			$t->assign('CountryList', $us->q->getCountryList());
			switch ($get['cmd'])
			{
				case 'getGeoId':
					$code = $us->q->getObjectNameById($get['GeoCode'], 'GEOCODE');
					echo $code;
				break;
				case 'glist':
					$t->assign('reg', $get['GeographyId']);
					$t->assign('geol', $us->q->loadGeoChilds($get['GeographyId']));
					$t->display('main_glist.tpl');
				break;
				case 'geolst':
					$t->assign('geol', $us->q->loadGeography(0));
					$t->display('main_glist.tpl');
				break;
				case 'caulst':
					$t->assign('caupredl', $us->q->loadCauses('PREDEF', 'active', $lg));
					$t->assign('cauuserl', $us->q->loadCauses('USER', 'active', $lg));
					$t->display('main_causelist.tpl');
				break;
				case 'evelst':
					$t->assign('evepredl', $us->q->loadEvents('PREDEF', 'active', $lg));
					$t->assign('eveuserl', $us->q->loadEvents('USER', 'active', $lg));
					$t->display('main_eventlist.tpl');
				break;
				case 'test':
					// DesInventarInfo
					$t->assign('LanguageList', $us->q->loadLanguages(1));
					$t->assign('CountryList', $us->q->getCountryList());
					
					// Datacards
					$t->assign('LabelsDisaster', $us->q->queryLabelsFromGroup('Disaster', $lg));
					$t->assign('LabelsRecord1', $us->q->queryLabelsFromGroup('Record|1', $lg));
					$t->assign('LabelsEvent', $us->q->queryLabelsFromGroup('Event', $lg));
					$t->assign('LabelsCause', $us->q->queryLabelsFromGroup('Cause', $lg));

					// Query Design
					$t->assign('rc2', $us->q->queryLabelsFromGroup('Record|2', $lg));

					$t->display('test.tpl');
				break;
				default:
					// Update UserSession with Current Language.
					$us->update();
					// Get UserRole
					$role = $us->getUserRole($RegionId);
					$roleValue = $us->getUserRoleValue($RegionId);
					$r = new DIRegion($us, $RegionId);
					$RegionStatus = (int)$r->get('RegionStatus');
					$RegionPublic = $RegionStatus & 2;
					$bShow = 0;
					if ($RegionPublic > 0)
					{
						$bShow = 1;
					}
					else
					{
						if ($roleValue > 0)
						{
							$bShow = 1;
						}
					}
					if ($bShow > 0)
					{
						// Datacards
						$t->assign('LabelsDisaster', $us->q->queryLabelsFromGroup('Disaster', $lg));
						$t->assign('LabelsRecord1', $us->q->queryLabelsFromGroup('Record|1', $lg));
						$t->assign('LabelsEvent', $us->q->queryLabelsFromGroup('Event', $lg));
						$t->assign('LabelsCause', $us->q->queryLabelsFromGroup('Cause', $lg));

						// Query Design
						$t->assign('rc2', $us->q->queryLabelsFromGroup('Record|2', $lg));
						
						$t->assign('reg', $RegionId);
						//$t->assign('path', VAR_DIR);
						
						$t->assign('role', $role);
						if (strlen($role) > 0)
						{
							$t->assign('ctl_user', true);
						}
						else
						{
							$t->assign('ctl_user', false);
						}
						// Set selection map
						$t->assign('RegionLabel', $RegionLabel);
						$t->assign('ctl_showmap', true);
						// get range of dates
						$ydb = $us->getDateRange();
						$t->assign('yini', substr($ydb[0], 0, 4));
						$t->assign('yend', substr($ydb[1], 0, 4));

						// Load default list of Geography, Event, Cause
						$geol = $us->q->loadGeography(0);
						$glev = $us->q->loadGeoLevels('', -1, false);
						$evepredl = $us->q->loadEvents('PREDEF', 'active', $lg);
						$eveuserl = $us->q->loadEvents('USER', 'active', $lg);
						$caupredl = $us->q->loadCauses('PREDEF', 'active', $lg);
						$cauuserl = $us->q->loadCauses('USER', 'active', $lg);

						// In Saved Queries set true in Geo, Events, Causes selected..
						if (isset($qd['D_GeographyId']))
						{
							$gtree = $us->q->buildGeoTree('', 0, $us->q->getMaxGeoLev(), $qd['D_GeographyId']);
							$t->assign('gtree', $gtree);
							$geol = null;
						}

						if (isset($qd['D_EventId']))
						{
							foreach ($qd['D_EventId'] as $ky=>$it)
							{
								if (isset($evepredl[$it]))
								{
									$evepredl[$it][3] = 1;
								}
								if (isset($eveuserl[$it]))
								{
									$eveuserl[$it][3] = 1;
								}
							}
						}

						if (isset($qd['D_CauseId']))
						{
							foreach ($qd['D_CauseId'] as $ky=>$it)
							{
								if (isset($caupredl[$it]))
								{
									$caupredl[$it][3] = 1;
								}
								if (isset($cauuserl[$it]))
								{
									$cauuserl[$it][3] = 1;
								}
							}
						}
						// List of elements: Geography, GLevels, Events, Causes..
						$t->assign('geol', $geol);
						$t->assign('glev', $glev);
						$t->assign('evepredl', $evepredl);
						$t->assign('eveuserl', $eveuserl);
						$t->assign('caupredl', $caupredl);
						$t->assign('cauuserl', $cauuserl);
						// Query words and phrases in dictionary..
						$ef1 = $us->q->queryLabelsFromGroup('Effect|People', $lg);
						$ef2 = $us->q->queryLabelsFromGroup('Effect|Affected', $lg);
						$ef3 = $us->q->queryLabelsFromGroup('Effect|Economic', $lg);
						$ef4 = $us->q->queryLabelsFromGroup('Effect|More', $lg);
						$sec = $us->q->queryLabelsFromGroup('Sector', $lg);
						// Add some fields to customize lists ??
						//$ef1['EffectFarmingAndForest'] = $ef2['EffectFarmingAndForest'];
						//$ef1['EffectLiveStock'] = $ef2['EffectLiveStock'];
						//$ef1['EffectRoads'] = $ef2['EffectRoads'];
						//$ef1['EffectEducationCenters'] = $ef2['EffectEducationCenters'];
						//$ef1['EffectMedicalCenters'] = $ef2['EffectMedicalCenters'];

						$sec['SectorTransport'][3] 		= null; //array('EffectRoads' => $ef2['EffectRoads'][0]);
						$sec['SectorCommunications'][3] = null;
						$sec['SectorRelief'][3] 		= null;
						$sec['SectorAgricultural'][3] 	= null; //array('EffectFarmingAndForest' => $ef2['EffectFarmingAndForest'][0],
																//'EffectLiveStock' => $ef2['EffectLiveStock'][0]);
						$sec['SectorWaterSupply'][3] 	= null;
						$sec['SectorSewerage'][3]		= null;
						$sec['SectorEducation'][3]		= null; //array('EffectEducationCenters' => $ef2['EffectEducationCenters'][0]);
						$sec['SectorPower'][3]			= null;
						$sec['SectorIndustry'][3]		= null;
						$sec['SectorHealth'][3]			= null; //array('EffectMedicalCenters' => $ef2['EffectMedicalCenters'][0]);
						$sec['SectorOther'][3]			= null;
						$dic = array();
						$dic = array_merge($dic, $us->q->queryLabelsFromGroup('MapOpt', $lg));
						$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Graph', $lg));
						$dic = array_merge($dic, $ef1);
						$dic = array_merge($dic, $ef2);
						$dic = array_merge($dic, $ef3);
						$dic = array_merge($dic, $sec);
						$t->assign('dic', $dic);
						$t->assign('ef1', $ef1);
						$t->assign('ef2', $ef2);
						$t->assign('ef3', $ef3);
						$t->assign('ef4', $ef4);
						$t->assign('sec', $sec);
						// DATA
						$dc2 = array();
						$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Disaster', $lg));
						$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Record|2', $lg));
						$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Geography', $lg));
						$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Event', $lg));
						$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Cause', $lg));
						$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Effect', $lg));
						$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Sector', $lg));
						$t->assign('dc2', $dc2);
						$fld = 'DisasterSerial,DisasterBeginTime,EventName,GeographyFQName,DisasterSiteNotes,'.
							'DisasterSource,EffectNotes,EffectPeopleDead,EffectPeopleMissing,EffectPeopleInjured,'.
							'EffectPeopleHarmed,EffectPeopleAffected,EffectPeopleEvacuated,EffectPeopleRelocated,'.
							'EffectHousesDestroyed,EffectHousesAffected,EffectFarmingAndForest,EffectRoads,'.
							'EffectEducationCenters,EffectMedicalCenters,EffectLiveStock,EffectLossesValueLocal,'.
							'EffectLossesValueUSD,EffectOtherLosses,SectorTransport,SectorCommunications,SectorRelief,'.
							'SectorAgricultural,SectorWaterSupply,SectorSewerage,SectorEducation,SectorPower,SectorIndustry,'.
							'SectorHealth,SectorOther,EventDuration,EventMagnitude,CauseName,CauseNotes';
						$sda = explode(',', $fld);
						$t->assign('sda', $sda);
						$sda1 = explode(',', 'GeographyCode,DisasterLatitude,DisasterLongitude,RecordAuthor,RecordCreation,RecordUpdate,EventNotes');
						$t->assign('sda1', $sda1);	// array_diff_key($dc2, array_flip($sda))
						// MAPS
						$mgl = $us->q->loadGeoLevels('', -1, true);
						$t->assign('mgel', $mgl);
						$range[] = array(     10, '1 - 10'          , 'ffff99');
						$range[] = array(    100, '11 - 100'        , 'ffff00');
						$range[] = array(   1000, '101 - 1000'      , 'ffcc00');
						$range[] = array(  10000, '1001 - 10000'    , 'ff6600');
						$range[] = array( 100000, '10001 - 100000'  , 'cc0000');
						$range[] = array(1000000, '100001 - 1000000', '660000');
						$range[] = array(''     , '1000001 ->'      , '000000');
						$t->assign('range', $range);
						// STATISTIC
						$st = array();
						foreach ($us->q->loadGeoLevels('', -1, false) as $k=>$i)
							$st['StatisticGeographyId_'. $k] = array($i[0], $i[1]);
						$std = array();
						$std = array_merge($std, $us->q->queryLabelsFromGroup('Statistic', $lg));
						$std = array_merge($std, $st);
						$t->assign('std', $std);

						/* DATACARDS */
						$t->assign('usr', $us->UserId);
						$desinventarUserRole = $role;
						$desinventarUserRoleValue = $roleValue;
						// Validate if user has permission to access database
						$dic = $us->q->queryLabelsFromGroup('DB', $lg);
						switch ($desinventarUserRole)
						{
							case 'ADMINREGION':
								$t->assign('showconfig', true);
								$dicrole = $dic['DBRoleAdmin'][0];
							break;
							case 'OBSERVER':
								$t->assign('showconfig', true);
								$t->assign('ro', 'disabled');
								$dicrole = $dic['DBRoleObserver'][0];
							break;
							case 'SUPERVISOR':
								$dicrole = $dic['DBRoleSupervisor'][0];
							break;
							case 'USER':
								$dicrole = $dic['DBRoleUser'][0];
							break;
							default:
								$dicrole = null;
							break;
						}
						$t->assign('dicrole', $dicrole);
						$t->assign('ctl_effects', true);
						$dis = $us->q->queryLabelsFromGroup('Disaster', $lg);
						$dis = array_merge($dis, $us->q->queryLabelsFromGroup('Geography', $lg));
						$t->assign('dis', $dis);
						$t->assign('rc1', $us->q->queryLabelsFromGroup('Record|1', $lg));
						$t->assign('rc2', $us->q->queryLabelsFromGroup('Record|2', $lg));
						$t->assign('eve', $us->q->queryLabelsFromGroup('Event', $lg));
						$t->assign('cau', $us->q->queryLabelsFromGroup('Cause', $lg));
						$sc3 = $us->q->querySecLabelFromGroup('Effect|Affected', $lg);
						$t->assign('sc3', $sc3);
						$t->assign('dmg', $us->q->queryLabelsFromGroup('MetGuide', $lg));
						
						// Geography Levels
						$GeoLevelList = $us->getGeoLevels();
						$t->assign('GeoLevelList', $GeoLevelList);
						
						$lev = 0;
						$t->assign('lev', $lev);
						$t->assign('levmax', $us->q->getMaxGeoLev());
						$t->assign('levname', $us->q->loadGeoLevById($lev));
						$t->assign('geol', $us->q->loadGeography($lev));
						$gItems = $us->getGeographyItemsByLevel(0, '');
						$t->assign('GeoLevelItems', $gItems);
						$t->assign('EventList', $us->q->loadEvents(null, 'active', $lg));
						$t->assign('CauseList', $us->q->loadCauses(null, 'active', $lg));
						$EEFieldList = $us->q->getEEFieldList('True');
						$t->assign('EEFieldList', $EEFieldList);
						$t->assign('RegionId', $RegionId);
						$t->assign('desinventarUserRole', $desinventarUserRole);
						$t->assign('desinventarUserRoleValue', $desinventarUserRoleValue);
						/* DATACARDS END */
						
						/* BEGIN THEMATIC MAP */
						// 2010-01-18 (jhcaiced) Windows machines doesn't use remote servers
						if (isset($_SERVER['WINDIR']))
						{
							$desinventarHasInternet = 0;
						}
						else
						{
							// Linux machines are assumed to be connected to internet
							$desinventarHasInternet = 1;
							/*
							if (!fsockopen('www.google.com',80))
							{
								$desinventarHasInternet = 0;
							}
							*/
						}	
						// 2009-07-14 (jhcaiced) Configure desinventarGoogleMapsKey
						$desinventarGoogleMapsKey = '';
						switch($_SERVER['SERVER_NAME'])
						{
							case 'devel.desinventar.org':
								$desinventarGoogleMapsKey = 'ABQIAAAALchGiIjlsbdmE3fN4eRcYBQB70apFGkcE_JIKPq7c7oktNLHXhTU2xdzBNS_-XzWYh911SdinR2Xkw';
								break;
							case 'online.desinventar.org':
								$desinventarGoogleMapsKey = 'ABQIAAAAv_HCDVf4YK_pJceWBA7XmRQHPIpdtLPiHEY9M3_iWXAS0AXQLhTwoORtm0ZLuqG03CB3sP09KKDtAg';		
								break;
							/*
							case '192.168.0.13':
								$desinventarGoogleMapsKey = 'ABQIAAAAv_HCDVf4YK_pJceWBA7XmRRT41YKyiJ82KgcK-Dai8T6I93cWxT4pcci6xQX6tWCkefVHbB2AtUGKw';
								break;
							*/
							case 'localhost':
								$desinventarGoogleMapsKey = 'ABQIAAAAv_HCDVf4YK_pJceWBA7XmRT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQrE9s8Pd9b8nrmaDwyyilebSXcPw';
								break;
							case '127.0.0.1':
								$desinventarGoogleMapsKey = 'ABQIAAAAv_HCDVf4YK_pJceWBA7XmRRi_j0U6kJrkFvY4-OX2XYmEAa76BSA4JvNpGUXBDLtWrA-lnRXmTahHg';
								break;
							default:
								$desinventarGoogleMapsKey = '';
								break;
						}
						$t->assign('desinventarGoogleMapsKey', $desinventarGoogleMapsKey);
						$t->assign('desinventarHasInternet', $desinventarHasInternet);
						$t->display('index.tpl');
					}
					else
					{
						$t->display('database_header.tpl');
						// Private Database, do not show anything...
						print 'PRIVATE DATABASE <br />' . "\n";
					}					
				break;
			} // switch
		}
	break;
} //switch($cmd)
</script>
