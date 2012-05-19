<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/diregion.class.php');
require_once('include/graphic.class.php');
$RegionId = getParameter('_REG','');
if ($RegionId == '')
{
	exit();
}
$us->open($RegionId);
foreach($_POST['prmGraph']['Field'] as $key => $value)
{
	if ($value == '')
	{
		unset($_POST['prmGraph']['Field'][$key]);
		unset($_POST['prmGraph']['Scale'][$key]);
		unset($_POST['prmGraph']['Data'][$key]);
		unset($_POST['prmGraph']['Mode'][$key]);
		unset($_POST['prmGraph']['Tendency'][$key]);
	}
}
$post = $_POST;
$r = new DIRegion($us, $RegionId);
$RegionLabel = $r->getRegionInfoValue('RegionLabel');
$t->assign('RegionLabel', $RegionLabel);
fixPost($post);
// load levels to display in totalizations
foreach ($us->q->loadGeoLevels('', -1, false) as $k=>$i)
{
	$st['GraphGeographyId_'. $k] = array($i[0], $i[1]);
} //foreach

$dic = array_merge(array(), $st);
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Graph', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));
$dic = array_merge($dic, $us->q->getEEFieldList('True'));
//$t->assign ('dic', $dic);
$prmGraph = $post['prmGraph'];
$GraphCommand = $prmGraph['Command'];
if ($GraphCommand != '')
{
	$query_graph = '';
	$prmGraph['SubType'] = (int)$prmGraph['SubType'];
	if ($prmGraph['SubType'] == GRAPH_HISTOGRAM_TEMPORAL)
	{
		$prmGraph['VarList'] = 'D.DisasterBeginTime';
	}
	elseif ($prmGraph['SubType'] == GRAPH_HISTOGRAM_EVENT)
	{
		$prmGraph['VarList'] = 'D.DisasterBeginTime|D.EventId';
	}
	elseif ($prmGraph['SubType'] == GRAPH_HISTOGRAM_CAUSE)
	{
		$prmGraph['VarList'] = 'D.DisasterBeginTime|D.CauseId';
	}
	elseif ($prmGraph['SubType'] == GRAPH_COMPARATIVE_EVENT)
	{
		$prmGraph['VarList'] = 'D.EventId';
	}
	elseif ($prmGraph['SubType'] == GRAPH_COMPARATIVE_CAUSE)
	{
		$prmGraph['VarList'] = 'D.CauseId';
	}
	elseif (($prmGraph['SubType'] >= 100) && ($prmGraph['SubType'] < 200) )
	{
		$k = $prmGraph['SubType'] - 100;
		$prmGraph['VarList'] = 'D.DisasterBeginTime|D.GeographyId_' . $k;
	}
	elseif ($prmGraph['SubType'] >= 200)
	{
		$k = $prmGraph['SubType'] - 200;
		$prmGraph['VarList'] = 'D.GeographyId_' . $k;
		# Bug 127 - Limit data in graph results
		$query_graph = 'G.GeographyLevel>=' . $k;
	}
	else
	{
		$prmGraph['VarList'] = $prmGraph['SubType'];
	}

	// Process QueryDesign Fields and count results
	$qd  = $us->q->genSQLWhereDesconsultar($post);
	# Add specific query parameters
	if ($query_graph != '')
	{
		$qd .= ' AND (' . $query_graph . ')';
	}
	$sqc = $us->q->genSQLSelectCount($qd);	
	$c   = $us->q->getresult($sqc);
	$NumRecords = $c['counter'];
	$t->assign ('NumRecords', $NumRecords);

	$post['prmGraph'] = $prmGraph;

	$sImageURL  = WWWDATA . '/graphs/graph_'. session_id() . '_' . time() . '.png';
	$sImageFile = WWWDIR  . '/graphs/graph_'. session_id() . '_' . time() . '.png';

	// Process Configuration options to Graphic
	$ele = array();
	foreach (explode('|', $prmGraph['VarList']) as $itm)
	{
		if ($itm == 'D.DisasterBeginTime')
		{
			// Histogram
			if (isset($prmGraph['Stat']) && strlen($prmGraph['Stat'])>0)
			{
				$ele[] = $prmGraph['Stat'] .'|'. $itm;
			}
			else
			{
				$ele[] = $prmGraph['Period'] .'|'. $itm;
			}
		}
		elseif (substr($itm, 2, 11) == 'GeographyId')
		{
			$gl = explode('_', $itm);
			$ele[] = $gl[1] .'|'. $gl[0];// '0|$itm'; 
		}
		else
		{
			$ele[] = '|'. $itm;
		}
	} // foreach

	$post['NumberOfVerticalAxis'] = 1;
	$post['FieldList'] = $prmGraph['Field'];
	$post['NumberOfVerticalAxis'] = count($post['FieldList']);
	
	/*	
	// Try to find the X Axis Field to Use (DisasterBeginTime)
	$XAxisField = '';
	foreach($ele as $XAxisItem)
	{
		$fl = explode('|', $XAxisItem);
		if (substr($fl[1],2) == 'DisasterBeginTime')
		{
			$XAxisField = $us->q->getGroupFieldName($XAxisItem);
		}
	}
	$ResultData = array();
	foreach($post['FieldList'] as $GraphVariable)
	{
		$VariableName = substr($GraphVariable,0,strpos($GraphVariable,'|'));
		$opc['Group'] = $ele;
		$opc['Field'] = $GraphVariable;
		$sql = $us->q->genSQLProcess($qd, $opc);
		$TmpData = $us->q->getassoc($sql);
		foreach($TmpData as $DataItem)
		{
			$Index = $DataItem[$XAxisField];
			foreach($DataItem as $Key => $Value)
			{
				$ResultData[$Index][$Key] = $Value;
			} //foreach
		}
	}
	
	// Complete Data Series, fill with zeros...
	foreach($post['FieldList'] as $GraphVariable)
	{
		$VariableName = substr($GraphVariable,0,strpos($GraphVariable,'|'));
		$VariableName = substr($VariableName, 2);
		foreach($ResultData as $XAxis => $DataItem)
		{
			if (! array_key_exists($VariableName, $DataItem))
			{
				$ResultData[$XAxis][$VariableName] = 0;
			}
		}
	}
	
	//$post['NumberOfVerticalAxis'] = 1;
	//$dislist = $ResultData['D.DisasterId'];
	$dislist = $ResultData;
	*/
	$opc['Group'] = $ele;
	$opc['Field'] = $prmGraph['Field'];
	$sql = $us->q->genSQLProcess($qd, $opc);
	$dislist = $us->q->getassoc($sql);
	if (!empty($dislist))
	{
		// Process results data
		$dl = $us->q->prepareList($dislist, 'GRAPH');
		$gl = array();
		// Translate Labels to Selected Language
		foreach ($dl as $k=>$i)
		{
			$kk = substr($k, 0, -1); // Select Hay marked like EffectsXX_
			$k2 = substr($k, 2);
			if (isset($dic['Graph'. $k][0]))
			{
				$dk = $dic['Graph'. $k][0];
			}
			elseif (isset($dic['Graph'. $k2][0]))
			{
				$dk = $dic['Graph'. $k2][0];
			}
			elseif (isset($dic[$k][0]))
			{
				$dk = $dic[$k][0];
			}
			elseif (isset($dic[$kk][0]))
			{
				$dk = $dic[$kk][0];
			}
			else
			{
				$dk = $k;
			}
			$gl[$dk] = $i;
		}
		// Construct Graphic Object and Show Page
		$g = new Graphic($us, $post, $gl);
		// Wrote graphic to file
		$g->Stroke($sImageFile);
	}
	if ($NumRecords > 0)
	{
		if ($GraphCommand == 'export')
		{
			// Export Graph as a Image
			header('Content-type: Image/png');
			header('Content-Disposition: attachment; filename=DesInventar_'. str_replace(' ', '', $RegionLabel) .'_Graphic.png');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check:0', false);
			header('Pragma: no-cache');
			readfile($sImageFile);
			exit();
		}
		else
		{
			// Display Graph in Browser
			$t->assign('qdet', $us->q->getQueryDetails($dic, $post));
			$t->assign('image', $sImageURL . '?'. rand(1,3000));
			$t->assign('ctl_showres', true);
		} //if
	}
} //if

$t->force_compile   = true; # Force this template to always compile
$t->display('graphic.tpl');

</script>
