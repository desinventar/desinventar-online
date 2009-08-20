<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');

$post = $_POST;
$get  = $_GET;

if (isset($post['_REG']) && !empty($post['_REG']))
	$reg = $post['_REG'];
elseif (isset($get['r']) && !empty($get['r']))
	$reg = $get['r'];
else
	exit();
     
$q = new Query($reg);
$regname = $q->getDBInfoValue('RegionLabel');
fixPost($post);

// load levels to display in totalizations
foreach ($q->loadGeoLevels('', -1, false) as $k=>$i)
	$st["StatisticGeographyId_". $k] = array($i[0], $i[1]);
$dic = array_merge(array(), $st);
$dic = array_merge($dic, $q->queryLabelsFromGroup('Statistic', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Sector', $lg));
$dic = array_merge($dic, $q->getEEFieldList("True"));
$t->assign ("reg", $reg);
$t->assign ("regname", $regname);

// Data Options Interface
if (isset($get['page']) || isset($post['_S+cmd'])) {
	// Process Desconsultar Query Design Form
	$tot = 0;
	$pag = 1;
	$export = false;
	// Show results by page number
	if (isset($get['page'])) {
		$pag = $get['page'];
		$rxp = $get['rxp'];
		$fld = $get['fld'];
		$sql = base64_decode($get['sql']);
		$geo = $get['geo'];
		if (isset($get['ord']))
			$sql .= " ORDER BY ". $get['ord'] ." DESC ";
	}
	// Process results with default options
	else if (isset($post['_S+cmd'])) {
		$qd 	= $q->genSQLWhereDesconsultar($post);
		$sqc 	= $q->genSQLSelectCount($qd);
		$c 		= $q->getresult($sqc);
		$tot 	= $c['counter'];
		$geo	= $post['_S+showgeo'];
		// Reuse calculate SQL values in all pages; calculate limits in pages
		$levg = array();
		if (isset($post['_S+Firstlev']) && !empty($post['_S+Firstlev']))
			$levg[] = $post['_S+Firstlev'];
		if (isset($post['_S+Secondlev']) && !empty($post['_S+Secondlev']))
			$levg[] = $post['_S+Secondlev'];
		if (isset($post['_S+Thirdlev']) && !empty($post['_S+Thirdlev']))
			$levg[] = $post['_S+Thirdlev'];
		$opc['Group'] = $levg;
		$field = explode(",", $post['_S+Field']);
		$opc['Field'] = $field;
		$sql = $q->genSQLProcess($qd, $opc);
		$cou = $q->getnumrows($sql);
		$sdl = $q->totalize($sql);
		$dlt = $q->getresult($sdl);

		// 2009-08-10 (jhcaiced) In Consolidates by Event/Cause, fix
		// the value
		$dlt['EventName'] = '';
		$dlt['CauseName'] = '';

		$fld = "DisasterId_";
		//echo $sqc ."<br>". $sql;
		// organize groups
		$gp = array();
		foreach ($opc['Group'] as $i) {
			$v = explode("|", $i);
			$val = substr($v[1],2);
			if ($val == "GeographyId" || $val == "DisasterBeginTime")
				$val = $val ."_". $v[0];
			elseif ($val == "EventId")
				$val = "V.EventName";
			elseif ($val == "CauseId")
				$val = "C.CauseName";
			$gp[] = $val;
			$fld .= ",$val";
		} //foreach

		foreach ($field as $i) {
			$v = explode("|", $i);
			if ($v[0] != "DisasterId")
				$fld .= ",". $v[0];
		} //foreach
		// Show results..
		if ($post['_S+cmd'] == "result") {
			$export = false;
			$rxp 		= $post['_S+SQL_LIMIT'];
			// Set values to paging list
			$last = (int) (($cou / $rxp) + 1);
			// Smarty assign SQL values
			$t->assign ("gp", $gp);
			$t->assign ("dlt", $dlt); // List of totals..
			$t->assign ("sql", base64_encode($sql));
			$t->assign ("sqt", $sql);
			$t->assign ("qdet", $q->getQueryDetails($dic, $post));
			$t->assign ("fld", $fld);
			$t->assign ("cou", $cou);
			$t->assign ("tot", $tot);
			$t->assign ("rxp", $rxp);
			$t->assign ("last",$last);
			$t->assign ("geo", $geo);
			// Show results interface 
			$t->assign ("ctl_showres", true);
		}
		// show export results
		else if ($post['_S+cmd'] == "export") {
			//header("Content-type: application/x-zip-compressed");
			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $regname) ."_Consolidate.xls");
			//header("Content-Transfer-Encoding: binary");
			// Limit 5000 results in export: few memory in PHP
			$export = true;
			$rxp 		= 5000;
			$last = (int) (($cou / $rxp) + 1);
		}
	}
	// Complete SQL to Paging, later check and run SQL
	if ($q->chkSQL($sql)) {
		if ($export) {
			// Save results in CSVfile
			$stdpth = TEMP ."/di8statistic_". session_id();
			$fp = fopen("$stdpth.xls", 'w');
			$pin = 0;
			$pgt = $last;
		}
		else {
			$pin = $pag-1;
			$pgt = $pag;
		}
		for ($i = $pin; $i < $pgt; $i++) {
			$slim = $sql ." LIMIT " . $i * $rxp .", ". $rxp;
			$dislist = $q->getassoc($slim);
			$dl = $q->printResults($dislist, $export, $geo);
			if ($i == $pin && !empty($dl)) {
				// Set translation in headers
				$lb = "";
				$sel = array_keys($dislist[0]);
				foreach ($sel as $kk=>$ii) {
					$i2 = substr($ii, 2);
					$i3 = substr($ii, 0, -1);
					if (isset($dic['Statistic'. $ii][0]))
						$dk[$ii] = $dic['Statistic'. $ii][0];
					elseif (isset($dic['Statistic'. $i2][0]))
						$dk[$ii] = $dic['Statistic'. $i2][0];
					elseif (isset($dic[$i3][0]))
						$dk[$ii] = $dic[$i3][0];
					elseif (isset($dic[$ii][0]))
						$dk[$ii] = $dic[$ii][0];
					else
						$dk[$ii] = $ii;		// no traduction..
					$lb .= '"'. $dk[$ii] .'"'. "\t";
				}
				if ($export)
					fwrite($fp, $lb ."\n");
			}
			if ($export)
				fwrite($fp, $dl);
		}
		if ($export) {
			fclose($fp);
			//$sto = system("zip -q $stdpth.zip $stdpth.csv");
			flush();
			readfile("$stdpth.xls");
			exit;
		}
		else {
			$t->assign ("offset", ($pag - 1) * $rxp);
			$t->assign ("sel", $sel);
			$t->assign ("dk", $dk);
			$t->assign ("dislist", $dl);
			$t->assign ("ctl_dislist", true);
		}
	}
}
$t->display ("statistic.tpl");

</script>
