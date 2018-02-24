<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
require_once('include/loader.php');
require_once('include/maps.class.php');
require_once('include/diregion.class.php');

use DesInventar\Legacy\DIRegion;
use DesInventar\Common\MapServer;
use DesInventar\Legacy\Maps;

$post = $_POST;
$get = $_GET;
$options = array();
// Always use http for KML files, Google earth doesn't load layers via https
$options['protocol_for_maps'] = 'http';
$options['protocol'] = 'http';
if (is_ssl()) {
    $options['protocol'] = 'https';
}
$url = $_SERVER['HTTP_HOST'] . $desinventarURL;
$options['url'] = $url;
$options = array_merge($options, $config->maps);
$cmd = getParameter('_M+cmd', getParameter('cmd', ''));
if ($cmd == '') {
    $cmd = 'result';
}
switch ($cmd) {
    case 'result':
        $RegionId = getParameter('_REG', getParameter('r', ''));
        if ($RegionId == '') {
            exit();
        }

        $us->open($RegionId);
        fixPost($post);
        $dic = array();
        $dic = array_merge($dic, $us->q->queryLabelsFromGroup('MapOpt', $lg));
        $dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
        $dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));
        // Process QueryDesign Fields and count results
        $qd = $us->q->genSQLWhereDesconsultar($post);
        $dic = array_merge($dic, $us->q->getEEFieldList('True'));
        $sqc = $us->q->genSQLSelectCount($qd);
        $c   = $us->q->getresult($sqc);
        $NumberOfRecords = $c['counter'];

        $t->assign('ctl_showres', false);
        if ($NumberOfRecords > 0) {
            // Assign ranges
            $mapserver = new MapServer($config);
            $range = $mapserver ->setRanges($post['_M+limit'], $post['_M+legend'], $post['_M+color']);
            // Data Options Interface
            $opc['Group'] = array($post['_M+Type']);
            $lev = explode('|', $post['_M+Type']);
            $opc['Field'] = $post['_M+Field'];
            $sql = $us->q->genSQLProcess($qd, $opc);
            // Apply Order fields to order legend too
            $v = explode('|', $opc['Field']);
            if ($v[0] == 'D.DisasterId') {
                $v[0] = 'D.DisasterId_';
            }
            $sql .= ' ORDER BY '. substr($v[0], 2) .' ASC';
            $info = $us->q->getQueryDetails($dic, $post);
            $info['NumberOfRecords'] = $NumberOfRecords;
            // get query results
            $dislist = $us->q->getassoc($sql);
            //$gitem = $us->q->getGeoCartoItems();
            // generate map
            $dl = $us->q->prepareList($dislist, 'MAPS');

            $MapId = time() . '-' . sprintf('%04d', rand(0, 9999));
            $options['id'] = $MapId;
            $t->assign('prmMapId', $MapId);

            // MAPS Query, RegionId, Level, datalist, ranges, dbinfo, label, maptype
            $m = new Maps(
                $us,
                $RegionId,
                $lev[0],
                $dl,
                $range,
                $info,
                $post['_M+Label'],
                $post['_M+Transparency'],
                'THEMATIC',
                $options,
                $config
            );
            $rinf = new DIRegion($us);
            $info['RECORDS'] = showStandardNumber($NumberOfRecords);
            $rgl[0]['regname'] = $rinf->get('RegionLabel');
            $rgl[0]['info'] = $info;
            // if valid filename then prepare interface to view MAPFILE
            if (strlen($m->filename()) > 0) {
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
                if (!empty($minx) && !empty($miny) && !empty($maxx) && !empty($maxy)) {
                    $lon = (int) (($minx + $maxx) / 2);
                    $lat = (int) (($miny + $maxy) / 2);
                    $aln[] = $minx;
                    $aln[] = $maxx;
                    $alt[] = $miny;
                    $alt[] = $maxy;
                } else {
                    $aln[] = 0;
                    $alt[] = 0;
                }
                $lnl[] = $lon;
                $ltl[] = $lat;
                $myly = '';
                if (isset($dl['CVReg'])) {
                    foreach (array_unique($dl['CVReg']) as $it) {
                        $myly .= $it .'effects,';
                    }
                    $myly = substr($myly, 0, -1);
                    $sLegendLayer='LAYER=' . reset(explode(',', $myly));
                } else {
                    $myly = 'effects';
                    $sLegendLayer = 'LAYER=' . $myly;
                }
                $rgl[0]['ly1'] = $myly;
                $rgl[0]['lv'] = $lev[0];
                $rgl[0]['map'] = str_replace('\\', '/', $m->filename());
            }
            if (isset($lnl) && isset($ltl)) {
                $t->assign('lon', array_sum($lnl)/count($lnl));
                $t->assign('lat', array_sum($ltl)/count($ltl));
                $zln = abs(max($aln) - min($aln));
                $zlt = abs(max($alt) - min($alt));
                $mx = ($zln == 0 || $zlt == 0) ? 1 : max($zln, $zlt);
                $zoom = round(log(180/$mx)) + 3;
                $t->assign('zoom', $zoom);
            }

            $glev = $us->q->loadGeoLevels('', -1, true);
            $t->assign('glev', $glev);
            $t->assign('rgl', $rgl);
            $t->assign('MapNumberOfRecords', $NumberOfRecords);
            $t->assign('qdet', $us->q->getQueryDetails($dic, $post));


            $mapfile = str_replace('\\', '/', $m->filename());
            $worldmap = str_replace('\\', '/', $config->maps['worldmap_dir'] . '/world_adm0.map');
            $timestamp = microtime(true);

            $sLegendURL = $m->getMapUrl($options['protocol']) . '/legend/?' . $sLegendLayer;
            $t->assign('legend', $sLegendURL);
            $t->assign('ctl_showres', true);
            $t->assign('reg', $RegionId);
            $t->assign('basemap', $worldmap);
            $t->assign('mps', MAPSERV);
        }
        $t->force_compile   = true; # Force this template to always compile
        $t->display('thematicmap.tpl');
        break;
    case 'getkml':
        $MapId = getParameter('MAPID', '');
        if ($MapId != '') {
            $sFilename = $config-paths['tmp_dir'] . '/map_' . $MapId . '.kml';
            if (file_exists($sFilename)) {
                $sOutFilename = 'DesInventar_ThematicMap_' . $MapId . '.kml';
                header('Content-type: text/kml');
                header('Content-Disposition: attachment; filename=' . $sOutFilename);
                echo file_get_contents($sFilename);
            }
        }
        break;
    case 'export':
        // Save image of an already created map
        $options = array_merge($options, $_POST['options']);
        foreach ($_GET as $key => $value) {
            $options[$key] = $value;
        }

        $mapserver = new MapServer($config);

        $iAllWidth = 1000;
        $iAllHeight = 760;

        // Create and assembly final image
        $imgAll = imagecreatetruecolor($iAllWidth, $iAllHeight);
        imagefilledrectangle(
            $imgAll,
            0,
            0,
            $iAllWidth - 1,
            $iAllHeight - 1,
            imagecolorallocate(
                $imgAll,
                192,
                192,
                192
            )
        );


        $font = getFont('arialbi.ttf');
        // Map Title Image
        $iTitleLeft   = 0;
        $iTitleTop    = 0;
        $iTitleWidth  = $iAllWidth;
        $iTitleHeight = 20;
        $imgTitle = imagecreatetruecolor($iTitleWidth, $iTitleHeight);
        $white = imagecolorallocate($imgTitle, 255, 255, 255);
        $black = imagecolorallocate($imgTitle, 0, 0, 0);
        imagefill($imgTitle, 0, 0, $white);
        $item = $options['title'];
        $bbox = imagettfbbox(11, 0, $font, $item);
        $iTitleMarginX = (imagesx($imgTitle) - ($bbox[2] - $bbox[0]) )/2;
        imagettftext($imgTitle, 11, 0, $iTitleMarginX, 13, $black, $font, $item);
        imagecopy($imgAll, $imgTitle, $iTitleLeft, $iTitleTop, 0, 0, $iTitleWidth, $iTitleHeight);

        // MapInfo Image - Query Info
        $fontsize = 10;
        $iInfoRows = count($options['info']);
        $iInfoWidth  = $iAllWidth;
        $iInfoHeight = ($fontsize + 2) * $iInfoRows + 2;
        $imgInfo = imagecreatetruecolor($iInfoWidth, $iInfoHeight);
        $white = imagecolorallocate($imgInfo, 255, 255, 255);
        $black = imagecolorallocate($imgInfo, 0, 0, 0);
        imagefill($imgInfo, 0, 0, $white);
        $iCount = 0;
        foreach ($options['info'] as $key => $value) {
            imagettftext($imgInfo, 10, 0, 0, ($fontsize + 2) * ($iCount + 1), $black, getFont('arial.ttf'), $value);
            $iCount++;
        }
        $iInfoTop    = $iAllHeight - imagesy($imgInfo);
        $iInfoLeft   = 0;
        imagecopy($imgAll, $imgInfo, $iInfoLeft, $iInfoTop, 0, 0, $iInfoWidth, $iInfoHeight);

        $iBaseLeft   = 0;
        $iBaseTop    = 0 + $iTitleHeight;
        $iBaseWidth  = $iAllWidth;
        $iBaseHeight = $iAllHeight - $iTitleHeight - $iInfoHeight;
        $sBaseUrl = $mapserver->getMapServerUrl($mapserver->getQueryString(array(
            'MAPID' => 'worldmap',
            'BBOX' => $options['extent'],
            'WIDTH' => $iBaseWidth,
            'HEIGHT' => $iBaseHeight
        )));
        $imgBase = imagecreatefromstring(file_get_contents($sBaseUrl));
        imagecopy($imgAll, $imgBase, $iBaseLeft, $iBaseTop, 0, 0, $iBaseWidth, $iBaseHeight);

        $iMapLeft    = $iBaseLeft;
        $iMapTop     = $iBaseTop;
        $iMapWidth   = $iBaseWidth;
        $iMapHeight  = $iBaseHeight;
        if ($options['layers'] != '') {
            $sMapUrl = $mapserver->getMapServerUrl($mapserver->getQueryString(array(
                'MAPID' => $options['id'],
                'BBOX' => $options['extent'],
                'WIDTH' => $iMapWidth,
                'HEIGHT' => $iMapHeight,
                'LAYERS' => $options['layers']
            )));
            $imgMap = imagecreatefromstring(file_get_contents($sMapUrl));
            imagecopy($imgAll, $imgMap, $iMapLeft, $iMapTop, 0, 0, $iMapWidth, $iMapHeight);
        }

        // Download and include legend
        $sLegendURL = $mapserver->getMapServerUrl($mapserver->getQueryString(array(
            'MAPID' => $options['id'],
            'LAYER' => 'effects',
            'REQUEST' => 'getlegendgraphic',
        )));
        $imgTmp = imagecreatefromstring(file_get_contents($sLegendURL));
        $fontsize = 11;
        $iLegendWidth = imagesx($imgTmp);
        $iLegendHeight = imagesy($imgTmp) + $fontsize + 2;
        $imgLegend = imagecreatetruecolor($iLegendWidth, $iLegendHeight);
        imagefill($imgLegend, 0, 0, imagecolorallocate($imgLegend, 255, 255, 255));
        imagecopy($imgLegend, $imgTmp, 0, $fontsize + 2, 0, 0, imagesx($imgTmp), imagesy($imgTmp));
        imagettftext(
            $imgLegend,
            10,
            0,
            4,
            2 + $fontsize,
            imagecolorallocate(
                $imgLegend,
                0,
                0,
                89
            ),
            'arialbi',
            $options['legendtitle']
        );

        $iLegendLeft = 5;
        $iLegendTop  = $iMapTop + $iMapHeight - (imagesy($imgLegend) + 5);
        imagecopy($imgAll, $imgLegend, $iLegendLeft, $iLegendTop, 0, 0, imagesx($imgLegend), imagesy($imgLegend));

        $gray = imagecolorallocate($imgAll, 192, 192, 192);
        imagerectangle($imgAll, $iBaseLeft, $iBaseTop, $iAllWidth - 1, $iBaseTop + $iBaseHeight, $gray);

        $mapfooter = trim('http://www.desinventar.org/' . ' - ' . $options['regionlabel']);
        $fontsize = 10;
        $bbox = imagettfbbox($fontsize, 0, $font, $mapfooter);
        $x = $bbox[2] - $bbox[0];
        $x = $iAllWidth - 2 - $x;
        $y = $iBaseTop + $iBaseHeight - 2;
        imagettftext($imgAll, $fontsize, 0, $x, $y, $black, $font, $mapfooter);

        $sOutFilename = 'DesInventar_ThematicMap_' . $options['id'] . '.png';
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename= '. $sOutFilename);
        imagepng($imgAll);

        //Free memory
        imagedestroy($imgAll);
        imagedestroy($imgTitle);
        imagedestroy($imgBase);
        imagedestroy($imgMap);
        imagedestroy($imgLegend);
        imagedestroy($imgInfo);
        break;
} //switch
