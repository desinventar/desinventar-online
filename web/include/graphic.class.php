<script language="php">
/*
 ***********************************************
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 **********************************************
*/

require_once(JPGRAPHDIR . "/jpgraph.php");
require_once(JPGRAPHDIR . "/jpgraph_line.php");
require_once(JPGRAPHDIR . "/jpgraph_log.php");
require_once(JPGRAPHDIR . "/jpgraph_date.php");
require_once(JPGRAPHDIR . "/jpgraph_bar.php");
require_once(JPGRAPHDIR . "/jpgraph_pie.php");
require_once(JPGRAPHDIR . "/jpgraph_pie3d.php");
require_once('../include/math.class.php');

class Graphic 
{
  /* Type BAR,LINE,PIE; Opc:Title,etc; data:Matrix,
      data[0] == X, data[1] = Y1,  .. */
  public function Graphic ($type, $opc, $data) 
  {
    #$fname = TMP_DIR . "/di8graphic_". $_SESSION['sessionid'] ."_.png";
    #$fname = GRAPHS_DIR . "/di8graphic_". $_SESSION['sessionid'] ."_.png";
    $fname = GRAPHS_DIR . "/di8graphic_". session_id() ."_.png";
    if (file_exists($fname))
      unlink($fname);
    // get Labels
    $lab = array_keys($data);
    $xlab = current($lab);
    $ylab = end($lab);
    $val = array();
    $acol = 1;
    // Accumulate values: Sum values..
    if ($opc['_G+Mode'] == "ACCUMULATE") {
      $acc = 0;
      foreach ($data[$ylab] as $k=>$it) {
        $acc += $it;
        $val[$data[$xlab][$k]] = $acc;
      }
    }
    // Reformat arrays to set a multiple bar
    elseif (count($lab) == 3) {
      if ($type == "BAR")
        $type = "MULTIBAR";
      if ($type == "LINE")
        $type = "MULTILINE";
      // Classify Events, Geo, Cause by temporaly
      $y2lab = $lab[1];
      $y = "";
      // convert data in matrix [EVENT][YEAR]=>VALUE
      foreach ($data[$y2lab] as $k=>$i) {
        foreach ($data[$xlab] as $l=>$j) {
          if ($k == $l)
            $tvl[$i][$j] = $data[$ylab][$k];
        }
      }
      // create complete matrix NxM, fill with 0's unassigned values..
      foreach (array_unique($data[$xlab]) as $it)
        $xvl[$it] = 0;
      foreach ($tvl as $k=>$v) {
        $res = $v;
        foreach ($xvl as $l=>$w) {
          if (!isset($res[$l]))
            $res[$l] = 0;
        }
        ksort($res, SORT_NUMERIC);
        reset($res);
        $val[$k] = $res;
      }
      //print_r($val);
      $acol = count(array_unique($data[$y2lab]));
    }
    // Set Array to [YEAR]=>VALUE
    else {
      foreach ($data[$ylab] as $k=>$it) {
        $val[$data[$xlab][$k]] = $it;
        $acol++;
      }
      // Order values to pie
      if ($type == "PIE") {
        arsort($val, SORT_NUMERIC);
        reset($val);
      }
      elseif (substr($opc['_G+Type'],2,17) == "DisasterBeginTime") {
        // Complete series in time (day, month, year)..
        // get range of dates
        $q = new Query($opc['_REG']);
        $ydb = $q->getDateRange();
        if (isset($opc['D:DisasterBeginTime'][0]))
          $dateini = (int)(empty($opc['D:DisasterBeginTime'][0]) ? substr($ydb[0], 0, 4) : $opc['D:DisasterBeginTime'][0]);
        else {
          $dateini = current(array_keys($val));
          if (!is_int($dateini))
            $dateini = next(array_keys($val));	// ignore first null value
        }
        if (isset($opc['D:DisasterEndTime'][0]))
          $dateend = (int)(empty($opc['D:DisasterEndTime'][0]) ? substr($ydb[1], 0, 4) : $opc['D:DisasterEndTime'][0]);
        else
          $dateend = end(array_keys($val));
        // delete column with null values (MONTH,DAY=0)
        if (isset($val[0]) || isset($val['']))
          $val = array_slice($val, 1, count($val), true);
        // Valid integer dates and exclude seasons (MONTH, WEEK, DAY.)
        if (is_int($dateini) && is_int($dateend) && empty($opc['_G+Stat'])) {
          for ($dte = $dateini; $dte <= $dateend; $dte++) {
            if (!isset($val[$dte]))
              $val[$dte] = 0;
          }
          ksort($val);
          reset($val);
        }
      }
    }
    // Choose presentation options, borders, intervals
    if (substr($opc['_G+Type'], 2, 19) == "DisasterBeginTime") {
      $grp = "SINGLE";
      if ($opc['_G+Period'] != "YEAR")
        $itv = 1;			// Interval 2
      else
        $itv = 1;
      //if ($opc['_G+Stat'] == "");
      $rl = 40;			// right limit
      $bl = 50;			// bottom limit
    }
    elseif (substr($opc['_G+Type'],2,18) == "DisasterBeginTime|") {
      $grp = "MULTIPLE";
      $itv = 1;			// no interval 
      $rl = 160;		// right limit
      $bl = 50;			// bottom limit
    }
    else {
      $grp = "COMPARATIVE";
      $itv = 1;			// no interval
      $rl = 30;			// right limit
      $bl = 120;		// bottom limit more space to xlabels
    }
    // calculate graphic size
    $wx = 760;
    $hx = 520;
    if ($type == "PIE") {
      $h = (24 * count($data[$ylab]));
      if ($h > $hx) 
        $hx = $h;
      $g = new PieGraph($wx, $hx, "auto");
      // Set label with variable displayed
      $t1 = new Text($ylab);
      $t1->SetPos(0.30, 0.8);
      $t1->SetOrientation("h");
      $t1->SetFont(FF_ARIAL,FS_NORMAL);
      $t1->SetBox("white","black","gray");
      $t1->SetColor("black");
      $g->AddText($t1);
    }
    else {
      $w = (12 * count($data[$xlab]));
      if ($w > $wx) 
        $wx = $w;
      $g = new Graph($wx, $hx, "auto");
      if (isset($opc['_G+Scale'])) {
        $g->SetScale($opc['_G+Scale']); // textlin , textlog
        $g->ygrid->Show(true,true);
        $g->xgrid->Show(true,true);
        $g->xaxis->SetTitle($xlab, 'middle');
        $g->xaxis->SetTitlemargin($bl - 30);
        $g->xaxis->title->SetFont(FF_ARIAL, FS_NORMAL);
        if ($type == "MULTIBAR" || $type == "MULTILINE") {
          foreach (array_unique($data[$xlab]) as $el)
            $lbl[] = $el;
          $g->xaxis->SetTickLabels($lbl);
        }
        else
          $g->xaxis->SetTickLabels(array_keys($val));
        $g->xaxis->SetFont(FF_ARIAL,FS_NORMAL, 8);
        $g->xaxis->SetTextLabelInterval($itv);
        $g->xaxis->SetLabelAngle(90);
        $g->yaxis->SetTitle($ylab, 'middle');
        $g->yaxis->SetTitlemargin(40);
        $g->yaxis->title->SetFont(FF_ARIAL, FS_NORMAL);
        if ($opc['_G+Scale'] == "textlog")
          $g->yaxis->scale->ticks->SetLabelLogType(LOGLABELS_PLAIN);
      }
    }
    // get color palette..
    if ($xlab == "Eventos" || (isset($y2lab) && $y2lab == "Eventos")) {
      $pal = $this->genPalette($acol, "BYEVENT", array_keys($val));
    }
    elseif ($grp == "SINGLE")
      $pal = "orange"; //$this->genPalette($acol, "DEG");
    else
      $pal = $this->genPalette($acol, "FIX", null);
    // Other options graphic
    $g->img->SetMargin(50,$rl,30,$bl);
    $g->legend->Pos(0.0, 0.1);
    $g->legend->SetFont(FF_ARIAL, FS_NORMAL, 10);
    $g->SetFrame(false);
    $title = wordwrap($opc['_G+Title'], 80);
    $subti = wordwrap($opc['_G+Title2'], 100);
    $g->title->Set($title);
    $g->subtitle->Set($subti);
    $g->title->SetFont(FF_ARIAL,FS_NORMAL, 12);
    //print_r($val);
    // Choose and draw graphic type
    switch ($type) {
      case "BAR":
        $m = $this->bar($opc, $val, $pal);
      break;
      case "LINE":
        $m[] = $this->line($opc, $val, $pal);
        // add linnear regression 
        $std = new Math();
        $xx = array_fill(0, count($val), 0);
        $rl = $std->linearRegression(array_keys($xx), array_values($val));
        $n = 0;
        foreach ($val as $kk=>$ii) {
          $x = ($rl['m'] * $n) + $rl['b'];
          $linreg[] = ($x < 0) ? 0 : $x;
          $n++;
        }
        $m[] = $this->line($opc, $linreg, 'single');
      break;
      case "MULTIBAR":
        $m = $this->multibar($opc, $val, $pal);
      break;
      case "MULTILINE":
        $m = $this->multiline($opc, $val, $pal);
      break;
      case "PIE":
        $m = $this->pie($opc, $val, $pal);
      break;
      default:
        $m = null;
      break;
    }
    // Extra presentation options
    if (!empty($m)) {
      if (isset($opc['_G+Data']) && $opc['_G+Data'] == "VALUE") {
        if ($type == "PIE") {
          $m->SetLabelType(PIE_VALUE_ABS);
          $m->value->SetFormat("%d");
          $m->value->SetFont(FF_ARIAL, FS_NORMAL, 6);
        }
        elseif ($type == "BAR" || $type == "LINE") {
          $m->value->SetFont(FF_ARIAL, FS_NORMAL, 7);
          $m->value->SetFormat("%d");
          $m->value->SetAngle(90);
          $m->value->SetColor("black","darkred");
          $m->value->Show();
        }
      }
      $g->footer->left->Set("DesInventar8 - http://online.desinventar.org");
      //$g->footer->right->Set("Fuentes: __________________________");
      if (is_array($m))
        foreach ($m as $m1)
          $g->Add($m1);
      else
        $g->Add($m);
      $g->Stroke($fname);
    }
  }
  
  // Setting a PIE graphic
  function pie($opc, $axi, $pal) {
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
  
  function bar($opc, $axi, $color) {
    $b = new BarPlot(array_values($axi));
    // normal histogram..
    if (is_array($color)) {
      $b->SetFillColor($color);
      $b->SetWidth(0.8);
    }
    else {
      if ($color == "orange")
        $b->SetFillGradient($color, 'white', GRAD_VER);
      else
        $b->SetFillColor($color);
      $b->SetWidth(1.0);
    }
    if ($opc['_G+Feel'] == "3D")
      $b->SetShadow("steelblue",2,2);
    return $b;
  }

  function multibar ($opc, $axi, $pal) {
    $i = 0;
    $lab = array_keys($axi);
    foreach ($axi as $k=>$ele) {
      $bar = $this->bar($opc, $ele, $pal[$i]);
      $bar->SetLegend($lab[$i]);
      $b[] = $bar;
      $i++;
    }
    //print_r($axi);
    if ($opc['_G+Mode'] == "OVERCOME")
      $gb = new AccBarPlot($b);
    else
      $gb = new GroupBarPlot($b);
    $gb->SetWidth(0.98);
    return $gb;
  }

  function line ($opc, $axi, $col) {
    $l = new LinePlot(array_values($axi));
    if ($col == 'single')
      $l->SetColor('blue');
    else
      $l->SetFillGradient($col,'white');
      //$l->SetColor($col);
      //$l->mark->SetFillColor("red");
      //$l->mark->SetWidth(2);
    return $l;
  }

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
  
  function genPalette($cnt, $gen, $evl) {
    $col = array("#0000ff","#00ff00", "#ff0000", "#ff00ff", "#00ffff", "#ffff00",
                 "#c7c7ff","#c782c7", "#ff7f7f", "#ffc7ff", "#c7ffff", "#ffffc7",
                 "#00007f","#007f00", "#7f0000", "#7f007f", "#007f7f", "#827f00");
    if ($gen == "FIX") {
      $r = array(0, 0, 200);
      $g = array(0, 200, 0);
      $b = array(200, 0, 0);
      $j = 0;
      for ($i=0; $i < $cnt; $i++) {
        if ($j >= count($col))
          $j = 0;
        $pal[] = $col[$j];
        $j++;
      }
    }
    elseif ($gen == "BYEVENT") {
			foreach ($evl as $item) {
				switch($item) {
				  case "INUNDACION":
				    $col[] = "#0000ff";
				  break;
				  case "INCENDIO":
				    $col[] = "#f91717";
          break;
          case "FORESTAL":
            $col[] = "#fd7b3b";
          break;
          case "AVENIDA":
            $col[] = "#3bfdd4";
          break;
          case "TEMPESTAD":
            $col[] = "#bc3bfd";
          break;
          case "VENDAVAL":
            $col[] = "#fd3bed";
          break;
          case "LLUVIAS":
            $col[] = "#8080ef";
          break;
          case "DESLIZAMIENTO":
            $col[] = "#2cdc2c";
          break;
          case "EPIDEMIA":
            $col[] = "#a9aeae";
          break;
				  case "ALUVION":
				    $col[] = "#6d5008";
          break;
          case "SEQUIA":
            $col[] = "#e8cd12";
          break;
          case "SISMO":
            $col[] = "#08740a";
          break;
          case "GRANIZADA":
            $col[] = "#a9dced";
          break;
          case "PLAGA":
            $col[] = "#cccccc";
          break;
        }
      }
      //echo "<PRE>"; print_r($evl); print_r($col); echo "</PRE>";
      $j = 0;
      for ($i=0; $i < $cnt; $i++) {
        if ($j >= count($col))
          $j = 0;
        $pal[] = $col[$j];
        $j++;
      }
    }
    // generate degradee palette 
    else {
      $pal = array();
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
    }
    //print_r($pal);
    return $pal;
  }

} //end class
