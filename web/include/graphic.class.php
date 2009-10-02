<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/
require_once(JPGRAPHDIR . "/jpgraph.php");
require_once(JPGRAPHDIR . "/jpgraph_line.php");
require_once(JPGRAPHDIR . "/jpgraph_log.php");
require_once(JPGRAPHDIR . "/jpgraph_date.php");
require_once(JPGRAPHDIR . "/jpgraph_bar.php");
require_once(JPGRAPHDIR . "/jpgraph_pie.php");
require_once(JPGRAPHDIR . "/jpgraph_pie3d.php");
require_once('include/math.class.php');
require_once('include/date.class.php');
class Graphic {
	var $g;
	var $sPeriod;
	var $sStat;
	/* opc [kind:BAR,LINE,PIE Opc:Title,etc] data:Matrix
	   data[0] == X, data[1] = Y1,  .. */
	public function Graphic ($opc, $data) {
		$kind = $opc['_G+Kind'];
		// Get Label Information
		$oLabels     = array_keys($data);
		$sXAxisLabel = current($oLabels);
		$sYAxisLabel = end($oLabels);
		$q = new Query($opc['_REG']);
		// Determine graphic type
		if (substr($opc['_G+Type'],2,18) == "DisasterBeginTime|") {
			$gType = "XTEMPO";				// One var x Event/Temporal..
			$sY2AxisLabel = $oLabels[1];
		}
		elseif (substr($opc['_G+Type'],2,17) == "DisasterBeginTime") {
			$gType = "TEMPO";				// One var x time
			// Set 2 axis graph only in Bars..
			if (isset($opc['_G+Field2']) && !empty($opc['_G+Field2']) && ($kind == "BAR" || $kind == "LINE"))
				$gType = "2TEMPO";			// Two vars x time
		}
		else {
			if (isset($opc['_G+Field2']) && !empty($opc['_G+Field2']) && ($kind == "BAR" || $kind == "LINE"))
				$gType = "2COMPAR";			// Two vars x event, cause...
			else
				$gType = $kind;				// Pie Comparatives
		}
		if ($gType == "2TEMPO" || $gType == "2COMPAR") {
			$sYAxisLabel = $oLabels[1];
			$sY2AxisLabel = $oLabels[2];
		}
		$val = array();
		// Cummulative Graph : Add Values in Graph
		if ($opc['_G+Mode'] == "ACCUMULATE") {
			$SumValue = 0;
			foreach ($data[$sYAxisLabel] as $Key=>$Value) {
				$SumValue += $Value;
				$val[$data[$sXAxisLabel][$Key]] = $SumValue;
			}
		}
		// get Period and Stationality of the Graph (YEAR, YMONTH, YWEEK, YDAY)
		if (isset($opc['_G+Period']))
			$this->sPeriod = $opc['_G+Period']; //$this->getGraphPeriod($opc['_G+Period']);
		if (isset($opc['_G+Stat']))
			$this->sStat = $opc['_G+Stat'];
		// MULTIBAR OR MULTILINE: reformat arrays completing time serie
		if ($gType == "XTEMPO") {
			if ($kind == "BAR")
				$kind = "MULTIBAR";
			elseif ($kind == "LINE")
				$kind = "MULTILINE";
			// Convert data in matrix [EVENT][YEAR]=>VALUE
			foreach ($data[$sY2AxisLabel] as $k=>$i) {
				foreach ($data[$sXAxisLabel] as $l=>$j) {
					if ($k == $l)
						$tvl[$i][$j] = $data[$sYAxisLabel][$k];
				}
			}
			foreach ($tvl as $kk=>$ii)
				$val[$kk] = $this->completeTimeSeries($opc, $ii, $q);
			$lbl = array_keys($val[$kk]);
			$acol = count(array_unique($data[$sY2AxisLabel]));
		} else {
			// Normal Graph (BAR, LINE, PIE)
			$n = 0;
			$acol = 1;
			// Set Array to [YEAR]=> { VALUE1, VALUE2 }  OR Set Array to [YEAR]=>VALUE
			foreach ($data[$sYAxisLabel] as $Key=>$Value) {
				if ($data[$sXAxisLabel][$n] != "00") {
					if ($gType == "2TEMPO" || $gType == "2COMPAR")
						$val[$data[$sXAxisLabel][$Key]] = array($Value, $data[$sY2AxisLabel][$Key]);
					else
						$val[$data[$sXAxisLabel][$Key]] = $Value;
					$acol++;
				}
				$n++;
			}
			//echo "<pre>"; print_r($data[$sXAxisLabel]);
			// Complete the data series for XAxis (year,month,day)
			if ($gType == "TEMPO" || $gType == "2TEMPO") {
				$val = $this->completeTimeSeries($opc, $val, $q);
			} elseif ($gType == "PIE") {
				// In Pie Graphs must order the values
				arsort($val, SORT_NUMERIC);
				reset($val);
			}
			$lbl = array_keys($val);
		}
		// Choose presentation options, borders, intervals
		$itv = 1;				// no interval
		if ($gType == "TEMPO" || $gType == "2TEMPO") {
			$rl = 70;			// right limit
			switch($this->sPeriod) {
				case "YEAR":		$bl = 50;	break;
				case "YWEEK":		$bl = 65;	break;
				case "YMONTH":		$bl = 65;	break;
				case "YDAY":		$bl = 85;	break;
				default:			$bl = 50;	break;
			}
		} elseif ($gType == "XTEMPO") {
			$rl = 160;		// right limit
			$bl = 50;		// bottom limit
		} else {
			$rl = 70;		// right limit
			$bl = 120;		// bottom limit more space to xlabels
		}
		// calculate graphic size
		$wx = 760;
		$hx = 520;
		// 1D Graphic - PIE
		if ($gType == "PIE") {
			$h = (24 * count($data[$sYAxisLabel]));
			if ($h > $hx)
				$hx = $h;
			$this->g = new PieGraph($wx, $hx, "auto");
			// Set label with variable displayed
			$t1 = new Text($sYAxisLabel);
			$t1->SetPos(0.30, 0.8);
			$t1->SetOrientation("h");
			$t1->SetFont(FF_ARIAL,FS_NORMAL);
			$t1->SetBox("white","black","gray");
			$t1->SetColor("black");
			$this->g->AddText($t1);
		} else {
			// 2D, 3D Graphic
			$w = (14 * count($data[$sXAxisLabel]));
			if ($w > $wx)
				$wx = $w;
			if ($wx > 1024)
				$wx = 1024;
			$this->g = new Graph($wx, $hx, "auto");
			if (isset($opc['_G+Scale'])) {
				$this->g->SetScale($opc['_G+Scale']); // textint, textlog
				$this->g->xgrid->Show(true,true);
				$this->g->xaxis->SetTitle($sXAxisLabel, 'middle');
				$this->g->xaxis->SetTitlemargin($bl - 20);
				$this->g->xaxis->title->SetFont(FF_ARIAL, FS_NORMAL);
				$this->g->xaxis->SetTickLabels($lbl);
				$this->g->xaxis->SetFont(FF_ARIAL,FS_NORMAL, 8);
				$this->g->xaxis->SetTextLabelInterval($itv);
				$this->g->xaxis->SetLabelAngle(90);
				$this->g->ygrid->Show(true,true);
				$this->g->yaxis->SetTitle($sYAxisLabel, 'middle');
				$this->g->yaxis->SetTitlemargin(40);
				$this->g->yaxis->title->SetFont(FF_ARIAL, FS_NORMAL);
				$this->g->yaxis->scale->SetGrace(0);
				$this->g->yaxis->SetColor('darkblue');
				if ($opc['_G+Scale'] == "textlog")
					$this->g->yaxis->scale->ticks->SetLabelLogType(LOGLABELS_PLAIN);
		        if (isset($opc['_G+Scale2']) && ($gType == "2TEMPO" || $gType == "2COMPAR")) {
					$this->g->SetY2Scale($opc['_G+Scale2']);	// int, log
					$this->g->y2grid->Show(true,true);
					$this->g->y2axis->SetTitle($sY2AxisLabel, 'middle');
					$this->g->y2axis->SetTitlemargin($rl - 20);
					$this->g->y2axis->title->SetFont(FF_ARIAL, FS_NORMAL);
					$this->g->y2axis->scale->SetGrace(0);
					$this->g->y2axis->SetColor('darkred');
					if ($opc['_G+Scale2'] == "log")
						$this->g->y2axis->scale->ticks->SetLabelLogType(LOGLABELS_PLAIN);
		        }
			} // if G+Scale
		}
		// 2009-02-03 (jhcaiced) Try to avoid overlapping labels in XAxis
		// by calculating the interval of the labels
		$iNumPoints = count($val);		
		$iInterval = ($iNumPoints * 14) / $wx;
		if ($iInterval < 1)
			$iInterval = 1;
		if ($gType != "PIE")
			$this->g->xaxis->SetTextLabelInterval($iInterval);
		// Other options graphic
		$this->g->img->SetMargin(50,$rl,30,$bl);
		$this->g->legend->SetAbsPos(5,5,'right','top');
		//$this->g->legend->Pos(0.0, 0.1);
		$this->g->legend->SetFont(FF_ARIAL, FS_NORMAL, 10);
		$this->g->SetFrame(false);
		$title = wordwrap($opc['_G+Title'], 80);
		$subti = wordwrap($opc['_G+Title2'], 100);
		$this->g->title->Set($title);
		$this->g->subtitle->Set($subti);
		$this->g->title->SetFont(FF_ARIAL,FS_NORMAL, 12);
		// Get color palette..
		if (substr_count($opc['_G+Type'], "Event") > 0)
			$pal = $this->genPalette($acol, DI_EVENT, array_keys($val), $q);
		elseif (substr_count($opc['_G+Type'], "Cause") > 0)
			$pal = $this->genPalette($acol, DI_CAUSE, array_keys($val), $q);
		elseif (substr_count($opc['_G+Type'], "Geography") > 0)
			$pal = $this->genPalette($acol, DI_GEOGRAPHY, array_keys($val), null);
		elseif ($gType == "TEMPO")
			$pal = "darkorange";
		else
			$pal = $this->genPalette($acol, "FIX", null, null);
		// Choose and draw graphic type
		if ($gType == "2TEMPO" || $gType == "2COMPAR") {
			$zo = array();
			$val1 = array();
			$val2 = array();
			foreach ($val as $ky=>$vl) {
				$zo[$ky] = 0;
				$val1[$ky] = $vl[0];
				$val2[$ky] = $vl[1];
			}
			$val = $val1;
		}
		switch ($kind) {
			case "BAR":
				if ($gType == "TEMPO" || $gType == "BAR") {
					$m = $this->bar($opc, $val, $pal);
					if (isset($opc['_G+Data']) && $opc['_G+Data'] == "VALUE") {
						$m->value->SetFont(FF_ARIAL, FS_NORMAL, 8);
						$m->value->SetFormat("%d");
						$m->value->SetAngle(90);
						$m->value->SetColor("black","darkred");
						$m->value->Show();
					}
				} elseif ($gType == "2TEMPO" || $gType == "2COMPAR") {
					$zp = $this->bar($opc, $zo, "");
					$y1 = $this->bar($opc, $val1, "darkblue");
					$y2 = $this->bar($opc, $val2, "darkred");
					$y1->SetLegend($sYAxisLabel);
					$y2->SetLegend($sY2AxisLabel);
					$y1p = new GroupBarPlot(array($y1, $zp));
					$y2p = new GroupBarPlot(array($zp, $y2));
					$this->g->Add($y1p);
					$this->g->AddY2($y2p);
				}
			break;
			case "LINE":
				if ($gType == "TEMPO" || $gType == "LINE") {
					$y1p = $this->line($opc, $val, $pal);
					$y1p->SetLegend($sYAxisLabel);
					// Add lineal regression 
					$std = new Math();
					$xx = array_fill(0, count($val), 0);
					$rl = $std->linearRegression(array_keys($xx), array_values($val));
					$n = 0;
					foreach ($val as $kk=>$ii) {
						$x = ($rl['m'] * $n) + $rl['b'];
						$linreg[] = ($x < 0) ? 0 : $x;
						$n++;
					}
					$ylr = $this->line($opc, $linreg, 'dashed');
					$ylr->SetLegend('Linnear Regression');
					$m[] = $y1p;
					$m[] = $ylr;
				}
				elseif ($gType == "2TEMPO" || $gType == "2COMPAR") {
					$y1p = $this->line($opc, $val1, "darkblue");
					$y2p = $this->line($opc, $val2, "darkred");
					$y1p->SetLegend($sYAxisLabel);
					$y2p->SetLegend($sY2AxisLabel);
					$this->g->Add($y1p);
					$this->g->AddY2($y2p);
				}
			break;
			case "MULTIBAR":
				$m = $this->multibar($opc, $val, $pal);
			break;
			case "MULTILINE":
				$m = $this->multiline($opc, $val, $pal);
			break;
			case "PIE":
				$m = $this->pie($opc, $val, $pal);
				if (isset($opc['_G+Data']) && $opc['_G+Data'] == "VALUE") {
					$m->SetLabelType(PIE_VALUE_ABS);
					$m->value->SetFormat("%d");
					$m->value->SetFont(FF_ARIAL, FS_NORMAL, 9);
				}
			break;
			default:
				$m = null;
			break;
		} //switch
		// Extra presentation options
		if (!empty($m)) {
			$this->g->footer->left->Set("DesInventar - http://www.desinventar.org");
			//$this->g->footer->right->Set("Fuentes: __________________________");
			if (is_array($m)) {
				foreach ($m as $m1)
					$this->g->Add($m1);
			}
			else
				$this->g->Add($m);
		}
	} // end function Graphic
	
	// This function creates the Graph in disk using all the curren parameters
	public function Stroke ($fname) {
		// Remove Old Graph is Exists
		if (file_exists($fname))
			unlink($fname);
		$this->g->Stroke($fname);
	}

	function getWeekOfYear ($sMyDate) {
		$iWeek = date("W", 
		  mktime(5, 0, 0, (int)substr($sMyDate,5,2),
		                  (int)substr($sMyDate,8,2),
		                  (int)substr($sMyDate,0,4)));
		return $iWeek;
	}
	
	function completeTimeSeries($opc, $val, $q) {
		$dateini = "";
		$dateend = "";
		// Get range of dates from Database
		//print_r($opc);
		$qini = $opc['D_DisasterBeginTime'];
		$qend = $opc['D_DisasterEndTime'];
		$ydb = $q->getDateRange();
		if (isset($qini[0])) {
			// If no month/day value specified, set default date to YEAR/01/01 or start of month
			if ($qini[1] == '') { $qini[1] = '1'; }
			if ($qini[2] == '') { $qini[2] = '1'; }
			$dateini = sprintf("%04d-%02d-%02d", $qini[0], $qini[1], $qini[2]);
		} else {
			$dateini = $ydb[0];
		}
		if (isset($qend[0])) {
			// If no month/day value specified in query, set default to YEAR/12/31 or end of month
			if ($qend[1] == '') { $qend[1] = '12'; }
			if ($qend[2] == '') { $qend[2] = DIDate::getLastDayOfMonth($qend[0],$qend[1]); }
			$dateend = sprintf("%04d-%02d-%02d", $qend[0], $qend[1], $qend[2]);
		} else {
			$dateend = $ydb[0];
		}
		// Calculate Start Date/EndDate, from Database or From Query
		// Delete initial columns with null values (MONTH,DAY=0)
		if (isset($val[0]) || isset($val['']))
			$val = array_slice($val, 1, count($val), true);
		// Generate YEAR, MONTH, WEEK, DAY series..
		if (empty($this->sStat)) {
			// Fill data series with zero; Year Loop (always execute)
			for ($iYear = substr($dateini, 0, 4); $iYear <= substr($dateend, 0, 4); $iYear++) {
					$sDate = sprintf("%04d", $iYear);
				if ($this->sPeriod == "YEAR") {
					if (!isset($val[$sDate]))
						$val[$sDate] = 0;
				} elseif ($this->sPeriod == "YWEEK")
					$this->completeWeekSeries($dateini, $dateend, $iYear, $val);
				else
					$this->completeMonthSeries($dateini, $dateend, $iYear, $val);
			}
		} else {
			// MultiPeriod Graphs
			if ($this->sStat == "DAY")
				$this->completeDaySeries($dateini, $dateend, "", 0, $val);
			elseif ($this->sStat == "WEEK")
				$this->completeWeekSeries($dateini, $dateend, "", $val);
			elseif ($this->sStat == "MONTH")
				$this->completeMonthSeries($dateini, $dateend, "", $val);
		}
		// Reorder XAxis Labels
		ksort($val);
		reset($val);
		return $val;
	}
  
	function completeWeekSeries($dateini, $dateend, $iYear, &$val) {
		$iWeekIni =  1;
		$sDate = sprintf("%04d-12-31", $iYear);
		$iWeekEnd = $this->getWeekOfYear($sDate);
		if ($iYear == substr($dateini, 0, 4))
			$iWeekIni = $this->getWeekOfYear($dateini);
		if ($iYear == substr($dateend, 0, 4))
			$iWeekEnd = $this->getWeekOfYear($dateend);
		for ($iWeek = $iWeekIni; $iWeek <= $iWeekEnd; $iWeek++) {
			if ($this->sPeriod == "YWEEK")
				$sDate = sprintf("%04d-%02d", $iYear, $iWeek);
			elseif ($this->sStat == "WEEK")
				$sDate = sprintf("%02d", $iWeek);
			if (!isset($val[$sDate]))
				$val[$sDate] = 0;
		}
		return;
	}
  
	function completeMonthSeries($dateini, $dateend, $iYear, &$val) {
		$iMonthIni =  1;
		$iMonthEnd = 12;
		if ($iYear == substr($dateini, 0, 4))
			$iMonthIni = substr($dateini, 5, 2);
		if ($iYear == substr($dateend, 0, 4))
			$iMonthEnd = substr($dateend, 5, 2);
		for ($iMonth = $iMonthIni; $iMonth <= $iMonthEnd; $iMonth++) {
			if ($this->sPeriod == "YDAY")
				$this->completeDaySeries($dateini, $dateend, $iYear, $iMonth, $val);
			else {
				if ($this->sPeriod == "YMONTH")
					$sDate = sprintf("%04d-%02d", $iYear, $iMonth);
				elseif ($this->sStat == "MONTH")
					$sDate = sprintf("%02d", $iMonth);
				if (!isset($val[$sDate]))
					$val[$sDate] = 0;
			}
		}
		return;
	}
  
	function completeDaySeries($dateini, $dateend, $iYear, $iMonth, &$val) {
		$iDayIni = 1;
		$iDayEnd = 30;
		$sDate = sprintf("%04d-%02d", $iYear, $iMonth);
		if ($sDate == substr($dateini, 0, 7))
			$iDayIni = substr($dateini, 8, 2);
		if ($sDate  == substr($dateend, 0, 7))
			$iDayEnd = substr($dateend, 8, 2);
		if ($this->sStat == "DAY") {
			$iDayIni = 1;
			$iDayEnd = 366;
		}
		for ($iDay = $iDayIni; $iDay <= $iDayEnd; $iDay = $iDay + 1) {
			if ($this->sPeriod == "YDAY")
				$sDate = sprintf("%04d-%02d-%02d", $iYear, $iMonth, $iDay);
			elseif ($this->sStat == "DAY")
				$sDate = sprintf("%03d", $iDay);
			if (!isset($val[$sDate]))
				$val[$sDate] = 0;
		}
		return;
	}
                                                                                        
	// Setting a PIE graphic
	function pie ($opc, $axi, $pal) {
		if ($opc['_G+Feel'] == "3D") {
			$p = new PiePlot3d(array_values($axi));
			$p->SetEdge("navy");
			$p->SetStartAngle(45);
			$p->SetAngle(55);
		}
		else
			$p = new PiePlot(array_values($axi));
		$p->SetSliceColors($pal);
		$p->SetCenter(0.32, 0.3);
		$p->SetSize(0.22);
		$tt = array_sum($axi);
		foreach ($axi as $k=>$i) {
			$per = sprintf("%.1f", 100*($i/$tt));
			$leg[] = "$k : $i ($per%%)";
		}
		$p->SetLegends($leg);
		return $p;
	}
  
	// Setting a Bar Graphic
	function bar ($opc, $axi, $color) {
		$b = new BarPlot(array_values($axi));
		// normal histogram..
		if (is_array($color)) {
			$b->SetFillColor($color);
			$b->SetWidth(0.8);
		} else {
			if ($color == "darkorange")
				$b->SetFillGradient($color, 'white', GRAD_VER);
			else
				$b->SetFillColor($color);
			$b->SetWidth(1.0);
		}
		if ($opc['_G+Feel'] == "3D")
			$b->SetShadow("steelblue",2,2);
		return $b;
	}

	// Setting a Multibar graphic
	function multibar ($opc, $axi, $pal) {
		$i = 0;
		$lab = array_keys($axi);
		foreach ($axi as $k=>$ele) {
			$bar = $this->bar($opc, $ele, $pal[$i]);
			$bar->SetLegend($lab[$i]);
			$b[] = $bar;
			$i++;
		}
		if ($opc['_G+Mode'] == "OVERCOME")
			$gb = new AccBarPlot($b);
		else
			$gb = new GroupBarPlot($b);
		$gb->SetWidth(0.98);
		return $gb;
	}

	// Setting a Line graphic
	function line ($opc, $axi, $col) {
		$l = new LinePlot(array_values($axi));
		if ($col == "dashed") {
			$l->SetColor('darkred');
			$l->SetStyle('dashed'); 
		}
		else
			$l->SetColor($col);
			//$l->SetFillGradient($col,'white');
			//$l->SetColor($col);
			//$l->mark->SetFillColor("red");
			//$l->mark->SetWidth(2);
		return $l;
	}

	// Setting a Multiline graphic
	function multiline ($opc, $axi, $pal) {
		$i = 0;
		$lab = array_keys($axi);
		foreach ($axi as $k=>$ele) {
			$line = $this->line($opc, $ele, $pal[$i]);
			$line->SetLegend($lab[$i]);
			$l[] = $line;
			$i++;
		}
		if ($opc['_G+Mode'] == "OVERCOME")
			$gl = new AccLinePlot($l);
		else
			$gl = $l;
		return $gl;
	}

	// Generate colors from database attrib-color or generate fix palette..
	function genPalette ($cnt, $mode, $evl, $qy) {
		$pal = array();
		if ($mode == DI_EVENT || $mode == DI_CAUSE) {
			// Find in database color attribute
			foreach ($evl as $k) {
				$col = $qy->getObjectColor($k, $mode);
				if (trim($col) == "")
				  $col = dechex(rand(0, 255)) . dechex(rand(0, 255)) . dechex(rand(0, 255));
				$pal[] = "#". $col;
			}
		} else {
			$col = array("#0000ff","#00ff00", "#ff0000", "#ff00ff", "#00ffff", "#ffff00",
					   "#c7c7ff","#c782c7", "#ff7f7f", "#ffc7ff", "#c7ffff", "#ffffc7",
					   "#00007f","#007f00", "#7f0000", "#7f007f", "#007f7f", "#827f00");
			$j = 0;
			for ($i=0; $i < $cnt; $i++) {
				if ($j >= count($col))
					$j = 0;
				$pal[] = $col[$j];
				$j++;
			}
		}
		/*		// Generate a Degradee palette 
		  $cl1 = array(20,   20, 200); // blue
		  $cl2 = array(200, 130,  20); // orange
		  $v1 = (($cl2[0] - $cl1[0]) / $cnt);
		  $v2 = (($cl2[1] - $cl1[1]) / $cnt);
		  $v3 = (($cl2[2] - $cl1[2]) / $cnt);
		  $med = array($v1, $v2, $v3);
		  for ($i=1; $i <= $cnt; $i++) {
			$h1 = dechex($cl1[0] + (int)($med[0] * $i));
			$h2 = dechex($cl1[1] + (int)($med[1] * $i));
			$h3 = dechex($cl1[2] + (int)($med[2] * $i));
			$pal[] = "#". $h1 . $h2 . $h3;
		  }
		  $r = array(0, 0, 200);
		  $g = array(0, 200, 0);
		  $b = array(200, 0, 0);*/
		return $pal;
	}

/*
	public function getGraphPeriod ($prmOption) {
		$Index = strrpos($prmOption, "-");
		if ($Index == FALSE)
		  $Index = 0; 
    else
      $Index += 1;
		$sGraphPeriod = substr($prmOption,$Index);
		if ($sGraphPeriod == "")
		  $sGraphPeriod = "YEAR";
		return $sGraphPeriod;
	}*/

} // end class

</script>
