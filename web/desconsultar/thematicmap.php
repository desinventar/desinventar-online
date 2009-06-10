<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');
require_once('../include/maps.class.php');

function hex2dec($col) {
  $h = str_split(substr($col, -6), 2);
  return hexdec($h[0])." ". hexdec($h[1]) . " ". hexdec($h[2]);
}

// set hash with limits, legends and colors
function setRanges($opc) {
  $lim = $opc['_M+limit'];
  $leg = $opc['_M+legend'];
  $col = $opc['_M+color'];
  $lmx = "10000000";
  $maxr = false;
  // First range is No data
  $range[0] = array(0, "= 0", "255 255 255");
  // generate range hash with limit, legend and color
  for ($j = 0; $j < count($lim); $j++) {
    if (isset($lim[$j])) {
      if ($lim[$j] != "")
        $range[$j+1] = array($lim[$j], $leg[$j], hex2dec($col[$j]));
      else {
        $range[$j+1] = array($lmx, $leg[$j], hex2dec($col[$j]));
        $maxr = true;
      }
    }
  }
  // if not assigned, set last range between last number and infinit
  if (!$maxr)
    $range[$j+1] = array($lmx, (int)$lim[$j-1] + 1 . " -> ", "30 30 30");
	return $range;
}

$post = $_POST;
$get  = $_GET;

if (isset($post['_REG']) && !empty($post['_REG']))
  $reg = $post['_REG'];
elseif (isset($get['r']) && !empty($get['r']))
  $reg = $get['r'];
else
  exit();

$q = new Query($reg);

$dic = array();
$dic = array_merge($dic, $q->queryLabelsFromGroup('MapOpt', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Sector', $lg));

if (isset($post['_M+cmd'])) {
  // Process QueryDesign Fields and count results
  $qd	= $q->genSQLWhereDesconsultar($post);
	$dic = array_merge($dic, $q->getEEFieldList("True"));
	$sqc = $q->genSQLSelectCount($qd);
	$c	 = $q->getresult($sqc);
	$cou = $c['counter'];
  $glev = $q->loadGeoLevels("map");
  if (isset($post['_M+cmd'])) {
    // Assign ranges
    $range = setRanges($post);
		$rinf = $q->getDBInfo();
		// Data Options Interface
    $opc['Group'] = array($post['_M+Type']);
    $lev = explode("|", $post['_M+Type']);
    $opc['Field'] = $post['_M+Field'];
		$sql = $q->genSQLProcess($qd, $opc);
		// Apply Order fields to order legend too
		$v = explode("|", $opc['Field']);
		if ($v[0] == "D.DisasterId")
			$v[0] = "D.DisasterId_";
		$sql .= " ORDER BY ". substr($v[0],2) ." ASC";
    $info = $q->getQueryDetails($dic, $post);
		// get query results
		$dislist = $q->getassoc($sql);
		//$gitem = $q->getGeoCartoItems();
		// generate map
		$dl = $q->prepareList($dislist, "MAPS");
		// foreach regionitems -> create map object
		//echo "<pre>"; print_r($dislist);
    // MAPS Object, RegionId, Level, datalist, ranges, dbinfo, label, maptype
    $m = new Maps($q, $reg, $lev[0], $dl, $range, $info, $post['_M+Label'], "THEMATIC");
		$rgl[0]['regname'] = $rinf['RegionLabel'];
    $rgl[0]['info'] = $info;
    // if valid filename then prepare interface to view MAPFILE
    if (strlen($m->filename()) > 0) {
      $lon = 0;
      $lat = 0;
      $minx = $rinf['GeoLimitMinX'];
      $maxx = $rinf['GeoLimitMaxX'];
      $miny = $rinf['GeoLimitMinY'];
      $maxy = $rinf['GeoLimitMaxY'];
      // set center
      if (!empty($minx) && !empty($miny) && !empty($maxx) && !empty($maxy)) {
        $lon = (int) (($minx + $maxx) / 2);
        $lat = (int) (($miny + $maxy) / 2);
        $aln[] = $minx;	$aln[] = $maxx;
        $alt[] = $miny; $alt[] = $maxy;
      }
      else {
        $aln[] = 0;
        $alt[] = 0;
      }
      $lnl[] = $lon;
      $ltl[] = $lat;
      $rgl[0]['ly1'] = "effects";
      $rgl[0]['lv'] = $lev[0];
      $rgl[0]['map'] = $m->filename();
    }
    if (isset($lnl) && isset($ltl)) {
      $t->assign ("lon", array_sum($lnl)/count($lnl));
      $t->assign ("lat", array_sum($ltl)/count($ltl));
      $zln = abs(max($aln) - min($aln));
      $zlt = abs(max($alt) - min($alt));
      $mx = ($zln == 0 || $zlt == 0) ? 1 : max($zln, $zlt); 
      $zoom = round(log(180/$mx)) + 3;
      $t->assign ("zoom", $zoom);
    }
    $t->assign ("glev", $glev);
    $t->assign ("rgl", $rgl);
    $t->assign ("tot", $cou);
    $t->assign ("qdet", $q->getQueryDetails($dic, $post));
    if ($post['_M+cmd'] == "export") {
      $url0 = "/cgi-bin/". MAPSERV ."?map=". VAR_DIR ."/_WORLD/region.map&SERVICE=WMS&VERSION=1.1.1".
        "&layers=base&REQUEST=getmap&STYLES=&SRS=EPSG:4326&BBOX=". $post['_M+extent'].
        "&WIDTH=500&HEIGHT=378&FORMAT=image/png";
      $bf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $url0);
      $url1 = "/cgi-bin/". MAPSERV ."?map=". $m->filename() ."&SERVICE=WMS&VERSION=1.1.1".
        "&layers=". $post['_M+layers'] ."&REQUEST=getmap&STYLES=&SRS=EPSG:4326".
        "&BBOX=". $post['_M+extent']."&WIDTH=500&HEIGHT=378&FORMAT=image/png";
      $mf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $url1);
      if ($mf) {
        $ibas = imagecreatefromstring($bf);
        $imap = imagecreatefromstring($mf);
        // Download and include legend
        $url2 = "/cgi-bin/". MAPSERV ."?map=". $m->filename() ."&SERVICE=WMS&VERSION=1.1.1".
          "&REQUEST=getlegendgraphic&LAYER=effects&FORMAT=image/png";
        $lf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $url2);
        $ileg = imagecreatefromstring($lf);
        $wt = imagesx($imap) + imagesx($ileg);
        $ht = imagesy($imap);
        $im = imagecreatetruecolor($wt, $ht);
        imagefilledrectangle($im, 0, 0, $wt - 1, $ht - 1, imagecolorallocate($im, 255, 255, 255));
        imagecopy($im, $ibas, 0, 0, 0, 0, 500, 378);
        imagecopy($im, $imap, 0, 0, 0, 0, 500, 378);
        imagecopy($im, $ileg, 501, 378-imagesy($ileg), 0, 0, imagesx($ileg), imagesy($ileg));
        imagestring($im, 3, 2, $ht - 20, 'http://online.desinventar.org/', imagecolorallocate($im, 0, 0, 0));
        header("Content-type: Image/png");
        header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $rinf['RegionLabel']) ."_ThematicMap.png");
        imagepng($im);
        imagedestroy($imap);
        imagedestroy($ileg);
        imagedestroy($im);
      }
    }
    else
      $t->assign ("ctl_showres", true);
  }
}
elseif (isset($get['cmd']) && $get['cmd'] == "getkml") {
  header("Content-type: text/kml");
  header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $reg) ."_ThematicMap.kml");
  $m = new Maps($q, $reg, null, null, null, null, null, "KML");
  echo $m->printKML();
  exit();
}
$t->assign ("reg", $reg);
$t->assign ("dic", $dic);
$t->assign ("basemap", VAR_DIR . "/_WORLD/region.map");
$t->assign ("mps", MAPSERV);
$t->display ("thematicmap.tpl");
</script>
