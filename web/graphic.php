<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/graphic.class.php');

$post = $_POST;

$reg = getParameter('_REG','');

if ($reg == '') {
	exit();
}

$us->open($reg);

$RegionLabel = $us->q->getDBInfoValue('RegionLabel');
$t->assign('RegionLabel', $RegionLabel);
fixPost($post);
// load levels to display in totalizations
foreach ($us->q->loadGeoLevels('', -1, false) as $k=>$i) {
	$st["GraphGeographyId_". $k] = array($i[0], $i[1]);
} //foreach

$dic = array_merge(array(), $st);
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Graph', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));
$dic = array_merge($dic, $us->q->getEEFieldList("True"));
//$t->assign ("dic", $dic);
$GraphCommand = getParameter('_G+cmd');
if ($GraphCommand != '') {
	// Process QueryDesign Fields and count results
	$qd  = $us->q->genSQLWhereDesconsultar($post);
	$sqc = $us->q->genSQLSelectCount($qd);	
	$c   = $us->q->getresult($sqc);
	$NumRecords = $c['counter'];
	$t->assign ("NumRecords", $NumRecords);
	
	// Process Configuration options to Graphic
	$ele = array();
	foreach (explode("|", $post['_G+Type']) as $itm) {
		if ($itm == "D.DisasterBeginTime") {
			// Histogram
			if (isset($post['_G+Stat']) && strlen($post['_G+Stat'])>0) {
				$ele[] = $post['_G+Stat'] ."|". $itm;
			} else {
				$ele[] = $post['_G+Period'] ."|". $itm;
			}
		} elseif (substr($itm, 2, 11) == "GeographyId") {
			$gl = explode("_", $itm);
			$ele[] = $gl[1] ."|". $gl[0];// "0|$itm"; 
		} else {
			$ele[] = "|". $itm;
		}
	} // foreach

	$post['NumberOfVerticalAxis'] = 1;
	$post['FieldList'] = array($post['_G+Field']);
	if (isset($post['_G+Field2']) && !empty($post['_G+Field2'])) {
		$post['NumberOfVerticalAxis'] = 2;
		array_push($post['FieldList'], $post['_G+Field2']);
	}
	
	// Try to find the X Axis Field to Use (DisasterBeginTime)
	$XAxisField = '';
	foreach($ele as $XAxisItem) {
		$fl = explode('|', $XAxisItem);
		if (substr($fl[1],2) == 'DisasterBeginTime') {
			$XAxisField = $us->q->getGroupFieldName($XAxisItem);
		}
	}
	$ResultData = array();
	foreach($post['FieldList'] as $GraphVariable) {
		$VariableName = substr($GraphVariable,0,strpos($GraphVariable,'|'));
		fb($VariableName);
		$opc['Group'] = $ele;
		$opc['Field'] = $GraphVariable;
		$sql = $us->q->genSQLProcess($qd, $opc);
		$TmpData = $us->q->getassoc($sql);
		fb($TmpData);
		foreach($TmpData as $DataItem) {
			$Index = $DataItem[$XAxisField];
			foreach($DataItem as $Key => $Value) {
				$ResultData[$Index][$Key] = $Value;
			} //foreach
		}
	}
	
	// Complete Data Series, fill with zeros...
	foreach($post['FieldList'] as $GraphVariable) {
		$VariableName = substr($GraphVariable,0,strpos($GraphVariable,'|'));
		$VariableName = substr($VariableName, 2);
		foreach($ResultData as $XAxis => $DataItem) {
			if (! array_key_exists($VariableName, $DataItem)) {
				$ResultData[$XAxis][$VariableName] = 0;
			}
		}
	}
	
	fb($ResultData);
	//$post['NumberOfVerticalAxis'] = 1;
	//$dislist = $ResultData['D.DisasterId'];
	$dislist = $ResultData;

	if (!empty($dislist)) {
		// Process results data
		$dl = $us->q->prepareList($dislist, "GRAPH");
		$gl = array();
		// Translate Labels to Selected Language
		foreach ($dl as $k=>$i) {
			$kk = substr($k, 0, -1); // Select Hay marked like EffectsXX_
			$k2 = substr($k, 2);
			if (isset($dic['Graph'. $k][0]))
				$dk = $dic['Graph'. $k][0];
			elseif (isset($dic['Graph'. $k2][0]))
				$dk = $dic['Graph'. $k2][0];
			elseif (isset($dic[$k][0]))
				$dk = $dic[$k][0];
			elseif (isset($dic[$kk][0]))
				$dk = $dic[$kk][0];
			else
				$dk = $k;
			$gl[$dk] = $i;
		}
		// Construct Graphic Object and Show Page
		$g = new Graphic($post, $gl);
		$sImageURL  = WWWDATA . "/graphs/di8graphic_". session_id() . ".png";
		$sImageFile = WWWDIR . "/graphs/di8graphic_". session_id() . ".png";
		// Wrote graphic to file
		$g->Stroke($sImageFile);
		if ($GraphCommand == "export") {
			// Export Graph as a Image
			header("Content-type: Image/png");
			header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $RegionLabel) ."_Graphic.png");
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check:0', false);
			header('Pragma: no-cache');
			readfile($sImageFile);
			exit();
		} else {
			// Display Graph in Browser
			$t->assign ("qdet", $us->q->getQueryDetails($dic, $post));
			$t->assign ("image", "$sImageURL?". rand(1,3000));
			$t->assign ("ctl_showres", true);
		} //if
	} // if
} //if

$t->display("graphic.tpl");

</script>
