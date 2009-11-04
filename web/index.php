<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

// This is the version of the software
define('VERSION', '8.2.0.60');

//ob_start( 'ob_gzhandler' );
require_once('include/loader.php');
require_once('include/diregion.class.php');

$post = $_POST;
$get  = $_GET;

$cmd = getParameter('cmd','');
$RegionId = getParameter('r', getParameter('RegionId', ''));
if (isset($post['_REG']) && !empty($post['_REG']))
	$RegionId = $post['_REG'];
elseif ($cmd == '' && $RegionId == '')
	$cmd = 'start';

// Default Template Values
$t->assign('request_uri', $_SERVER['REQUEST_URI']);
$t->assign('version', VERSION);

if (!empty($RegionId))
	$us->open($RegionId);

switch ($cmd) {
	case 'getversion':
		print VERSION;
		break;
	case 'start':
		$d = new Query();
		$t->assign('lg', $lg);
		$t->assign("lglst", $d->loadLanguages(1));
		$listdb = $d->listDB();
		// unique database, choose than
		if (count($listdb) == 1) {
			$t->assign('option', "r=". key($listdb));
		}
		else
			$t->assign('option', "cmd=main");
		$t->assign('ctl_start', true);
		$t->display('index.tpl');
		break;
	case 'main':
		// Direct access returns a list of public regions on this server
		$d = new Query();
		$t->assign('lg', $lg);
		$t->assign("lglst", $d->loadLanguages(1));
		$t->assign('regionlist', $d->listDB());
		$t->assign("userid", $us->UserId);
		$t->assign("ctl_noregion", true);
		$t->assign('ctl_mainpage', true);
		$t->display('index.tpl');
		break;
	case 'listdb':
		// Direct access returns a list of public regions on this server
		$d = new Query();
		$t->assign('regionlist', $d->listDB());
		$t->assign('ctl_showlistdb', true);
		$t->display('index.tpl');
		break;
	case 'searchdb':
		$d = new Query();
		$searchdbquery = getParameter('searchdbquery', '');
		$searchbycountry = getParameter('searchbycountry', '');
		$reglst = $d->searchDB($searchdbquery, $searchbycountry);
		$t->assign('ctl_showregionlist', true);
		$t->assign('regionlist', $reglst);
		print json_encode($reglst);
		break;
	case 'getCountryName':
		$d = new Query();
		$CountryIso = getParameter('CountryIso','');
		$CountryName = $d->getCountryName($CountryIso);
		print $CountryName;
		break;
	case 'getRegionLogo':
		header("Content-type: Image/png");
		$murl = VAR_DIR . "/database/". $RegionId . "/logo.png";
		if (!file_exists($murl))
			$murl = "images/di_logo.png";
		readfile($murl);
		exit();
		break;
	case 'getRegionBasicInfo':
		$r = new DIRegion($us, $RegionId);
		$RegionInfo = array();
		$RegionInfo['RegionId'] = $RegionId;
		$a = $r->getDBInfo();
		$a['NumDatacards'] = $us->q->getNumDisasterByStatus('PUBLISHED');
		$t->assign('RegionInfo', $a);
		$t->display('regionbasicinfo.tpl');
		break;
	case 'getRegionTechInfo':
		$r = new DIRegion($us, $RegionId);
		$RegionInfo = array();
		$RegionInfo['RegionId'] = $RegionId;
		$t->assign('RegionInfo', $r->getDBInfo());
		$labels = $us->q->queryLabelsFromGroup('DB', $lg, false);
		$t->assign ('Labels', $labels);
		$t->display('regiontechinfo.tpl');
		break;
	case 'getRegionFullInfo':
		$t->assign('reg', $RegionId);
		$r = new DIRegion($us, $RegionId);
		$a = $r->getDBInfo();
		$a['NumDatacards'] = $us->q->getNumDisasterByStatus('PUBLISHED');
		$t->assign('RegionInfo', $a);
		$labels = $us->q->queryLabelsFromGroup('DB', $lg, false);
		$t->assign ('Labels', $labels);
		$t->assign ('ctl_showRegionInfo', true);
		$t->display('index.tpl');
		break;
	case 'getGraphParameters':
		$t->display('graphparameters.tpl');
		break;
	case "getRegionBackup":
		$FileName = TMP_DIR . '/di8backup_' . uuid() . '.zip';
		$iReturn = DIRegion::createRegionBackup($us, $RegionId, $FileName);
		if ($iReturn > 0) {
			header("Content-type: application/x-zip-compressed");
			header("Content-Disposition: attachment; filename=". $RegionId .".zip");
			flush();
			readfile($FileName);
			unlink($FileName);
		}
	break;
	default:
		if (isset($get['r']) && !empty($get['r'])) {
			$RegionId = $get['r'];
			$us->open($RegionId);
		}
		elseif (isset($post['_CMD']) && $post['_CMD'] == "savequery") {
			// Request to save Query Design in File..
			fixPost($post);
			header("Content-type: text/xml");
			header("Content-Disposition: attachment; filename=Query_". str_replace(" ", "", $RegionId) .".xml");
			echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
			echo "<DIQuery />". base64_encode(serialize($post));
			exit();
		}
		elseif (isset($_FILES['qry'])) {
			// Open file, decode and assign saved query..
			$myfile = $_FILES['qry']['tmp_name'];
			$handle = fopen($myfile, "r");
			$cq = fread($handle, filesize($myfile));
			fclose($handle);
			$xml = '<DIQuery />';
			$pos = strpos($cq, $xml);
			if (!empty($cq) &&  $pos != false) {			
				$qy = substr($cq, $pos + strlen($xml));
				$qd = unserialize(base64_decode($qy));
			}
			else
				exit();
			$RegionId = $qd['_REG'];
			$t->assign ("qd", $qd);
		}
		// 2009-08-07 (jhcaiced) Validate if Database Exists...
		if (!empty($RegionId) && file_exists($us->q->getDBFile($RegionId))) {
			// Accessing a region with some operation
			$us->open($RegionId);
			//$q = new Query($RegionId);
			if (isset($get['lang']) && !empty($get['lang']))
				$_SESSION['lang'] = $get['lang'];
			if (isset($get['cmd'])) {
				switch ($get['cmd']) {
					case "getGeoId":
						$code = $us->q->getObjectNameById($get['GeoCode'], "GEOCODE");
						echo "$code";
					break;
					case "glist":
						$t->assign ("reg", $get['GeographyId']);
						$t->assign ("geol", $us->q->loadGeoChilds($get['GeographyId']));
						$t->assign ("ctl_glist", true);
					break;
					case "levlst":
						$t->assign ("glev", $us->q->loadGeoLevels('', -1, false));
						$t->assign ("ctl_levlst", true);
					break;
					case "geolst":
						$t->assign ("geol", $us->q->loadGeography(0));
						$t->assign ("ctl_glist", true);
					break;
					case "caulst":
						$t->assign ("caupredl", $us->q->loadCauses("PREDEF", "active", $lg));
						$t->assign ("cauuserl", $us->q->loadCauses("USER", "active", $lg));
						$t->assign ("ctl_caulst", true);
					break;
					case "evelst":
						$t->assign ("evepredl", $us->q->loadEvents("PREDEF", "active", $lg));
						$t->assign ("eveuserl", $us->q->loadEvents("USER", "active", $lg));
						$t->assign ("ctl_evelst", true);
					break;
				} //switch
			}
			else {
				$t->assign ("ms", MAPSERV);
				$t->assign ("dis", $us->q->queryLabelsFromGroup('Disaster', $lg));
				$t->assign ("rc1", $us->q->queryLabelsFromGroup('Record|1', $lg));
				$t->assign ("rc2", $us->q->queryLabelsFromGroup('Record|2', $lg));
				$t->assign ("eve", $us->q->queryLabelsFromGroup('Event', $lg));
				$t->assign ("cau", $us->q->queryLabelsFromGroup('Cause', $lg));
				$t->assign ("ctl_glist", true);
				$t->assign ("reg", $RegionId);
				$t->assign ("path", VAR_DIR);
				$t->assign ("exteffel", $us->q->getEEFieldList("True"));
				// Get UserRole
				$role = $us->getUserRole($RegionId);
				$t->assign ("role", $role);
				if (strlen($role) > 0)
					$t->assign ("ctl_user", true);
				else
					$t->assign ("ctl_user", false);
				// Set selection map
				$regname = $us->q->getDBInfoValue('RegionLabel');
				$t->assign ("regname", $regname);
				//  if (testMap(VAR_DIR . "/". $RegionId . "/". $data))
				$t->assign ("ctl_showmap", true);
				// get range of dates
				$ydb = $us->q->getDateRange();
				$t->assign ("yini", substr($ydb[0], 0, 4));
				$t->assign ("yend", substr($ydb[1], 0, 4));
				// In Saved Queries set true in Geo, Events, Causes selected..
				if (isset($qd["D_GeographyId"])) {
					foreach ($qd["D_GeographyId"] as $ky=>$it) {
						if (isset($geol[$it]))
							$geol[$it][3] = 1;
					}
				}
				if (isset($qd["D_EventId"])) {
					foreach ($qd["D_EventId"] as $ky=>$it) {
						if (isset($evepredl[$it]))
							$evepredl[$it][3] = 1;
						if (isset($eveuserl[$it]))
							$eveuserl[$it][3] = 1;
					}
				}
				if (isset($qd["D_CauseId"])) {
					foreach ($qd["D_CauseId"] as $ky=>$it) {
						if (isset($caupredl[$it]))
							$caupredl[$it][3] = 1;
						if (isset($cauuserl[$it]))
							$cauuserl[$it][3] = 1;
					}
				}
				// List of elements: Geography, GLevels, Events, Causes..
				$t->assign ("geol", $us->q->loadGeography(0));
				$t->assign ("glev", $us->q->loadGeoLevels('', -1, false));
				$t->assign ("evepredl", $us->q->loadEvents("PREDEF", "active", $lg));
				$t->assign ("eveuserl", $us->q->loadEvents("USER", "active", $lg));
				$t->assign ("caupredl", $us->q->loadCauses("PREDEF", "active", $lg));
				$t->assign ("cauuserl", $us->q->loadCauses("USER", "active", $lg));
				// Query words and phrases in dictionary..
				$ef1 = $us->q->queryLabelsFromGroup('Effect|People', $lg);
				$ef2 = $us->q->queryLabelsFromGroup('Effect|Affected', $lg);
				$ef3 = $us->q->queryLabelsFromGroup('Effect|Economic', $lg);
				$sec = $us->q->queryLabelsFromGroup('Sector', $lg);
				$sec['SectorTransport'][3] 		= array('EffectRoads' => $ef2['EffectRoads'][0]);
				$sec['SectorCommunications'][3] = null;
				$sec['SectorRelief'][3] 		= null;
				$sec['SectorAgricultural'][3] 	= array('EffectFarmingAndForest' => $ef2['EffectFarmingAndForest'][0],
													'EffectLiveStock' => $ef2['EffectLiveStock'][0]);
				$sec['SectorWaterSupply'][3] 	= null;
				$sec['SectorSewerage'][3]		= null;
				$sec['SectorEducation'][3]		= array('EffectEducationCenters' => $ef2['EffectEducationCenters'][0]);
				$sec['SectorPower'][3]			= null;
				$sec['SectorIndustry'][3]		= null;
				$sec['SectorHealth'][3]			= array('EffectMedicalCenters' => $ef2['EffectMedicalCenters'][0]);
				$sec['SectorOther'][3]			= null;
				$dic = array();
				$dic = array_merge($dic, $us->q->queryLabelsFromGroup('MapOpt', $lg));
				$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Graph', $lg));
				$dic = array_merge($dic, $ef1);
				$dic = array_merge($dic, $ef2);
				$dic = array_merge($dic, $ef3);
				$dic = array_merge($dic, $sec);
				$t->assign ("dic", $dic);
				$t->assign ("ef1", $ef1);
				$t->assign ("ef2", $ef2);
				$t->assign ("ef3", $ef3);
				$t->assign ("sec", $sec);
				$t->assign ("ef4", $us->q->queryLabelsFromGroup('Effect|More', $lg));
				// DATA
				$dc2 = array();
				$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Disaster', $lg));
				$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Record|2', $lg));
				$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Geography', $lg));
				$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Event', $lg));
				$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Cause', $lg));
				$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Effect', $lg));
				$dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Sector', $lg));
				$t->assign ("dc2", $dc2);
				$fld = "DisasterSerial,DisasterBeginTime,EventName,GeographyFQName,DisasterSiteNotes,".
					"DisasterSource,EffectNotes,EffectPeopleDead,EffectPeopleMissing,EffectPeopleInjured,".
					"EffectPeopleHarmed,EffectPeopleAffected,EffectPeopleEvacuated,EffectPeopleRelocated,".
					"EffectHousesDestroyed,EffectHousesAffected,EffectFarmingAndForest,EffectRoads,".
					"EffectEducationCenters,EffectMedicalCenters,EffectLiveStock,EffectLossesValueLocal,".
					"EffectLossesValueUSD,EffectOtherLosses,SectorTransport,SectorCommunications,SectorRelief,".
					"SectorAgricultural,SectorWaterSupply,SectorSewerage,SectorEducation,SectorPower,SectorIndustry,".
					"SectorHealth,SectorOther,EventDuration,EventMagnitude,CauseName,CauseNotes";
				$sda = explode(",", $fld);
				$t->assign ("sda", $sda);
				$sda1 = explode(",",
				"GeographyCode,DisasterLatitude,DisasterLongitude,RecordAuthor,RecordCreation,RecordUpdate,EventNotes");
				$t->assign ("sda1", $sda1);	// array_diff_key($dc2, array_flip($sda))
				// MAPS
				$mgl = $us->q->loadGeoLevels('', -1, true);
				$t->assign ("mgel", $mgl);
				$range[] = array(10, "1 - 10", "ffff99");
				$range[] = array(100, "11 - 100", "ffff00");
				$range[] = array(1000, "101 - 1000", "ffcc00");
				$range[] = array(10000, "1001 - 10000", "ff6600");
				$range[] = array(100000, "10001 - 100000", "cc0000");
				$range[] = array(1000000, "100001 - 1000000", "660000");
				$range[] = array('',       "1000001 ->",        "000000");
				$t->assign ("range", $range);
				// STATISTIC
				$st = array();
				foreach ($us->q->loadGeoLevels('', -1, false) as $k=>$i)
					$st["StatisticGeographyId_". $k] = array($i[0], $i[1]);
				$std = array();
				$std = array_merge($std, $us->q->queryLabelsFromGroup('Statistic', $lg));
				$std = array_merge($std, $st);
				$t->assign ("std", $std);
				$t->assign ("ctl_show", true);
				$t->assign ("ctl_qryres", true);
				$t->assign ("ctl_qrydsg", true);
			}
			// Direct access returns a list of public regions on this server
			$t->assign ("lglst", $us->q->loadLanguages(1));
			$t->assign ("userid", $us->UserId);
			$t->display ("index.tpl");
		}
	break;
} //switch($cmd)
</script>
