<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/maps.class.php');
require_once('include/diregion.class.php');

$post = $_POST;
$get = $_GET;
$options = array();
$options['URL'] = 'http://' . $_SERVER['HTTP_HOST'] . $desinventarURL;
$cmd = getParameter('_M+cmd', getParameter('cmd', ''));
if ($cmd == '')
{
	$cmd = 'result';
}
switch($cmd)
{
	case 'result':
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
			$imgTitle = imagecreatetruecolor($width, $height);
			$white = imagecolorallocate($imgTitle, 255,255,255);
			$black = imagecolorallocate($imgTitle, 0,0,0);
			imagefill($imgTitle, 0,0, $white);

			$item = $txtMapTitle;
			$bbox = imagettfbbox(11, 0, $font, $item);
			$x = ($width - ($bbox[2] - $bbox[0]) )/2;
			imagettftext($imgTitle, 11, 0, $x, 13, $black, $font, $item);
			
			// Map Info Image
			$fontsize = 10;
			$sy = $fontsize;
			$height = ($sy + 2) * $ImageRows + 2;
			$imgInfo = imagecreatetruecolor($width, $height);
			$white = imagecolorallocate($imgInfo, 255,255,255);
			$black = imagecolorallocate($imgInfo, 0,0,0);
			imagefill($imgInfo, 0,0, $white);
			
			$y = 0;
			foreach($infoTranslated as $key => $item)
			{
				if ( ($key != 'TITLE') && ($key != 'TITLE2') && ($key != 'NumberOfRecords') )
				{
					imagettftext($imgInfo, 10, 0, 0, ($sy + 2) * ($y + 1), $black, getFont('arial.ttf'), $item);
					$y++;
				}
			}

			$mapfile = str_replace('\\', '/', $m->filename());
			$worldmap = str_replace('\\','/', MAPDIR . '/world_adm0.map');
			$timestamp = microtime(true);
			$sLegendURL = $options['URL'] . '/wms/' . $options['Id'] . '/legend/';
			$t->assign('legend', $sLegendURL);	
			$t->assign('ctl_showres', true);
			imagedestroy($imgInfo);
			$t->assign('reg', $RegionId);
			$t->assign('basemap', $worldmap);
			$t->assign('mps', MAPSERV);
		}
		$t->display('thematicmap.tpl');
	break;
	case 'getkml':
		$MapId = getParameter('MAPID', '');
		if ($MapId != '')
		{
			$sFilename = TMP_DIR . '/map_' . $MapId . '.kml';
			if (file_exists($sFilename))
			{
				$sOutFilename = 'DesInventar_ThematicMap_' . $MapId . '.kml';
				header('Content-type: text/kml');
				header('Content-Disposition: attachment; filename=' . $sOutFilename);
				echo file_get_contents($sFilename);
			}
		}
	break;
	case 'export':
		// Save image of an already created map
		$options['Id']     = getParameter('_M+mapid', getParameter('mapid', ''));
		$options['Extent'] = getParameter('_M+extent', getParameter('extent', ''));
		fb($options);

		$iBaseLeft   = 0;
		$iBaseTop    = 0;
		$iBaseWidth  = 1000;
		$iBaseHeight = 756;
		$sBaseUrl = $options['URL'] . '/wms/worldmap/' . '?SRS=EPSG:900913' .
			'&BBOX='. $options['Extent'] . '&WIDTH='. $iBaseWidth .'&HEIGHT='. $iBaseHeight;
		$imgBase = imagecreatefromstring(file_get_contents($sBaseUrl));

		$iMapLeft    = $iBaseLeft;
		$iMapTop     = $iBaseTop;
		$iMapWidth   = $iBaseWidth;
		$iMapHeight  = $iBaseHeight;
		$sMapLayers  = 'effects';  // Must read POST parameter
		$sMapUrl = $options['URL'] . '/wms/' . $options['Id'] . '/?SRS=EPSG:900913' . 
			'&BBOX='. $options['Extent'] .'&WIDTH='. $iMapWidth .'&HEIGHT='. $iMapHeight .
			'&LAYERS=' . $sMapLayers;
		$imgMap = imagecreatefromstring(file_get_contents($sMapUrl));
		//	'?layers='. $post['_M+layers'] 
		
		$iAllWidth  = $iBaseWidth;
		$iAllHeight = $iBaseHeight;
		/*
		// Download and include legend
		$lf = file_get_contents('http://'. $_SERVER['HTTP_HOST'] . $sLegendURL);
		$imgTmp = imagecreatefromstring($lf);
		$fontsize = 11;
		$sx = imagesx($imgTmp);
		$sy = imagesy($imgTmp) + $fontsize + 2;
		$imgLegend = imagecreatetruecolor($sx, $sy);
		imagefill($imgLegend, 0,0, imagecolorallocate($imgLegend, 255,255,255));
		imagecopy($imgLegend, $imgTmp, 0, $fontsize + 2, 0, 0, imagesx($imgTmp), imagesy($imgTmp));
		imagettftext($imgLegend, 10, 0, 4, 2 + $fontsize, imagecolorallocate($imgLegend,0, 0, 89), 'arialbi',  $txtMapVariable);

		// Include MapInfo Image (Title, Query Info etc.)
		$iAllWidth = imagesx($imgMap);
		$iAllHeight = imagesy($imgMap) + imagesy($imgTitle) + imagesy($imgInfo);
		imagecopy($imgAll, $imgTitle, 0, 0, 0, 0, imagesx($imgTitle), imagesy($imgTitle));
		imagecopy($imgAll, $imgLegend, 5, ($iMapHeight + imagesy($imgTitle)) - (imagesy($imgLegend) + 5), 0, 0, imagesx($imgLegend), imagesy($imgLegend));
		imagecopy($imgAll, $imgInfo, 0, $iAllHeight - imagesy($imgInfo), 0, 0, imagesx($imgInfo), imagesy($imgInfo));
		
		// 2010-05-18 (jhcaiced) Draw a gray rectangle around the image...
		imagerectangle($imgAll, 0, imagesy($imgTitle), $iAllWidth - 1, $iAllHeight - imagesy($imgInfo), imagecolorallocate($im,192,192,192));
		
		//imagecopy($imgAll, $imgLegend, $iMapWidth+1, $iMapHeight - imagesy($imgLegend), 0, 0, imagesx($imgLegend), imagesy($imgLegend));
		$mapfooter = trim('http://www.desinventar.org/' . ' - ' . $rinf->get('RegionLabel'));
		$font = 'arial';
		$fontsize = 10;
		$bbox = imagettfbbox($fontsize, 0, $font, $mapfooter);
		$x = $bbox[2] - $bbox[0];
		$x = $iAllWidth - 2 - $x;
		$y = $iAllHeight - imagesy($imgInfo) - 4;
		imagettftext($imgAll, $fontsize, 0, $x, $y, $black, $font,  $mapfooter);
		imagedestroy($imgLegend);
		*/

		// Create and assembly final image
		$imgAll = imagecreatetruecolor($iAllWidth, $iAllHeight);
		imagefilledrectangle($imgAll, 0, 0, $iAllWidth - 1, $iAllHeight - 1, imagecolorallocate($imgAll,192,192,192));
		imagecopy($imgAll, $imgBase, $iBaseLeft, $iBaseTop, 0, 0, $iBaseWidth, $iBaseHeight);
		imagecopy($imgAll, $imgMap , $iMapLeft , $iMapTop , 0, 0, $iMapWidth , $iMapHeight );
		$sOutFilename = 'DesInventar_ThematicMap_' . $options['Id'] . '.png';
		//header('Content-Disposition: attachment; filename= '. $sOutFilename);
		header('Content-Type: image/png');
		imagepng($imgAll);
		
		//Free memory
		imagedestroy($imgAll);
		imagedestroy($imgBase);
		imagedestroy($imgMap);
	break;
} //switch

function hex2dec($prmColor)
{
	$oHex = str_split(substr($prmColor, -6), 2);
	return hexdec($oHex[0]) . ' ' . hexdec($oHex[1]) . ' ' . hexdec($oHex[2]);
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
</script>
