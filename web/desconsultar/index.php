<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

//ob_start( 'ob_gzhandler' );
require_once('../include/loader.php');
 //require_once('../include/dictionary.class.php');
require_once('../include/query.class.php');
require_once('../include/usersession.class.php');

// Direct access to module
if (isset($_GET['r']) && !empty($_GET['r'])) {
	$reg = $_GET['r'];
	if (isset($_GET['v']) && $_GET['v'] == "true") {
		$db = "";
	} else {
		$db = $reg;
	}
	$q = new Query($db);
	if (isset($_GET['lang']) && !empty($_GET['lang'])) {
		$_SESSION['lang'] = $_GET['lang'];
	}
} else {
	// Direct Acccess Not allowed, do not show anything...
	exit();
}

 //$d = new Dictionary(VAR_DIR);

// Display Geographic list of childs..
if (isset($_GET['cmd']) && $_GET['cmd'] == "glist") {
  $t->assign ("reg", $_GET['GeographyId']);
  $t->assign ("geol", $q->loadGeoChilds($_GET['GeographyId']));
  $t->assign ("ctl_glist", true);
}
else {
  $t->assign ("dis", $q->queryLabelsFromGroup('Disaster', $lg));
  $t->assign ("rc1", $q->queryLabelsFromGroup('Record|1', $lg));
  $t->assign ("rc2", $q->queryLabelsFromGroup('Record|2', $lg));
  $t->assign ("eve", $q->queryLabelsFromGroup('Event', $lg));
  $t->assign ("cau", $q->queryLabelsFromGroup('Cause', $lg));
  $t->assign ("ctl_glist", true);
  $t->assign ("reg", $reg);
  // Set lists if is VirtualRegion
  if (isset($_GET['v']) && $_GET['v'] == "true") {
    $t->assign ("isvreg", true);
    $t->assign ("regname", $reg);
    $areg = $q->getVirtualRegItems($reg);
    $t->assign ("geol", null);
    $glev = array();
    $t->assign ("evepredl", $q->loadEvents("BASE", null, $lg));
    $t->assign ("caupredl", $q->loadCauses("BASE", null, $lg));
    $t->assign ("ctl_showmap", true);
  }
  else {
    $rinf = $q->getDBInfo();
    $t->assign ("regname", $rinf['RegionLabel']);
    $t->assign ("geol", $q->loadGeography(0));
    $glev = $q->loadGeoLevels("");
    $t->assign ("evepredl", $q->loadEvents("PREDEF", "active", $lg));
    $t->assign ("eveuserl", $q->loadEvents("USER", "active", $lg));
    $t->assign ("caupredl", $q->loadCauses("PREDEF", "active", $lg));
    $t->assign ("cauuserl", $q->loadCauses("USER", "active", $lg));
    $t->assign ("exteffel", $q->getEEFieldList("True"));
    // Get UserRole
	$role = $us->getUserRole($reg);
	if (strlen($role) > 0) {
		$t->assign ("ctl_user", true);
	} else {
		$t->assign ("ctl_user", false);
	}
    // Set selection map
    $dinf = $q->getDBInfo();
    if (($dinf['GeoLimitMinX'] != "") && ($dinf['GeoLimitMinY'] != "") &&
        ($dinf['GeoLimitMaxX'] != "") && ($dinf['GeoLimitMaxY'] != "")) {
      $t->assign ("x1", $dinf['GeoLimitMinX']);
      $t->assign ("x2", $dinf['GeoLimitMaxX']);
      $t->assign ("y1", $dinf['GeoLimitMinY']);
      $t->assign ("y2", $dinf['GeoLimitMaxY']);
      if (file_exists(DATADIR . "/". $reg . "/region.map"))
        $t->assign ("ctl_showmap", true);
      else
        $t->assign ("ctl_showmap", false);
    }
    // get range of dates
    $ydb = $q->getDateRange();
    $t->assign ("yini", substr($ydb[0], 0, 4));
    $t->assign ("yend", substr($ydb[1], 0, 4));
  }
  // Geography levels
  $t->assign ("glev", $glev);
  // common sentences in virtual and real regions..
  $t->assign ("ctl_show", true);
  // Results dictionary..
  $dic = array();
  $ef1 = $q->queryLabelsFromGroup('Effect|People', $lg);
  $ef2 = $q->queryLabelsFromGroup('Effect|Affected', $lg);
  $ef3 = $q->queryLabelsFromGroup('Effect|Economic', $lg);
  $sec = $q->queryLabelsFromGroup('Sector', $lg);
  $sec['SectorTransport'][3] 		= array('EffectRoads' => $ef2['EffectRoads'][0]);
  $sec['SectorCommunications'][3] = null;
  $sec['SectorRelief'][3] 			= null;
  $sec['SectorAgricultural'][3] = array('EffectFarmingAndForest' => $ef2['EffectFarmingAndForest'][0],
                                        'EffectLiveStock' => $ef2['EffectLiveStock'][0]);
  $sec['SectorWaterSupply'][3] 	= null;
  $sec['SectorSewerage'][3]			= null;
  $sec['SectorEducation'][3]		= array('EffectEducationCenters' => $ef2['EffectEducationCenters'][0]);
  $sec['SectorPower'][3]				= null;
  $sec['SectorIndustry'][3]			= null;
  $sec['SectorHealth'][3]				= array('EffectMedicalCenters' => $ef2['EffectMedicalCenters'][0]);
  $sec['SectorOther'][3]				= null;
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
  $dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Event', $lg));
  $dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Cause', $lg));
  $dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Effect', $lg));
  $dc2 = array_merge($dc2, $q->queryLabelsFromGroup('Sector', $lg));
  $t->assign ("dc2", $dc2);
  $fld = "DisasterSerial,DisasterBeginTime,EventId,DisasterGeographyId,DisasterSiteNotes,".
        "DisasterSource,EffectNotes,EffectPeopleDead,EffectPeopleMissing,EffectPeopleInjured,".
        "EffectPeopleHarmed,EffectPeopleAffected,EffectPeopleEvacuated,EffectPeopleRelocated,".
        "EffectHousesDestroyed,EffectHousesAffected,EffectFarmingAndForest,EffectRoads,".
        "EffectEducationCenters,EffectMedicalCenters,EffectLiveStock,EffectLossesValueLocal,".
        "EffectLossesValueUSD,EffectOtherLosses,SectorTransport,SectorCommunications,SectorRelief,".
        "SectorAgricultural,SectorWaterSupply,SectorSewerage,SectorEducation,SectorPower,SectorIndustry,".
        "SectorHealth,SectorOther,EventDuration,EventMagnitude,CauseId,CauseNotes";
  $sda = explode(",", $fld);
  $t->assign ("sda", $sda);
  $sda1 = explode(",", "DisasterLatitude,DisasterLongitude,RecordAuthor,RecordCreation,RecordLastUpdate,EventNotes");
  $t->assign ("sda1", $sda1);	// array_diff_key($dc2, array_flip($sda))
  // MAPS
  if (isset($_GET['v']) && $_GET['v'] == "true")
    $mgl = array(0=>array("Nivel0",""), 1=>array("Nivel1",""));
  else
    $mgl = $q->loadGeoLevels("map");
  $t->assign ("mgel", $mgl);
  $range[] = array(10,			"1 - 10", "ffff99");
  $range[] = array(100,			"11 - 100", "ffff00");
  $range[] = array(1000,		"101 - 1000", "ffcc00");
  $range[] = array(10000,		"1001 - 10000", "ff6600");
  $range[] = array(100000,	"10001 - 100000", "cc0000");
  $range[] = array(1000000,	"100001 - 1000000", "660000");
  $range[] = array('',      "1000001 ->",         "000000");
  $t->assign ("range", $range);
  // STADISTIC
  foreach ($ef1 as $k=>$i) {
    $sst[$k] = array($k."Stat|>|-1", $i[0]);
    $nst[$k] = $sst[$k];
//    $nst[$k."_"] = array("$k|=|-1", "Hay ". $i[0]);
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
