<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
require_once('include/loader.php');
require_once('include/diregion.class.php');

$post = $_POST;

$reg = getParameter('_REG', getParameter('r',''));

if ($reg == '')
{
	exit();
}

$us->open($reg);
$r = new DIRegion($us, $reg);
$RegionLabel = $r->getRegionInfoValue('RegionLabel');
fixPost($post);

// load levels to display in totalizations
foreach ($us->q->loadGeoLevels('', -1, false) as $k=>$i)
{
	$st['StatisticGeographyId_'. $k] = array($i[0], $i[1]);
}
$dic = array_merge(array(), $st);
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Statistic', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));
$dic = array_merge($dic, $us->q->getEEFieldList('True'));
$t->assign('reg', $reg);
$t->assign('RegionLabel', $RegionLabel);

// Data Options Interface
if (isset($post['page']) || isset($post['_S+cmd']))
{
	// Process Desconsultar Query Design Form
	$tot = 0;
	$pag = 1;
	$export = '';
	// Show results by page number
	if (isset($post['page']))
	{
		$pag = $post['page'];
		$rxp = $post['rxp'];
		$fld = $post['fld'];
		$sql = base64_decode($post['sql']);
		$geo = $post['geo'];
		if (isset($post['ord']))
		{
			$sql .= ' ORDER BY '. $post['ord'] .' '. $post['dir'];
		}
	}
	elseif (isset($post['_S+cmd']))
	{
		// Process results with default options
		$qd 	= $us->q->genSQLWhereDesconsultar($post);
		$sqc 	= $us->q->genSQLSelectCount($qd);
		$c 		= $us->q->getresult($sqc);
		$tot 	= $c['counter'];
		$geo	= $post['_S+showgeo'];
		// Reuse calculate SQL values in all pages; calculate limits in pages
		$levg = array();
		if (isset($post['_S+Firstlev']) && !empty($post['_S+Firstlev']))
		{
			$levg[] = $post['_S+Firstlev'];
		}
		if (isset($post['_S+Secondlev']) && !empty($post['_S+Secondlev']))
		{
			$levg[] = $post['_S+Secondlev'];
		}
		if (isset($post['_S+Thirdlev']) && !empty($post['_S+Thirdlev']))
		{
			$levg[] = $post['_S+Thirdlev'];
		}
		$opc['Group'] = $levg;
		$field = explode(',', $post['_S+Field']);
		$opc['Field'] = $field;
		$sql = $us->q->genSQLProcess($qd, $opc);
		$cou = $us->q->getnumrows($sql);
		$sdl = $us->q->totalize($sql);
		$dlt = $us->q->getresult($sdl);
		// 2009-08-10 (jhcaiced) In Consolidates by Event/Cause, fix
		// the value
		$dlt['EventName'] = '';
		$dlt['CauseName'] = '';
		$fld = 'DisasterId_';
		//echo $sql;
		// organize groups
		$gp = array();
		foreach ($opc['Group'] as $i)
		{
			$v = explode('|', $i);
			$val = substr($v[1],2);
			if ($val == 'GeographyId' || $val == 'DisasterBeginTime')
			{
				$val = $val .'_'. $v[0];
			}
			elseif ($val == 'EventId')
			{
				$val = 'V.EventName';
			}
			elseif ($val == 'CauseId')
			{
				$val = 'C.CauseName';
			}
			$gp[] = $val;
			$fld .= ',' . $val;
		} //foreach

		foreach ($field as $i)
		{
			$v = explode('|', $i);
			if ($v[0] != 'DisasterId')
			{
				$fld .= ','. $v[0];
			}
		} //foreach
		// Show results..
		if ($post['_S+cmd'] == 'result')
		{
			$export = '';
			$rxp 	= $post['_S+SQL_LIMIT'];
			// Set values to paging list
			$last = (int) (($cou / $rxp) + 1);
			// Smarty assign SQL values
			$t->assign('gp', $gp);
			$t->assign('dlt', $dlt); // List of totals..
			$t->assign('sql', base64_encode($sql));
			$t->assign('sqt', $sql);
			$t->assign('qdet', $us->q->getQueryDetails($dic, $post));
			$t->assign('fld', $fld);
			$t->assign('cou', $cou);
			$t->assign('tot', $tot);
			$t->assign('rxp', $rxp);
			$t->assign('last',$last);
			$t->assign('geo', $geo);
			// Show results interface 
			$t->assign('ctl_showres', true);
		}
		elseif ($post['_S+cmd'] == 'export')
		{
			// Export Results to File
			if ($post['_S+saveopt'] == 'csv')
			{
				$export = 'csv';
			}
			else
			{
				$export = 'xls';
			}
			//header('Content-type: application/x-zip-compressed');
			header('Content-type: text/x-csv');
			header('Content-Disposition: attachment; filename=DI8_'. str_replace(' ', '', $RegionLabel) .'_Consolidate.'. $export);
			//header('Content-Transfer-Encoding: binary');
			// Limit 5000 results in export: few memory in PHP
			$rxp 		= 5000;
			$last = (int) (($cou / $rxp) + 1);
		}
	}
	// Complete SQL to Paging, later check and run SQL
	if ($us->q->chkSQL($sql))
	{
		if (!empty($export))
		{
			// Save results in CSVfile
			$stdpth = TEMP .'/di8statistic_'. session_id() . '.' . $export;
			$fp = fopen($stdpth, 'w');
			$pin = 0;
			$pgt = $last;
		}
		else
		{
			$pin = $pag-1;
			$pgt = $pag;
		}
		for ($i = $pin; $i < $pgt; $i++)
		{
			$slim = $sql .' LIMIT ' . $i * $rxp .', '. $rxp;
			$dislist = $us->q->getassoc($slim);
			
			// 2011-01-06 (jhcaiced) Adding Totals to data when exporting...
			if ($export != '') 
			{
				foreach($gp as $GroupField)
				{
					$dlt[$GroupField] = '';
				}
				$dislist[] = $dlt;
			}
			$dl = $us->q->printResults($dislist, $export, $geo);
			
			/*
			//2011-01-06 (jhcaiced) Create a log used for debug.
			ob_start();
			//print_r($gp);
			//print_r($post);
			//print_r($dl);
			//print_r($dislist);
			//print_r($dlt);
			$Log = ob_get_clean();
			file_put_contents('/tmp/log.txt', $Log);
			*/
			
			if ($i == $pin && !empty($dl))
			{
				// Set translation in headers
				$lb = '';
				$sel = array_keys($dislist[0]);
				foreach ($sel as $kk=>$ii)
				{
					$i2 = substr($ii, 2);
					$i3 = substr($ii, 0, -1);
					if (isset($dic['Statistic'. $ii][0]))
					{
						$dk[$ii] = $dic['Statistic'. $ii][0];
					}
					elseif (isset($dic['Statistic'. $i2][0]))
					{
						$dk[$ii] = $dic['Statistic'. $i2][0];
					}
					elseif (isset($dic[$i3][0]))
					{
						$dk[$ii] = $dic[$i3][0];
					}
					elseif (isset($dic[$ii][0]))
					{
						$dk[$ii] = $dic[$ii][0];
					}
					else
					{
						$dk[$ii] = $ii;		// no traduction..
					}
					$ColumnSeparator = "\t";
					if ($export == 'csv')
					{
						$ColumnSeparator = ',';
					}
					$lb .= '"'. $dk[$ii] .'"' . $ColumnSeparator;
				}
				if (!empty($export))
				{
					fwrite($fp, $lb ."\n");
				}
			}
			if (!empty($export))
			{
				fwrite($fp, $dl);
			}
		}
		if (!empty($export))
		{
			fclose($fp);
			//$sto = system('zip -q $stdpth.zip $stdpth.csv');
			flush();
			readfile($stdpth);
			exit;
		}
		else
		{
			$t->assign('offset', ($pag - 1) * $rxp);
			$t->assign('sel', $sel);
			$t->assign('dk', $dk);
			$t->assign('dislist', $dl);
			$t->assign('ctl_dislist', true);
		}
	}
}
$t->display('statistic.tpl');

</script>
