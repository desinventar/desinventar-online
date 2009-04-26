<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');
require_once('../include/maps.class.php');

function hex2dec($col) {
  $c = substr($col, -6);
  $h = str_split($c, 2);
  $v1 = hexdec($h[0]);
  $v2 = hexdec($h[1]);
  $v3 = hexdec($h[2]);
  return $v1 ." ". $v2 . " ". $v3;
}

// set hash with limits, legends and colors
function setRanges($opc) {
  $lim = $opc['_M+limit'];
  $leg = $opc['_M+legend'];
  $col = $opc['_M+color'];
  $lmx = "10000000";
  $maxr = false;
  // First range is No data
  $range[0] = array(1, "= 0", "255 255 255");
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

if (isset($post['_REG']) && !empty($post['_REG'])) {
  $reg = $post['_REG'];
  if (isset($post['_VREG']) && $post['_VREG'] == "true")
    $q = new Query();
  else
    $q = new Query($reg);
}
elseif (isset($get['r']) && !empty($get['r'])) {
  $reg = $get['r'];
  $q = new Query($reg);
}
else
  exit();

$dic = array();
$dic = array_merge($dic, $q->queryLabelsFromGroup('MapOpt', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Sector', $lg));

if (isset($post['_M+cmd'])) {
  // Process QueryDesign Fields and count results
  $qd	= $q->genSQLWhereDesconsultar($post);
  $cou = 0;
  if (isset($post['_VREG']) && $post['_VREG'] == "true")
    $areg = $q->getVirtualRegItems($reg);
  else {
    $areg = (array)$reg;
    $dic = array_merge($dic, $q->getEEFieldList("True"));
  }
  // accumulate results of VirtualRegions items
  foreach ($areg as $rg) {
    $q2 = new Query($rg);
    $sqc	= $q2->genSQLSelectCount($qd);
    $c		= $q2->getresult($sqc);
    $cou += $c['counter'];
  }
  if (isset($post['_VREG']) && $post['_VREG'] == "true") {
    $areg = $q->getVirtualRegItems($reg);
    $t->assign ("isvreg", true);
    $glev = array();
  }
  else {
    $areg = (array)$reg;
    $t->assign ("reg", $reg);
    $glev = $q->loadGeoLevels("map");
  }
  //
  if (isset($post['_M+cmd'])) {
    // Assign ranges
    $range = setRanges($post);
    foreach ($areg as $kg=>$rg) {
      // if is VirtualRegion apply foreach to database..
      $q3 = new Query($rg);
      $rinf = $q3->getDBInfo();
      $rgl[$kg]['regname'] = $rinf['RegionLabel'];
      // Data Options Interface
      $opc['Group'] = array($post['_M+Type']);
      $lev = explode("|", $post['_M+Type']);
      $opc['Field'] = $post['_M+Field'];
      //print_r($opc);
      $sql = $q3->genSQLProcess($qd, $opc);
      // Apply Order fields to order legend too
      $v = explode("|", $opc['Field']);
      if ($v[0] == "D.DisasterId")
        $v[0] = "D.DisasterId_";
      $sql .= " ORDER BY ". substr($v[0],2);
      $dislist = $q3->getassoc($sql);
      // get query results
      //if (!empty($dislist)) {
      // generate map
        $dl = $q3->prepareList($dislist, "MAPS");
        $info = $q3->getQueryDetails($dic, $post);
        // MAPS Object, RegionId, Level, datalist, ranges, dbinfo, label, maptype
        $m = new Maps($q3, $rg, $lev[0], $dl, $range, $info, $post['_M+Label'], "THEMATIC");
        $rgl[$kg]['info'] = $info;
        // if valid filename then prepare interface to view MAPFILE
        if (strlen($m->filename()) > 0) {
          $lon = 0;
          $lat = 0;
          $minx = $rinf['GeoLimitMinX'];
          $maxx = $rinf['GeoLimitMaxX'];
          $miny = $rinf['GeoLimitMinY'];
          $maxy = $rinf['GeoLimitMaxY'];
          //$dinf = $q->getDBInfo();
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
          $rgl[$kg]['ly1'] = "effects";
          $rgl[$kg]['lv'] = $lev[0];
          $rgl[$kg]['map'] = $m->filename();
        }
      //}
    } // foreach
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
    $t->assign ("qdet", $q3->getQueryDetails($dic, $post));
    if ($post['_M+cmd'] == "export" && !(isset($post['_VREG']) && $post['_VREG'] == "true")) {
      $dinf = $q->getDBInfo();
      $regname = $dinf['RegionLabel'];
      $url = "/cgi-bin/mapserv?map=". $m->filename() ."&SERVICE=WMS&VERSION=1.1.1".
        "&layers=". $post['_M+layers'] ."&REQUEST=getmap&STYLES=&SRS=EPSG:4326".
        "&BBOX=". $post['_M+extent']."&WIDTH=500&HEIGHT=378&FORMAT=image/png";
      $mf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $url);
      if ($mf) {
        $imap = imagecreatefromstring($mf);
        // Download and include legend
        $url2 = "/cgi-bin/mapserv?map=". $m->filename() ."&SERVICE=WMS&VERSION=1.1.1".
          "&REQUEST=getlegendgraphic&LAYER=effects&FORMAT=image/png";
        $lf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $url2);
        $ileg = imagecreatefromstring($lf);
        $im = imagecreate(imagesx($imap) + imagesx($ileg), imagesy($imap));
        imagecopy($im, $imap, 0, 0, 0, 0, 500, 378);
        imagecopy($im, $ileg, 501, 378-imagesy($ileg), 0, 0, imagesx($ileg), imagesy($ileg));
        header("Content-type: Image/png");
        header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $regname) ."_ThematicMap.png");
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
$t->assign ("dic", $dic);
$t->assign ("basemap", VAR_DIR . "/_WORLD/region.map");

if (LNX) {
  $t->assign ("shw_server", true);
  $t->assign ("mps", "mapserv");
}
else {
  $t->assign ("shw_server", false);
  $t->assign ("mps", "mapserv.exe");
}

$t->display ("thematicmap.tpl");

</script>
