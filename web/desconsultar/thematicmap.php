<!--
	/*
	DesInventar8 - http://www.desinventar.org
	(c) 1999-2008 Corporacion OSSO
	*/
-->
<script language="php">
	require_once('../include/loader.php');
	require_once('../include/dictionary.class.php');
	require_once('../include/query.class.php');
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
		// generate range hash with limit, legend and color
		for ($j = 0; $j < count($lim); $j++) {
			if (isset($lim[$j])) {
				if ($lim[$j] != "") {
					$range[$j] = array($lim[$j], $leg[$j], hex2dec($col[$j]));
				} else {
					$range[$j] = array($lmx, $leg[$j], hex2dec($col[$j]));
					$maxr = true;
				} // else
			} // if
		} // for
		
		// if not assigned, set last range between last number and infinit
		if (!$maxr) {
			$range[$j] = array($lmx, (int)$lim[$j-1] + 1 . " -> ", "30 30 30");
		}
		return $range;
	}

	$post = $_POST;
	$get  = $_GET;

	if (isset($post['_REG']) && !empty($post['_REG'])) {
		$reg = $post['_REG'];
		if (isset($post['_VREG']) && $post['_VREG'] == "true") {
			$q = new Query('');
		} else {
			$q = new Query($reg);
		} // else
	} elseif (isset($get['r']) && !empty($get['r'])) {
		$reg = $get['r'];
		$q = new Query($reg);
	} else {
		exit();
	}

	$d = new Dictionary(DICT_DIR);
	$dic = array();
	$dic = array_merge($dic, $d->queryLabelsFromGroup('MapOpt', $lg));
	$dic = array_merge($dic, $d->queryLabelsFromGroup('Effect', $lg));
	$dic = array_merge($dic, $d->queryLabelsFromGroup('Sector', $lg));

	if (isset($post['_M+cmd'])) {
		// Process QueryDesign Fields and count results
		$qd	= $q->genSQLWhereDesconsultar($post);
		$cou = 0;
		if (isset($post['_VREG']) && $post['_VREG'] == "true") {
			$areg = $q->getVirtualRegItems($reg);
		} else {
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
		} else {
			$areg = (array)$reg;
			$t->assign ("reg", $reg);
			$glev = $q->loadGeoLevels("map");
		}

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
				if ($v[0] == "D.DisasterId") {
					$v[0] = "D.DisasterId_";
				}
				$sql .= " ORDER BY ". substr($v[0],2);
				$dislist = $q3->getassoc($sql);
				
				// get query results
				//if (!empty($dislist)) {
				// generate map
				$dl = $q3->prepareMaps($dislist);
				$info = $q3->getQueryDetails($dic, $post);
				
				// MAPS Object, RegionId, Level, datalist, ranges, dbinfo, label, maptype
				$m = new Maps($q3, $rg, $lev[0], $dl, $range, $info, $post['_M+Label'], "THEMATIC");
				$rgl[$kg]['info'] = $info;
				// if valid filename then prepare interface to view MAPFILE
				if (strlen($m->filename()) > 0) {
					$lon = 0;
					$lat = 0;
					//$dinf = $q->getDBInfo();
					// set center
					if (!empty($rinf['GeoLimitMinX']) && !empty($rinf['GeoLimitMinY']) &&
						!empty($rinf['GeoLimitMaxX']) && !empty($rinf['GeoLimitMaxY'])) {
						$lon = (int) (($rinf['GeoLimitMinX'] + $rinf['GeoLimitMaxX']) / 2);
						$lat = (int) (($rinf['GeoLimitMinY'] + $rinf['GeoLimitMaxY']) / 2);
						$aln[] = $rinf['GeoLimitMinX'];	$aln[] = $rinf['GeoLimitMaxX'];
						$alt[] = $rinf['GeoLimitMinY']; $alt[] = $rinf['GeoLimitMaxY'];
					}
					$lnl[] = $lon;
					$ltl[] = $lat;
					$rgl[$kg]['ly1'] = "effects";
					$rgl[$kg]['lv'] = $lev[0];
					$rgl[$kg]['map'] = $m->filename();
				}
			} // foreach
			
			if (isset($lnl) && isset($ltl)) {
				$t->assign ("lon", array_sum($lnl)/count($lnl));
				$t->assign ("lat", array_sum($ltl)/count($ltl));
				$zln = abs(max($aln) - min($aln));
				$zlt = abs(max($alt) - min($alt));
				$zoom = round(log(180/max($zln, $zlt))) + 3;
				$t->assign ("zoom", $zoom);
			}
			
			$t->assign ("glev", $glev);
			$t->assign ("rgl", $rgl);
			$t->assign ("tot", $cou);
			$t->assign ("qdet", $q3->getQueryDetails($dic, $post));
			
			if ($post['_M+cmd'] == "export" && !(isset($post['_VREG']) && $post['_VREG'] == "true")) {
				$dinf = $q->getDBInfo();
				$regname = $dinf['RegionLabel'];
				
				if (!empty($dinf['GeoLimitMinX']) && !empty($dinf['GeoLimitMinY']) &&
					!empty($dinf['GeoLimitMaxX']) && !empty($dinf['GeoLimitMaxY'])) {
					$minx = $dinf['GeoLimitMinX'];
					$maxx = $dinf['GeoLimitMaxX'];
					$miny = $dinf['GeoLimitMinY'];
					$maxy = $dinf['GeoLimitMaxY'];
					$w = (int) ($maxx - $minx) * 50;
					$h = (int) ($maxy - $miny) * 50;
					$t->assign("mapfilename", $m->filename());
					$url = "/cgi-bin/mapserv?map=". $m->filename() . 
					  "&SERVICE=WMS&VERSION=1.1.1".
					  "&layers=effects&REQUEST=getmap&STYLES=&SRS=EPSG:4326" .
					  "&BBOX=$minx,$miny,$maxx,$maxy&WIDTH=$w&HEIGHT=$h&FORMAT=image/png";
					$mf = file_get_contents("http://". $_SERVER['HTTP_HOST'] . $url);
					
					if ($mf) {
						header("Content-type: Image/png");
						header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $regname) ."_ThematicMap.png");
						echo $mf;
					} // if
				} // if 
			} else {
				$t->assign ("ctl_showres", true);
			} // else
		}
	} elseif (isset($get['cmd']) && $get['cmd'] == "getkml") {
		header("Content-type: text/kml");
		header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $reg) ."_ThematicMap.kml");
		$m = new Maps($q, $reg, null, null, null, null, null, "KML");
		echo $m->printKML();
		exit();
	}
	$t->assign ("dic", $dic);
	$t->assign ("basemap", "/usr/share/desinventar/worldmap/worldmap.map");

	if (LNX) {
		$t->assign ("shw_server", true);
		$t->assign ("mps", "mapserv");
	} else {
		$t->assign ("shw_server", false);
		$t->assign ("mps", "mapserv.exe");
	}

	$t->display ("thematicmap.tpl");

</script>
