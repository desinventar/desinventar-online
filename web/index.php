<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

//ob_start( 'ob_gzhandler' );
require_once('include/loader.php');
$post = $_POST;
$get  = $_GET;

$cmd = getParameter('cmd','');
$r = getParameter('r','');
if ($cmd == '') {
	if ($r == '') {
		$cmd = 'listdb';
	}
}
fb('Command : ' . $cmd);
fb('Region  : ' . $r);

// Default Template Values
$t->assign('request_uri', $_SERVER['REQUEST_URI']);

switch ($cmd) {
case 'listdb':
	// Direct access returns a list of public regions on this server
	$d = new Query();
	$reglst = $d->searchDB();
	$t->assign('ctl_showregionlist', true);
	$t->assign('regionlist', $reglst);
	$t->display('portal.tpl');
	break;
case 'searchdb':
	$d = new Query();
	$searchdbquery = getParameter('searchdbquery', '');
	$reglst = $d->searchDB($searchdbquery);
	$t->assign('ctl_showregionlist', true);
	$t->assign('regionlist', $reglst);
	$t->display('regionlist.tpl');
	break;
default:
	if (isset($get['r']) && !empty($get['r'])) {
		$reg = $get['r'];
	} elseif (isset($post) && !empty($post['_REG'])) {
		// Request to save Query Design in File..
		fixPost($post);
		header("Content-type: text/xml");
		header("Content-Disposition: attachment; filename=Query_". str_replace(" ", "", $post['_REG']) .".xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
		echo "<DIQuery />". base64_encode(serialize($post));
		exit();
	} elseif (isset($_FILES['qry'])) {
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
		$reg = $qd['_REG'];
		$t->assign ("qd", $qd);
	}
	// 2009-08-07 (jhcaiced) Validate if Database Exists...
	if (!empty($reg) && file_exists($us->q->getDBFile($reg))) {
		// Accessing a region with some operation
		$us->open($reg);
		$q = new Query($reg);
		if (isset($get['lang']) && !empty($get['lang']))
			$_SESSION['lang'] = $get['lang'];
		if (isset($get['cmd'])) {
			switch ($get['cmd']) {
				case "getGeoId":
					$code = $q->getObjectNameById($get['GeoCode'], "GEOCODE");
					echo "$code";
				break;
				case "glist":
					$t->assign ("reg", $get['GeographyId']);
					$t->assign ("geol", $q->loadGeoChilds($get['GeographyId']));
					$t->assign ("ctl_glist", true);
				break;
				case "levlst":
					$t->assign ("glev", $q->loadGeoLevels('', -1, false));
					$t->assign ("ctl_levlst", true);
				break;
				case "geolst":
					$t->assign ("geol", $q->loadGeography(0));
					$t->assign ("ctl_glist", true);
				break;
				case "caulst":
					$t->assign ("caupredl", $q->loadCauses("PREDEF", "active", $lg));
					$t->assign ("cauuserl", $q->loadCauses("USER", "active", $lg));
					$t->assign ("ctl_caulst", true);
				break;
				case "evelst":
					$t->assign ("evepredl", $q->loadEvents("PREDEF", "active", $lg));
					$t->assign ("eveuserl", $q->loadEvents("USER", "active", $lg));
					$t->assign ("ctl_evelst", true);
				break;
			}
		} else {
			$t->assign ("ms", MAPSERV);
			$t->assign ("dis", $q->queryLabelsFromGroup('Disaster', $lg));
			$t->assign ("rc1", $q->queryLabelsFromGroup('Record|1', $lg));
			$t->assign ("rc2", $q->queryLabelsFromGroup('Record|2', $lg));
			$t->assign ("eve", $q->queryLabelsFromGroup('Event', $lg));
			$t->assign ("cau", $q->queryLabelsFromGroup('Cause', $lg));
			$t->assign ("ctl_glist", true);
			$t->assign ("reg", $reg);
			$t->assign ("path", VAR_DIR);
			$t->assign ("exteffel", $q->getEEFieldList("True"));
			// Get UserRole
			$role = $us->getUserRole($reg);
			$t->assign ("role", $role);
			//echo "<pre>". $us->UserId ; print_r($role);
			if (strlen($role) > 0)
				$t->assign ("ctl_user", true);
			else
				$t->assign ("ctl_user", false);
			// Set selection map
			$regname = $q->getDBInfoValue('RegionLabel');
			$t->assign ("regname", $regname);
			//  if (testMap(VAR_DIR . "/". $reg . "/". $data))
			$t->assign ("ctl_showmap", true);
			// get range of dates
			$ydb = $q->getDateRange();
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
			$t->assign ("geol", $q->loadGeography(0));
			$t->assign ("glev", $q->loadGeoLevels('', -1, false));
			$t->assign ("evepredl", $q->loadEvents("PREDEF", "active", $lg));
			$t->assign ("eveuserl", $q->loadEvents("USER", "active", $lg));
			$t->assign ("caupredl", $q->loadCauses("PREDEF", "active", $lg));
			$t->assign ("cauuserl", $q->loadCauses("USER", "active", $lg));
			// Query words and phrases in dictionary..
			$ef1 = $q->queryLabelsFromGroup('Effect|People', $lg);
			$ef2 = $q->queryLabelsFromGroup('Effect|Affected', $lg);
			$ef3 = $q->queryLabelsFromGroup('Effect|Economic', $lg);
			$sec = $q->queryLabelsFromGroup('Sector', $lg);
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
			$dic = array_merge($dic, $q->queryLabelsFromGroup('MapOpt', $lg));
			$dic = array_merge($dic, $q->queryLabelsFromGroup('Graph', $lg));
			$dic = array_merge($dic, $ef1);
			$dic = array_merge($dic, $ef2);
			$dic = array_merge($dic, $ef3);
			$dic = array_merge($dic, $sec);
			$t->assign ("dic", $dic);
			$t->assign ("ef1", $ef1);
			$t->assign ("ef2", $ef2);
			$t->assign ("ef3", $ef3);
			$t->assign ("sec", $sec);
			$t->assign ("ef4", $q->queryLabelsFromGroup('Effect|More', $lg));
			// DATA
			$dc2 = array();
			$dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Disaster', $lg));
			$dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Record|2', $lg));
			$dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Geography', $lg));
			$dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Event', $lg));
			$dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Cause', $lg));
			$dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Effect', $lg));
			$dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Sector', $lg));
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
			$mgl = $q->loadGeoLevels('', -1, true);
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
			foreach ($ef1 as $k=>$i) {
				$sst[$k] = array($k."Q|>|-1", $i[0]);
				$nst[$k] = $sst[$k];
			}
			foreach ($ef2 as $k=>$i)
				$nst[$k] = array("$k|>|-1", $i[0]);
			foreach ($ef3 as $k=>$i)
				$nst[$k] = array("$k|>|-1", $i[0]);
			$sst1 = array_diff_key($nst, array_flip(array_keys($sst)));
			$t->assign ("sst1", $sst1);
			$t->assign ("sst", $sst);
			$st = array();
			foreach ($q->loadGeoLevels('', -1, false) as $k=>$i) {
				$st["StatisticGeographyId_". $k] = array($i[0], $i[1]);
			}
			$std = array();
			$std = array_merge($std, $q->queryLabelsFromGroup('Statistic', $lg));
			$std = array_merge($std, $st);
			$t->assign ("std", $std);
			$t->assign ("ctl_show", true);
			$t->assign ("ctl_qryres", true);
			$t->assign ("ctl_qrydsg", true);
		}

		$t->assign ("ctl_show", true);
		// Direct access returns a list of public regions on this server
		$t->assign ("lglst", $us->q->loadLanguages(1));
		$t->assign ("userid", $us->UserId);
		$t->display ("index.tpl");
	}
	break;
} //switch($cmd)
</script>
