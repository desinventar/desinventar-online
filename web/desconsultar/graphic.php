<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');
require_once('../include/graphic.class.php');

if (isset($_POST['_REG']) && !empty($_POST['_REG']))
	$reg = $_POST['_REG'];
else
	exit();

$q = new Query($reg);
$rinfo = $q->getDBInfo();
$regname = $rinfo['RegionLabel'];
$post = $_POST;

// load levels to display in totalizations
foreach ($q->loadGeoLevels("") as $k=>$i)
	$st["GraphDisasterGeographyId_". $k] = array($i[0], $i[1]);
$dic = array_merge(array(), $st);
$dic = array_merge($dic, $q->queryLabelsFromGroup('Graph', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Sector', $lg));
$dic = array_merge($dic, $q->getEEFieldList("True"));
$t->assign ("dic", $dic);
$t->assign ("regname", $regname);

if (isset($post['_G+cmd'])) {
	// Process QueryDesign Fields and count results
	$qd  = $q->genSQLWhereDesconsultar($post);
/*	$sqc = $q->genSQLSelectCount($qd);
	$c   = $q->getresult($sqc);
	$cou = $c['counter'];*/
	// Process Configuration options to Graphic
	$ele = array();
	foreach (explode("|", $post['_G+Type']) as $itm) {
		if ($itm == "D.DisasterBeginTime") {
			if (isset($post['_G+Stat']) && strlen($post['_G+Stat'])>0) {
				$ele[] = $post['_G+Stat'] ."|". $itm;
			} else {
				$ele[] = $post['_G+Period'] ."|". $itm;
			}
		} elseif (substr($itm, 2, 19) == "DisasterGeographyId") {
			$gl = explode("_", $itm);
			$ele[] = $gl[1] ."|". $gl[0];// "0|$itm"; 
		} else {
			$ele[] = "|". $itm;
		}
	} // foreach
	$opc['Group'] = $ele;
	$opc['Field'] = array($post['_G+Field']);
	if (isset($post['_G+Field2']) && !empty($post['_G+Field2']))
		array_push($opc['Field'], $post['_G+Field2']);
	$sql = $q->genSQLProcess($qd, $opc);
	$dislist = $q->getassoc($sql);
//	echo $sql;
//	echo "<pre>"; print_r($gl); echo "</pre>";
	if (!empty($dislist)) {
		// Process results data
		$dl = $q->prepareList($dislist, "GRAPH");
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
		if ($post['_G+cmd'] == "export") {
			header("Content-type: Image/png");
			header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $regname) ."_Graphic.png");
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check:0', false);
			header('Pragma: no-cache');
			readfile($sImageFile);
			exit();
		} else {
			$t->assign ("qdet", $q->getQueryDetails($dic, $post));
			$t->assign ("image", "$sImageURL?". rand(1,3000));
			$t->assign ("ctl_showres", true);
		} //if
	} // if
} //if

$t->display ("graphic.tpl");

</script>
