<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/
require_once('include/loader.php');

$post = $_POST;
$get  = $_GET;

if (isset($post['_REG']) && !empty($post['_REG']))
	$reg = $post['_REG'];
elseif (isset($get['r']) && !empty($get['r']))
	$reg = $get['r'];
else
	exit();

$q = $us->q;
$regname = $q->getDBInfoValue('RegionLabel');
fixPost($post);

// load basic field of dictionary
$dic = array();
$dic = array_merge($dic, $q->queryLabelsFromGroup('Disaster', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Record|2', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Geography', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Event', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Cause', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $q->queryLabelsFromGroup('Sector', $lg));
$dic = array_merge($dic, $q->getEEFieldList("True"));
//$t->assign ("dic", $dic);
$t->assign ("reg", $reg);
$t->assign ("regname", $regname);

// Data Options Interface
if (isset($get['page']) || isset($post['_D+cmd'])) {
	// Process Desconsultar Query Design Form
	$iNumberOfRecords = 0;
	$pag = 1;
	$export = '';
	if (isset($get['page'])) {
		// Show results by page number
		$pag = $get['page'];
		$iRecordsPerPage = $get['RecordsPerPage'];
		$fld = $get['fld'];
		$sql = base64_decode($get['sql']);
	}
	elseif (isset($post['_D+cmd'])) {
		// Process results with default options
		$qd  = $q->genSQLWhereDesconsultar($post);
		$sqc = $q->genSQLSelectCount($qd);
		$c	 = $q->getresult($sqc);
		$iNumberOfRecords = $c['counter'];
		// Reuse calculate SQL values in all pages; calculate limits in pages
		$levg = array();
		$fld = $post['_D+Field'];
		$ord = "D.DisasterBeginTime,V.EventName,G.GeographyFQName";
		if (isset($post['_D+SQL_ORDER']))
			$ord = $post['_D+SQL_ORDER'];
		$sql = $q->genSQLSelectData($qd, $fld, $ord);
		$dlt = $q->dreg->query($sqc);
		if ($post['_D+cmd'] == "result") {
			// show results in window
			$export = '';
			$iRecordsPerPage = $post['_D+SQL_LIMIT'];
			// Set values to paging list
			$iNumberOfPages = (int) (($iNumberOfRecords / $iRecordsPerPage) + 1);
			// Smarty assign SQL values
			$t->assign ("sql", base64_encode($sql));
			$t->assign ("fld", $fld);
			$t->assign ("tot", $iNumberOfRecords);
			$t->assign ("RecordsPerPage", $iRecordsPerPage);
			$t->assign ("NumberOfPages" ,$iNumberOfPages);
			// Show results interface 
			$t->assign ("role", $us->getUserRole($reg));
			$t->assign ("ctl_showres", true);
		} else if ($post['_D+cmd'] == "export") {
			if ($post['_D+saveopt'] == "csv")
				$export = 'csv';
			else
				$export = 'xls';
			// show export results
			//header("Content-type: application/x-zip-compressed");
			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $regname) ."_Data.". $export);
			//header("Content-Transfer-Encoding: binary");
			// Limit 1000 results in export: few memory in PHP
			$iRecordsPerPage = 1000;
			$iNumberOfPages = (int) (($iNumberOfRecords / $iRecordsPerPage) + 1);
		}
	}
	// Complete SQL to Paging, later check and run SQL
	if ($q->chkSQL($sql)) {
		if (!empty($export)) {
			// Save results in CSVfile
			$datpth = TEMP ."/di8data_". session_id() .".$export";
			$fp = fopen($datpth, 'w');
			$pin = 0;
			$pgt = $iNumberOfPages;
		} else {
			$pin = $pag-1;
			$pgt = $pag;
		}
		for ($i = $pin; $i < $pgt; $i++) {
			$slim = $sql ." LIMIT " . $i * $iRecordsPerPage .", ". $iRecordsPerPage;
			$dislist = $q->getassoc($slim);
			$dl = $q->printResults($dislist, $export, "NAME");
			if ($i == $pin && !empty($dl)) {
				// Traduce Headers to Current Language
				$lb = "";
				$sel = array_keys($dislist[0]);
				foreach ($sel as $kk=>$ii) {
					$i3 = substr($ii, 0, -4);
					if (isset($dic[$ii][0]))
						$dk[$ii] = $dic[$ii][0];
					elseif (isset($dic[$i3][0]))
						$dk[$ii] = $dic[$i3][0];
					else
						$dk[$ii] = $ii; // No translation, use default value
					//Assign Headers..
					$lb .= '"'. $dk[$ii] .'"'. "\t";
				} //foreach
				if (!empty($export))
					fwrite($fp, $lb ."\n");
				else {
					$t->assign ("dk", $dk);
					$t->assign ("sel", $sel);
				}
			} //if
			if (!empty($export)) {
				fwrite($fp, $dl);
			}
		} //for
		$t->assign ("sqt", $slim);
		if (!empty($export)) {
			fclose($fp);
			//$sto = system("zip -q $datpth.zip $datpth.xls");
			flush();
			readfile($datpth);
			exit;
		} else {
			$t->assign ("offset", ($pag - 1) * $iRecordsPerPage);
			$t->assign ("dislist", $dl);
			$t->assign ("ctl_dislist", true);
		} //else
	} //if
} //if
$time_end = microtime_float();
$t->assign ("time", $time_end - $time_start);
$t->display ("data.tpl");

</script>
