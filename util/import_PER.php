#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . '/include/dievent.class.php');
	require_once(BASE . '/include/dicause.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/didisaster.class.php');
	require_once(BASE . '/include/dieedata.class.php');
	require_once(BASE . '/include/diimport.class.php');
	require_once(BASE . '/include/diregion.class.php');
	
	$RegionId = 'PER-1250695241-peru_inventario_historico_de_desastres';
	$us->login('diadmin','di8');
	$us->open($RegionId);

	$sWhere = " (D.RecordStatus = 'PUBLISHED') AND ( ( (D.DisasterBeginTime BETWEEN '1970-00-00' AND '2001-06-22') AND (D.GeographyId LIKE '00004%' OR D.GeographyId LIKE '0000500006%' OR D.GeographyId LIKE '0000500007%' OR D.GeographyId LIKE '0000500008%' OR D.GeographyId LIKE '00018%' OR D.GeographyId LIKE '00023%')) )";

	/*
	$us->q->dreg->query("DROP TABLE IF EXISTS TmpTable1;");
	$us->q->dreg->query("DROP TABLE IF EXISTS TmpTable2;");
	$sQuery = "CREATE TABLE TmpTable1 AS SELECT D.* FROM Disaster D WHERE " . $sWhere;
	$us->q->dreg->query($sQuery);
	$sQuery = "CREATE TABLE TmpTable2 AS SELECT E.* FROM Disaster D,EEData E WHERE (D.DisasterId=E.DisasterId) AND " . $sWhere;
	$us->q->dreg->query($sQuery);
	*/
	
	// Delete records to be replaced
	/*
	$sQuery = "SELECT D.DisasterId FROM Disaster D WHERE " . $sWhere;
	$iCount = 0;
	foreach($us->q->dreg->query($sQuery) as $row) {
		$DisasterId = $row['DisasterId'];
		$us->q->dreg->query("DELETE FROM Disaster WHERE DisasterId='" . $DisasterId . "';");
		$us->q->dreg->query("DELETE FROM EEData   WHERE DisasterId='" . $DisasterId . "';");
		$iCount++;
	}
	fb($iCount);
	*/
	
	
	$r = new DIRegion($us, $RegionId);
	$r->copyEvents('spa');
	$r->copyCauses('spa');
	
	$i = new DIImport($us);
	$a = $i->importFromCSV('/tmp/PS_disaster.csv', DI_DISASTER, true, 0);
	$us->close();
	$us->logout();
</script>
