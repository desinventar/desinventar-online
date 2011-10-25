#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . '/include/dievent.class.php');
	require_once(BASE . '/include/dicause.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/didisaster.class.php');
	require_once(BASE . '/include/diimport.class.php');
	require_once(BASE . '/include/diregion.class.php');
	
	$RegionId = 'COL-CUNDINAMARCA';
	$us->login('diadmin','di8');
	$us->open($RegionId);
	
	// Delete Records outside Cundinamarca
	$us->q->dreg->query('DELETE FROM Disaster WHERE GeographyId NOT LIKE "00011%"');
	$us->q->dreg->query('DELETE FROM Disaster WHERE GeographyId="00011"');
	// Delete non matching EEData Records
	$sQuery = 'DELETE FROM EEData WHERE DisasterId IN (SELECT EEData.DisasterId FROM EEData LEFT JOIN Disaster ON EEData.DisasterId=Disaster.DisasterId WHERE Disaster.DisasterId IS NULL)';
	$us->q->dreg->query($sQuery);

	// Update GeoLevel Info
	$us->q->dreg->query('DELETE FROM GeoLevel');
	$us->q->dreg->query('INSERT INTO GeoLevel VALUES (0,"spa","","Provincia","",1,"","","")');
	$us->q->dreg->query('INSERT INTO GeoLevel VALUES (1,"spa","","Municipio","",1,"","","")');
	// Update GeoCarto Info
	$us->q->dreg->query('UPDATE GeoCarto SET RegionId=""');
	$us->q->dreg->query('UPDATE GeoCarto SET GeoLevelLayerFile="Prov_Cundinamarca",GeoLevelLayerCode="PROVINCIA",GeoLevelLayerName="PROVINCI0" WHERE GeoLevelId=0');
	$us->q->dreg->query('UPDATE GeoCarto SET GeoLevelLayerFile="Mun_Cundinamarca",GeoLevelLayerCode="MUNICIPIO",GeoLevelLayerName="MUNICIPI0" WHERE GeoLevelId=1');

	// Create New Geography
	$sQuery = 'DELETE FROM Geography';
	$us->q->dreg->query($sQuery);
	$i = new DIImport($us);
	$a = $i->importFromCSV('/tmp/g2.csv', DI_GEOGRAPHY, true, 0);

	// Update Geography Codes
	$g = array();
	$fh = fopen('/tmp/g2.csv', 'r');
	$a = fgetcsv($fh, 0, ',');
	while (! feof($fh) )
	{
		$a = fgetcsv($fh, 0, ',');
		$NewId = $a[4];
		$OldId = $a[5];
		$g[$OldId] = $NewId;
		//$us->q->dreg->query('UPDATE Disaster SET GeographyId="' . $NewId . '" WHERE GeographyId="' . $OldId . '";');
	}
	fclose($fh);

	foreach($us->q->dreg->query('SELECT * FROM Disaster') as $row) 
	{
		$NewId = $g[$row['GeographyId']];
		$Query = 'UPDATE Disaster SET GeographyId="' . $NewId . '" WHERE DisasterId="' . $row['DisasterId'] . '"';
		$us->q->dreg->query($Query);
	}
	$us->close();
	$us->logout();
</script>
