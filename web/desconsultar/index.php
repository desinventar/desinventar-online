<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

//ob_start( 'ob_gzhandler' );
require_once('../include/loader.php');
require_once('../include/usersession.class.php');

$post = $_POST;
$get  = $_GET;

// Direct access to module
if (isset($get['r']) && !empty($get['r']))
	$reg = $get['r'];
// Request to save Query Design in File..
elseif (isset($post) && !empty($post['_REG'])) {
	fixPost($post);
	header("Content-type: text/xml");
	header("Content-Disposition: attachment; filename=Query_". str_replace(" ", "", $post['_REG']) .".xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
	echo "<DIQuery />". base64_encode(serialize($post));
	exit();
}
// Open file, decode and assign saved query..
elseif (isset($_FILES['qry'])) {
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
else
	$reg = $us->sRegionId;

// Direct Acccess Not allowed, do not show anything...
if (!empty($reg)) {
	$us->open($reg);
	$q = new Query($reg);
	if (isset($get['lang']) && !empty($get['lang']))
		$_SESSION['lang'] = $get['lang'];
}
else
	exit();

// 2009-08-07 (jhcaiced) Validate if Database Exists...
if (! file_exists($us->q->getDBFile($reg))) {
	print "<h3>Requested Region doesn't exist</h3><br />";
	exit();
}

// Display Geographic list of childs..
if (isset($get['cmd'])) {
  if ($get['cmd'] == "getGeoId") {
    $code = $q->getObjectNameById($get['GeoCode'], "GEOCODE");
    echo "$code";
  }
  // Display Geographic list of childs..
  elseif ($get['cmd'] == "glist") {
    $t->assign ("reg", $get['GeographyId']);
    $t->assign ("geol", $q->loadGeoChilds($get['GeographyId']));
    $t->assign ("ctl_glist", true);
  }
}
else {
  $t->assign ("ms", MAPSERV);
  $t->assign ("dis", $q->queryLabelsFromGroup('Disaster', $lg));
  $t->assign ("rc1", $q->queryLabelsFromGroup('Record|1', $lg));
  $t->assign ("rc2", $q->queryLabelsFromGroup('Record|2', $lg));
  $t->assign ("eve", $q->queryLabelsFromGroup('Event', $lg));
  $t->assign ("cau", $q->queryLabelsFromGroup('Cause', $lg));
  $t->assign ("ctl_glist", true);
  $t->assign ("reg", $reg);
  $t->assign ("path", VAR_DIR);
  $geol = $q->loadGeography(0);
  $glev = $q->loadGeoLevels('', -1, false);
  $evepredl = $q->loadEvents("PREDEF", "active", $lg);
  $eveuserl = $q->loadEvents("USER", "active", $lg);
  $caupredl = $q->loadCauses("PREDEF", "active", $lg);
  $cauuserl = $q->loadCauses("USER", "active", $lg);
  $t->assign ("exteffel", $q->getEEFieldList("True"));
  // Get UserRole
  $role = $us->getUserRole($reg);
  if (strlen($role) > 0)
    $t->assign ("ctl_user", true);
  else
    $t->assign ("ctl_user", false);
  // Set selection map
  $dinf = $q->getDBInfo();
  $t->assign ("regname", $dinf['RegionLabel']);
  $t->assign ("x1", $dinf['GeoLimitMinX']);
  $t->assign ("x2", $dinf['GeoLimitMaxX']);
  $t->assign ("y1", $dinf['GeoLimitMinY']);
  $t->assign ("y2", $dinf['GeoLimitMaxY']);
//  if (testMap(VAR_DIR . "/". $reg . "/". $data))
	$t->assign ("ctl_showmap", true);
  // get range of dates
  $ydb = $q->getDateRange();
  $t->assign ("yini", substr($ydb[0], 0, 4));
  $t->assign ("yend", substr($ydb[1], 0, 4));
  // In Saved Queries set true in Geo, Events, Causes selected..
  if (isset($qd["D_DisasterGeographyId"])) {
    foreach ($qd["D_DisasterGeographyId"] as $ky=>$it) {
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
  $t->assign ("geol", $geol);
  $t->assign ("glev", $glev);
  $t->assign ("evepredl", $evepredl);
  $t->assign ("eveuserl", $eveuserl);
  $t->assign ("caupredl", $caupredl);
  $t->assign ("cauuserl", $cauuserl);
  $t->assign ("ctl_show", true);
  // Query words and phrases in dictionary..
  $ef1 = $q->queryLabelsFromGroup('Effect|People', $lg);
  $ef2 = $q->queryLabelsFromGroup('Effect|Affected', $lg);
  $ef3 = $q->queryLabelsFromGroup('Effect|Economic', $lg);
  $sec = $q->queryLabelsFromGroup('Sector', $lg);
  $sec['SectorTransport'][3] 		= array('EffectRoads' => $ef2['EffectRoads'][0]);
  $sec['SectorCommunications'][3] = null;
  $sec['SectorRelief'][3] 			= null;
  $sec['SectorAgricultural'][3] 	= array('EffectFarmingAndForest' => $ef2['EffectFarmingAndForest'][0],
                                        'EffectLiveStock' => $ef2['EffectLiveStock'][0]);
  $sec['SectorWaterSupply'][3] 		= null;
  $sec['SectorSewerage'][3]			= null;
  $sec['SectorEducation'][3]		= array('EffectEducationCenters' => $ef2['EffectEducationCenters'][0]);
  $sec['SectorPower'][3]			= null;
  $sec['SectorIndustry'][3]			= null;
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
  $fld = "DisasterSerial,DisasterBeginTime,EventName,DisasterGeographyId,DisasterSiteNotes,".
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
    "GeographyCode,DisasterLatitude,DisasterLongitude,RecordAuthor,RecordCreation,RecordLastUpdate,EventNotes");
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
  // STADISTIC
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
  foreach ($glev as $k=>$i)
    $st["StadistDisasterGeographyId_". $k] = array($i[0], $i[1]);
  $std = array();
  $std = array_merge($std, $q->queryLabelsFromGroup('stadist', $lg));
  $std = array_merge($std, $st);
  $t->assign ("std", $std);
}
$t->display ("index.tpl");

</script>
