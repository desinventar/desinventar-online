<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/
require_once('include/loader.php');

$post = $_POST;

$RegionId = getParameter('RegionId', getParameter('_REG', getParameter('r','')));
if ($RegionId == '') {
	exit();
}

$us->open($RegionId);

$RegionLabel = $us->q->getDBInfoValue('RegionLabel');
fixPost($post);

// load basic field of dictionary
$dic = array();
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Disaster', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Record|2', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Geography', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Event', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Cause', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));
$dic = array_merge($dic, $us->q->getEEFieldList("True"));


$UserRole = $us->getUserRole($RegionId);
$UserRoleValue = $us->getUserRoleValue($RegionId);

$t->assign('RegionId'   , $RegionId);
$t->assign('RegionLabel', $RegionLabel);
$t->assign('UserRole', $UserRole);
$t->assign('UserRoleValue', $UserRoleValue);

// Data Options Interface
if (isset($post['page']) || isset($post['_D+cmd'])) {
	// Process Desconsultar Query Design Form
	$iNumberOfRecords = 0;
	$pag = 1;
	$export = '';
	if (isset($post['page'])) {
		// Show results by page number
		$pag = $post['page'];
		$iRecordsPerPage = $post['RecordsPerPage'];
		$fld = $post['fld'];
		$sql = base64_decode($post['sql']);
	} elseif (isset($post['_D+cmd'])) {
		fb($post);
		// Process results with default options
		$qd  = $us->q->genSQLWhereDesconsultar($post);
		$sqc = $us->q->genSQLSelectCount($qd);
		fb($sqc);
		$c	 = $us->q->getresult($sqc);
		$iNumberOfRecords = $c['counter'];
		// Reuse calculate SQL values in all pages; calculate limits in pages
		$levg = array();
		$fld = $post['_D+Field'];
		$ord = "D.DisasterBeginTime,V.EventName,G.GeographyFQName";
		if (isset($post['_D+SQL_ORDER']))
			$ord = $post['_D+SQL_ORDER'];
		$sql = $us->q->genSQLSelectData($qd, $fld, $ord);
		//$dlt = $us->q->dreg->query($sqc);
		if ($post['_D+cmd'] == "result") {
			// show results in window
			$export = '';
			$iRecordsPerPage = $post['_D+SQL_LIMIT'];
			// Set values to paging list
			$iNumberOfPages = (int) (($iNumberOfRecords / $iRecordsPerPage) + 1);
			// Smarty assign SQL values
			$t->assign('sql', base64_encode($sql));
			$t->assign('fld', $fld);
			$t->assign('tot', $iNumberOfRecords);
			$t->assign('RecordsPerPage', $iRecordsPerPage);
			$t->assign('NumberOfPages',$iNumberOfPages);
			// Show results interface 
			$t->assign('role', $us->getUserRole($RegionId));
			$t->assign('qdet', $us->q->getQueryDetails($dic, $post));
			$t->assign('ctl_showres', true);
		} else if ($post['_D+cmd'] == 'export') {
			if ($post['_D+saveopt'] == 'csv')
				$export = 'csv';
			else
				$export = 'xls';
			// show export results
			//header("Content-type: application/x-zip-compressed");
			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $RegionLabel) ."_Data.". $export);
			//header("Content-Transfer-Encoding: binary");
			// Limit 1000 results in export: few memory in PHP
			$iRecordsPerPage = 1000;
			$iNumberOfPages = (int) (($iNumberOfRecords / $iRecordsPerPage) + 1);
		}
	}
	// Complete SQL to Paging, later check and run SQL
	if ($us->q->chkSQL($sql)) {
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
			$dislist = $us->q->getassoc($slim);
			$dl = $us->q->printResults($dislist, $export, "NAME");
			if ($i == $pin && !empty($dl)) {
				// Translate headers to current interface language
				if ($export == 'csv') {
					$ColumnSeparator = ',';
				} else {
					$ColumnSeparator = "\t";
				}
				$lb = "";
				$sel = array_keys($dislist[0]);
				$bFirst = true;
				foreach ($sel as $kk=>$ii) {
					if (! $bFirst) {
						$lb .= $ColumnSeparator;
					}
					$i3 = substr($ii, 0, -4);
					if (isset($dic[$ii][0])) {
						$dk[$ii] = $dic[$ii][0];
					} elseif (isset($dic[$i3][0])) {
						$dk[$ii] = $dic[$i3][0];
					} else {
						$dk[$ii] = $ii; // No translation, use default value
					}					
					//Assign Headers..
					$lb .= '"'. $dk[$ii] .'"';
					$bFirst = false;
				} //foreach
				if (!empty($export)) {
					fwrite($fp, $lb ."\n");
				} else {
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
