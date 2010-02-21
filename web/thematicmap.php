<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/maps.class.php');

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
$get = $_GET;

if (isset($post['_REG']) && !empty($post['_REG']))
	$reg = $post['_REG'];
elseif (isset($get['r']) && !empty($get['r']))
	$reg = $get['r'];
else
	exit();

$us->open($reg);

fixPost($post);

$dic = array();
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('MapOpt', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));

if (isset($post['_M+cmd'])) {
	// 2010-01-18 (jhcaiced) Windows machines doesn't use remote servers
	if (isset($_SERVER["WINDIR"])) {
		$hasInternet = 0;
	} else {
		// Linux machines are assumed to be connected to internet
		$hasInternet = 1;
		/*
		if (!fsockopen('www.google.com',80)) {
			$hasInternet = 0;
		}
		*/
	}	
	$t->assign('hasInternet', $hasInternet);
	// Process QueryDesign Fields and count results
	$qd	= $us->q->genSQLWhereDesconsultar($post);
	$dic = array_merge($dic, $us->q->getEEFieldList("True"));
	$sqc = $us->q->genSQLSelectCount($qd);
	$c	 = $us->q->getresult($sqc);
	$cou = $c['counter'];
	// Assign ranges
	$range = setRanges($post);
	// Data Options Interface
	$opc['Group'] = array($post['_M+Type']);
	$lev = explode("|", $post['_M+Type']);
	$opc['Field'] = $post['_M+Field'];
	$sql = $us->q->genSQLProcess($qd, $opc);
	// Apply Order fields to order legend too
	$v = explode("|", $opc['Field']);
	if ($v[0] == "D.DisasterId")
		$v[0] = "D.DisasterId_";
	$sql .= " ORDER BY ". substr($v[0],2) ." ASC";
	$info = $us->q->getQueryDetails($dic, $post);
	// get query results
	$dislist = $us->q->getassoc($sql);
	//$gitem = $us->q->getGeoCartoItems();
	// generate map
	$dl = $us->q->prepareList($dislist, "MAPS");
	// MAPS Query, RegionId, Level, datalist, ranges, dbinfo, label, maptype
	$m = new Maps($us->q, $reg, $lev[0], $dl, $range, $info, $post['_M+Label'], $post['_M+Transparency'], "THEMATIC");	
	$rinf = $us->q->getDBInfo($lg);
	$rgl[0]['regname'] = $rinf['RegionLabel|'];
	$rgl[0]['info'] = $info;
	// if valid filename then prepare interface to view MAPFILE	
	if (strlen($m->filename()) > 0) {
		$lon = 0;
		$lat = 0;
		$minx = $rinf['GeoLimitMinX|'];
		$maxx = $rinf['GeoLimitMaxX|'];
		$miny = $rinf['GeoLimitMinY|'];
		$maxy = $rinf['GeoLimitMaxY|'];
		$t->assign('minx', $minx);
		$t->assign('maxx', $maxx);
		$t->assign('miny', $miny);
		$t->assign('maxy', $maxy);
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
		$myly = "";
		if (isset($dl['CVReg'])) {
			foreach (array_unique($dl['CVReg']) as $it)
				$myly .= $it ."effects,";
			$myly = substr($myly, 0, -1);
		}
		else
			$myly = "effects";
		$rgl[0]['ly1'] = $myly;
		$rgl[0]['lv'] = $lev[0];
		$rgl[0]['map'] = str_replace('\\','/',$m->filename());
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
	
	$t->assign ("glev", $us->q->loadGeoLevels('', -1, true));
	$t->assign ("rgl", $rgl);
	$t->assign ("tot", $cou);
	$t->assign ("qdet", $us->q->getQueryDetails($dic, $post));
	$mapfile = str_replace('\\', '/', $m->filename());
	$worldmap = str_replace('\\','/', DATADIR . "/worldmap/world_adm0.map");
	$legend = "/cgi-bin/". MAPSERV ."?map=" . rawurlencode($mapfile) . "&SERVICE=WMS&VERSION=1.1.1".
				"&REQUEST=getlegendgraphic&LAYER=". substr($myly, 0, 12) ."&FORMAT=image/png";
	$t->assign ("legend", $legend);	
	// 2009-09-10 (jhcaiced) Replace backslash chars to slash, when passing data to mapserver
	if ($post['_M+cmd'] == "export") {
		$w = 1000;
		$h = 756;
		$size = "1000756";
		$base = "/cgi-bin/". MAPSERV ."?map=". rawurlencode($worldmap) . "&SERVICE=WMS&VERSION=1.1.1".
			"&layers=base&REQUEST=getmap&STYLES=&SRS=EPSG:900913&BBOX=". $post['_M+extent'].
			"&WIDTH=". $w ."&HEIGHT=". $h ."&FORMAT=image/png";
		$bf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $base);
		$url1 = "/cgi-bin/". MAPSERV ."?map=". rawurlencode($mapfile) ."&SERVICE=WMS&VERSION=1.1.1".
			"&layers=". $post['_M+layers'] ."&REQUEST=getmap&STYLES=&SRS=EPSG:900913".
			"&BBOX=". $post['_M+extent']."&WIDTH=". $w ."&HEIGHT=". $h ."&FORMAT=image/png";
		$mf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $url1);
		if ($mf) {
			$ibas = imagecreatefromstring($bf);
			$imap = imagecreatefromstring($mf);
			// Download and include legend
			$lf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $legend);
			$ileg = imagecreatefromstring($lf);
			$wt = imagesx($imap) + imagesx($ileg);
			$ht = imagesy($imap);
			$im = imagecreatetruecolor($wt, $ht);
			imagefilledrectangle($im, 0, 0, $wt - 1, $ht - 1, imagecolorallocate($im, 255, 255, 255));
			imagecopy($im, $ibas, 0, 0, 0, 0, $w, $h);
			imagecopy($im, $imap, 0, 0, 0, 0, $w, $h);
			imagecopy($im, $ileg, $w+1, $h - imagesy($ileg), 0, 0, imagesx($ileg), imagesy($ileg));
			imagestring($im, 3, 2, $ht - 20, 'http://www.desinventar.org/', imagecolorallocate($im, 0, 0, 0));
			header("Content-type: Image/png");
			header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $rinf['RegionLabel|']) ."_ThematicMap.png");
			imagepng($im);
			imagedestroy($imap);
			imagedestroy($ileg);
			imagedestroy($im);
		}
	}
	else {
		$t->assign ("ctl_showres", true);
	}
} elseif (isset($get['cmd']) && $get['cmd'] == "getkml") {
	// Send KML file - GoogleEarth
	header("Content-type: text/kml");
	header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $reg) ."_ThematicMap.kml");
	$m = new Maps($us->q, $reg, null, null, null, null, null, null, "KML");
	echo $m->printKML();
	$fh = fopen('/tmp/map.kml','w+');
	fputs($fh, $m->printKML());
	fclose($fh);
	exit();
}

// 2009-07-14 (jhcaiced) Configure Google Map Key
$GoogleMapsKey = "";
switch($_SERVER["SERVER_NAME"]) {
	case "devel.desinventar.org":
		$GoogleMapsKey = "ABQIAAAAv_HCDVf4YK_pJceWBA7XmRQHPIpdtLPiHEY9M3_iWXAS0AXQLhTwoORtm0ZLuqG03CB3sP09KKDtAg";
		break;
	case "online.desinventar.org":
		$GoogleMapsKey = "ABQIAAAAv_HCDVf4YK_pJceWBA7XmRQHPIpdtLPiHEY9M3_iWXAS0AXQLhTwoORtm0ZLuqG03CB3sP09KKDtAg";		
		break;
	/*
	case "192.168.0.13":
		$GoogleMapsKey = "ABQIAAAAv_HCDVf4YK_pJceWBA7XmRRT41YKyiJ82KgcK-Dai8T6I93cWxT4pcci6xQX6tWCkefVHbB2AtUGKw";
		break;
	*/
	case "localhost":
		$GoogleMapsKey = "ABQIAAAAv_HCDVf4YK_pJceWBA7XmRT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQrE9s8Pd9b8nrmaDwyyilebSXcPw";
		break;
	case "127.0.0.1":
		$GoogleMapsKey = "ABQIAAAAv_HCDVf4YK_pJceWBA7XmRRi_j0U6kJrkFvY4-OX2XYmEAa76BSA4JvNpGUXBDLtWrA-lnRXmTahHg";
		break;
	default:
		$GoogleMapsKey = "";
		break;
}

$t->assign ("reg", $reg);
//$t->assign ("dic", $dic);
$t->assign ("basemap", $worldmap);
$t->assign ("mps", MAPSERV);
$t->assign ("googlemapkey", $GoogleMapsKey);
$t->display ("thematicmap.tpl");
</script>
