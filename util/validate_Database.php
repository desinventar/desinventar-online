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
	
	$RegionId = 'ECU-1250695659-ecuador_sist_de_inf_de_desastres_y_emergencias';
	//$RegionId = 'ECU-1250695011-ecuador_inventario_historico_de_desastres';
	$us->login('diadmin','di8');
	$us->open($RegionId);
	//$r = new DIRegion($us, $RegionId);
	//$r->copyEvents('spa');
	//$r->copyCauses('spa');
	//$i = new DIImport($us);
	//$a = $i->importFromCSV('/tmp/mx_event.csv', DI_EVENT, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_cause.csv', DI_CAUSE, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_geography.csv', DI_GEOGRAPHY, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_disaster.csv', DI_DISASTER, true, 0);
	$sQuery = 'SELECT * FROM Disaster';
	$result = $us->q->dreg->query($sQuery);
	while ($record = $result->fetch(PDO::FETCH_ASSOC)) {
		$msg = array();
		// Validate EEData Record
		$sQuery = 'SELECT COUNT(DisasterId) AS C FROM EEData WHERE DisasterId="' . $record['DisasterId'] . '"';
		foreach($us->q->dreg->query($sQuery) as $row) {
			$count = $row['C'];
		}
		if ($count == 0) {
			$msg[] = 'No tiene registro en EEData';
		}
		
		// Validate Event
		$sQuery = 'SELECT COUNT(EventId) AS C FROM Event WHERE EventId="' . $record['EventId'] . '"';
		foreach($us->q->dreg->query($sQuery) as $row) {
			$count = $row['C'];
		}
		if ($count == 0) {
			$msg[] = 'EventId Error : ' . $record['EventId'];
		}
		
		// Validate Cause		
		$sQuery = 'SELECT COUNT(CauseId) AS C FROM Cause WHERE CauseId="' . $record['CauseId'] . '"';
		foreach($us->q->dreg->query($sQuery) as $row) {
			$count = $row['C'];
		}
		if ($count == 0) {
			$msg[] = 'CauseId Error : ' . $record['CauseId'];
		}

		// Validate Geography
		$sQuery = 'SELECT COUNT(GeographyId) AS C FROM Geography WHERE GeographyId="' . $record['GeographyId'] . '"';
		foreach($us->q->dreg->query($sQuery) as $row) {
			$count = $row['C'];
		}
		if ($count == 0) {
			$msg[] = 'GeographyId Error : ' . $record['GeographyId'];
		}
		
		// Validate DisasterBeginTime
		
		
		if (count($msg) > 0) {
			foreach($msg as $line) {
				printf('%-36s %-10s %s' . "\n", $record['DisasterId'], $record['DisasterSerial'], $line);
			}
		}
	} //while
	$us->close();
	$us->logout();
</script>
