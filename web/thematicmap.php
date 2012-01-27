<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/maps.class.php');
require_once('include/diregion.class.php');

function hex2dec($col)
{
	$h = str_split(substr($col, -6), 2);
	return hexdec($h[0]) . ' ' . hexdec($h[1]) . ' ' . hexdec($h[2]);
}

// set hash with limits, legends and colors
function setRanges($opc)
{
	$lim = $opc['_M+limit'];
	$leg = $opc['_M+legend'];
	$col = $opc['_M+color'];
	$lmx = '10000000';
	$maxr = false;
	// First range is No data
	$range[0] = array(0, '= 0', '255 255 255');
	// generate range hash with limit, legend and color
	for ($j = 0; $j < count($lim); $j++)
	{
		if (isset($lim[$j]))
		{
			if ($lim[$j] != '')
			{
				$range[$j+1] = array($lim[$j], $leg[$j], hex2dec($col[$j]));
			}
			else
			{
				$range[$j+1] = array($lmx, $leg[$j], hex2dec($col[$j]));
				$maxr = true;
			}
		}
	}
	// if not assigned, set last range between last number and infinit
	if (!$maxr)
	{
		$range[$j+1] = array($lmx, (int)$lim[$j-1] + 1 . ' -> ', '30 30 30');
	}
	return $range;
}

$post = $_POST;
$get = $_GET;

$RegionId = getParameter('_REG',getParameter('r',''));
if ($RegionId == '')
{
	exit();
}

$us->open($RegionId);
fixPost($post);
$dic = array();
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('MapOpt', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));

$options = array();
$options['URL'] = 'http://' . $_SERVER['HTTP_HOST'] . $desinventarURL;
if (isset($post['_M+cmd']))
{
	// Process QueryDesign Fields and count results
	$qd	= $us->q->genSQLWhereDesconsultar($post);
	$dic = array_merge($dic, $us->q->getEEFieldList('True'));
	$sqc = $us->q->genSQLSelectCount($qd);
	$c	 = $us->q->getresult($sqc);
	$NumberOfRecords = $c['counter'];

	$t->assign('ctl_showres', false);
	if ($NumberOfRecords > 0)
	{
		// Assign ranges
		$range = setRanges($post);
		// Data Options Interface
		$opc['Group'] = array($post['_M+Type']);
		$lev = explode('|', $post['_M+Type']);
		$opc['Field'] = $post['_M+Field'];
		$sql = $us->q->genSQLProcess($qd, $opc);
		// Apply Order fields to order legend too
		$v = explode('|', $opc['Field']);
		if ($v[0] == 'D.DisasterId')
		{
			$v[0] = 'D.DisasterId_';
		}
		$sql .= ' ORDER BY '. substr($v[0],2) .' ASC';
		$info = $us->q->getQueryDetails($dic, $post);
		$info['NumberOfRecords'] = $NumberOfRecords;
		// get query results
		$dislist = $us->q->getassoc($sql);
		//$gitem = $us->q->getGeoCartoItems();
		// generate map
		$dl = $us->q->prepareList($dislist, 'MAPS');

		$MapId = time() . '.' . sprintf('%04d', rand(0, 9999));
		$options['Id'] = $MapId;
		$t->assign('prmMapId', $MapId);

		// MAPS Query, RegionId, Level, datalist, ranges, dbinfo, label, maptype
		$m = new Maps($us, $RegionId, $lev[0], $dl, $range, $info, $post['_M+Label'], $post['_M+Transparency'], 'THEMATIC', $options);
		$rinf = new DIRegion($us);
		$info['RECORDS'] = showStandardNumber($NumberOfRecords);
		$rgl[0]['regname'] = $rinf->get('RegionLabel');
		$rgl[0]['info'] = $info;
		// if valid filename then prepare interface to view MAPFILE	
		if (strlen($m->filename()) > 0)
		{
			$lon = 0;
			$lat = 0;
			$minx = $rinf->get('GeoLimitMinX');
			$maxx = $rinf->get('GeoLimitMaxX');
			$miny = $rinf->get('GeoLimitMinY');
			$maxy = $rinf->get('GeoLimitMaxY');
			$t->assign('minx', $minx);
			$t->assign('maxx', $maxx);
			$t->assign('miny', $miny);
			$t->assign('maxy', $maxy);
			// set center
			if (!empty($minx) && !empty($miny) && !empty($maxx) && !empty($maxy))
			{
				$lon = (int) (($minx + $maxx) / 2);
				$lat = (int) (($miny + $maxy) / 2);
				$aln[] = $minx;	$aln[] = $maxx;
				$alt[] = $miny; $alt[] = $maxy;
			}
			else
			{
				$aln[] = 0;
				$alt[] = 0;
			}
			$lnl[] = $lon;
			$ltl[] = $lat;
			$myly = '';
			if (isset($dl['CVReg']))
			{
				foreach (array_unique($dl['CVReg']) as $it)
				{
					$myly .= $it .'effects,';
				}
				$myly = substr($myly, 0, -1);
			}
			else
			{
				$myly = 'effects';
			}
			$rgl[0]['ly1'] = $myly;
			$rgl[0]['lv'] = $lev[0];
			$rgl[0]['map'] = str_replace('\\','/',$m->filename());
		}
		if (isset($lnl) && isset($ltl))
		{
			$t->assign('lon', array_sum($lnl)/count($lnl));
			$t->assign('lat', array_sum($ltl)/count($ltl));
			$zln = abs(max($aln) - min($aln));
			$zlt = abs(max($alt) - min($alt));
			$mx = ($zln == 0 || $zlt == 0) ? 1 : max($zln, $zlt); 
			$zoom = round(log(180/$mx)) + 3;
			$t->assign('zoom', $zoom);
		}

		$glev = $us->q->loadGeoLevels('', -1, true);
		$t->assign('glev', $glev );
		$t->assign('rgl', $rgl);
		$t->assign('MapNumberOfRecords', $NumberOfRecords);
		$t->assign('qdet', $us->q->getQueryDetails($dic, $post));
		
		// 2010-05-12 (jhcaiced) Create an image for the Map Title and Description...
		// Process array to calculate some parameters...
		$mapinfodic = $us->q->queryLabelsFromGroup('MapInfo', $lg);
		$infoTranslated = array();
		$info2 = $info;
		
		$txtMapTitle = '';
		$txtMapVariable = '';
		
		// Manually Processed Data
		foreach($info2 as $key => $value)
		{
			$value = trim($value);
			$info2[$key] = $value;
		}
		if ($info2['TITLE'] != '')
		{
			$value = $info2['TITLE'];
			$title = $mapinfodic['MapInfoTITLE'][0];
			//$txtMapTitle = $title . ' ' . strtolower($value);
			$txtMapTitle = $post['_M+title'];
			$txtMapVariable = $value;
			unset($info2['TITLE']);
		}

		$ImageRows = 0;
		foreach($info2 as $key => $value)
		{
			$info2[$key] = $value;
			if ($value != '')
			{ 
				$ImageRows++;
				$title = $mapinfodic['MapInfo' . $key][0];
				$infoTranslated[$key] = $title . ': ' . $value;
			}
		}
		$font = getFont('arialbi.ttf');
		
		// Map Title Image
		$width  = 1000; //$sx * $ImageCols;
		$height = 20;
		$imgMapTitle = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($imgMapTitle, 255,255,255);
		$black = imagecolorallocate($imgMapTitle, 0,0,0);
		imagefill($imgMapTitle, 0,0, $white);

		$item = $txtMapTitle;
		$bbox = imagettfbbox(11, 0, $font, $item);
		$x = ($width - ($bbox[2] - $bbox[0]) )/2;
		imagettftext($imgMapTitle, 11, 0, $x, 13, $black, $font, $item);
		
		// Map Info Image
		$fontsize = 10;
		$sy = $fontsize;
		$height = ($sy + 2) * $ImageRows + 2;
		$imgMapInfo = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($imgMapInfo, 255,255,255);
		$black = imagecolorallocate($imgMapInfo, 0,0,0);
		imagefill($imgMapInfo, 0,0, $white);
		
		$y = 0;
		foreach($infoTranslated as $key => $item)
		{
			if ( ($key != 'TITLE') && ($key != 'TITLE2') && ($key != 'NumberOfRecords') )
			{
				imagettftext($imgMapInfo, 10, 0, 0, ($sy + 2) * ($y + 1), $black, getFont('arial.ttf'), $item);
				$y++;
			}
		}

		//$MapInfoImg = 'mapinfo_' . session_id() . '_' . rand(0, 50000) . '.jpg';
		//imagejpeg($imgMapInfo, WWWDIR . '/' . $MapInfoImg, 100);
		//$t->assign('mapinfoimg', WWWDATA . '/' . $MapInfoImg);
		
		$mapfile = str_replace('\\', '/', $m->filename());
		$worldmap = str_replace('\\','/', MAPDIR . '/world_adm0.map');
		$timestamp = microtime(true);
		$sLegendURL = $options['URL'] . '/wms/' . $options['Id'] . '/legend/';
		$t->assign('legend', $sLegendURL);	
		$t->assign('ctl_showres', true);
		// 2009-09-10 (jhcaiced) Replace backslash chars to slash, when passing data to mapserver
		if ($post['_M+cmd'] == 'export')
		{
			$t->assign('ctl_showres', false);
			$w = 1000;
			$h = 756;
			$size = '1000756';
			/*
			$base = $options['URL'] . '/wms/worldmap/' . 
				'?REQUEST=getmap&STYLES=&SRS=EPSG:900913&BBOX='. $post['_M+extent'] .
				'&WIDTH='. $w .'&HEIGHT='. $h .'&FORMAT=image/png';
			$bf = file_get_contents($base);
			$url1 = $options['URL'] . '/wms/' . $options['Id'] . '/' . 
				'?layers='. $post['_M+layers'] .'&REQUEST=getmap&STYLES=&SRS=EPSG:900913'.
				'&BBOX='. $post['_M+extent'].'&WIDTH='. $w .'&HEIGHT='. $h .'&FORMAT=image/png';
			$mf = file_get_contents($url1);
			*/

			$base = '/cgi-bin/'. MAPSERV .'?map='. rawurlencode($worldmap) . '&SERVICE=WMS&VERSION=1.1.1'.
				'&layers=base&REQUEST=getmap&STYLES=&SRS=EPSG:900913&BBOX='. $post['_M+extent'].
				'&WIDTH='. $w .'&HEIGHT='. $h .'&FORMAT=image/png' . '&t=' . $timestamp;
			$bf = file_get_contents('http://'. $_SERVER['HTTP_HOST'] . $base);
			$url1 = '/cgi-bin/'. MAPSERV .'?map='. rawurlencode($mapfile) .'&SERVICE=WMS&VERSION=1.1.1'.
				'&layers='. $post['_M+layers'] .'&REQUEST=getmap&STYLES=&SRS=EPSG:900913'.
				'&BBOX='. $post['_M+extent'].'&WIDTH='. $w .'&HEIGHT='. $h .'&FORMAT=image/png' . '&t=' . $timestamp;
			$mf = file_get_contents('http://'. $_SERVER['HTTP_HOST'] . $url1);
			if ($mf)
			{
				$ibas = imagecreatefromstring($bf);
				$imap = imagecreatefromstring($mf);
				// Download and include legend
				$lf = file_get_contents('http://'. $_SERVER['HTTP_HOST'] . $sLegendURL);
				$imgTmp = imagecreatefromstring($lf);
				$fontsize = 11;
				$sx = imagesx($imgTmp);
				$sy = imagesy($imgTmp) + $fontsize + 2;
				$imgMapLegend = imagecreatetruecolor($sx, $sy);
				imagefill($imgMapLegend, 0,0, imagecolorallocate($imgMapLegend, 255,255,255));
				imagecopy($imgMapLegend, $imgTmp, 0, $fontsize + 2, 0, 0, imagesx($imgTmp), imagesy($imgTmp));
				imagettftext($imgMapLegend, 10, 0, 4, 2 + $fontsize, imagecolorallocate($imgMapLegend,0, 0, 89), 'arialbi',  $txtMapVariable);

				// Include MapInfo Image (Title, Query Info etc.)
				$wt = imagesx($imap); // + imagesx($imgMapLegend);
				$ht = imagesy($imap) + imagesy($imgMapTitle) + imagesy($imgMapInfo);
				$im = imagecreatetruecolor($wt, $ht);
				imagefilledrectangle($im, 0, 0, $wt - 1, $ht - 1, imagecolorallocate($im, 255, 0, 0));
				imagecopy($im, $imgMapTitle, 0, 0, 0, 0, imagesx($imgMapTitle), imagesy($imgMapTitle));
				imagecopy($im, $ibas, 0, imagesy($imgMapTitle), 0, 0, $w, $h);
				imagecopy($im, $imap, 0, imagesy($imgMapTitle), 0, 0, $w, $h);
				imagecopy($im, $imgMapLegend, 5, ($h + imagesy($imgMapTitle)) - (imagesy($imgMapLegend) + 5), 0, 0, imagesx($imgMapLegend), imagesy($imgMapLegend));
				imagecopy($im, $imgMapInfo, 0, $ht - imagesy($imgMapInfo), 0, 0, imagesx($imgMapInfo), imagesy($imgMapInfo));
				
				// 2010-05-18 (jhcaiced) Draw a gray rectangle around the image...
				imagerectangle($im, 0, imagesy($imgMapTitle), $wt - 1, $ht - imagesy($imgMapInfo), imagecolorallocate($im,192,192,192));
				
				//imagecopy($im, $imgMapLegend, $w+1, $h - imagesy($imgMapLegend), 0, 0, imagesx($imgMapLegend), imagesy($imgMapLegend));
				$mapfooter = trim('http://www.desinventar.org/' . ' - ' . $rinf->get('RegionLabel'));
				$font = 'arial';
				$fontsize = 10;
				$bbox = imagettfbbox($fontsize, 0, $font, $mapfooter);
				$x = $bbox[2] - $bbox[0];
				$x = $wt - 2 - $x;
				$y = $ht - imagesy($imgMapInfo) - 4;
				imagettftext($im, $fontsize, 0, $x, $y, $black, $font,  $mapfooter);
				header('Content-Disposition: attachment; filename=DesInventar_'. str_replace(' ', '', $rinf->get('RegionLabel')) .'_ThematicMap.png');
				imagepng($im);
				imagedestroy($imap);
				imagedestroy($imgMapLegend);
				imagedestroy($im);
			}
		}
		imagedestroy($imgMapInfo);
		$t->assign('reg', $RegionId);
		$t->assign('basemap', $worldmap);
		$t->assign('mps', MAPSERV);
	}
	$t->display('thematicmap.tpl');
}
elseif (isset($get['cmd']) && $get['cmd'] == 'getkml')
{
	$m = new Maps($us, $RegionId, null, null, null, null, null, null, 'KML', $options);

	// Send KML file to browser
	header('Content-type: text/kml');
	header('Content-Disposition: attachment; filename=DesInventar_'. str_replace(' ', '', $RegionId) .'_ThematicMap.kml');
	echo $m->printKML();
	/*
	$filename = TMP_DIR . '/kmlmap' . $us->sSessionId .  '.kml';
	$fh = fopen($filename,'w+');
	fputs($fh, $m->printKML());
	fclose($fh);
	*/
}
</script>
