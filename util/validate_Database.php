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
	$iCount = 0;
	$sQuery = 'SELECT DisasterId,DisasterSerial,EventId,CauseId,GeographyId FROM Disaster'; // WHERE DisasterSerial IN ("DGR-2010-02048")';
	$result = $us->q->dreg->query($sQuery);
	$reclist = $result->fetchAll(PDO::FETCH_ASSOC);
	$result->closeCursor();
	foreach($reclist as $record)
	{
		$msg = array();
		$us->q->dreg->beginTransaction();
		// Validate EEData Record
		$sQuery = 'SELECT COUNT(DisasterId) AS C FROM EEData WHERE DisasterId="' . $record['DisasterId'] . '"';
		foreach($us->q->dreg->query($sQuery) as $row) {
			$count = $row['C'];
		}
		$us->q->dreg->commit();
		if ($count == 0) {
			$msg[] = 'No tiene registro en EEData';
		}
		
		// Validate Event
		$sQuery = 'SELECT COUNT(EventId) AS C FROM Event WHERE EventId="' . $record['EventId'] . '"';
		$us->q->dreg->beginTransaction();
		foreach($us->q->dreg->query($sQuery) as $row) {
			$count = $row['C'];
		}
		if ($count == 0) {
			$msg[] = 'EventId Error : ' . $record['EventId'];
		}
		$us->q->dreg->commit();
		
		// Validate Cause		
		$sQuery = 'SELECT COUNT(CauseId) AS C FROM Cause WHERE CauseId="' . $record['CauseId'] . '"';
		$us->q->dreg->beginTransaction();
		foreach($us->q->dreg->query($sQuery) as $row) {
			$count = $row['C'];
		}
		$us->q->dreg->commit();
		if ($count == 0) {
			$msg[] = 'CauseId Error : ' . $record['CauseId'];
		}

		// Validate Geography
		$sQuery = 'SELECT COUNT(GeographyId) AS C FROM Geography WHERE GeographyId="' . $record['GeographyId'] . '"';
		$us->q->dreg->beginTransaction();
		foreach($us->q->dreg->query($sQuery) as $row) {
			$count = $row['C'];
		}
		$us->q->dreg->commit();
		if ($count == 0) {
			$msg[] = 'GeographyId Error : ' . $record['GeographyId'];
		}
		
		// Validate DisasterBeginTime
		if (count($msg) > 0) {
			foreach($msg as $line) {
				printf('%-36s %-10s %s' . "\n", $record['DisasterId'], $record['DisasterSerial'], $line);
			}
		}
		if ( ($iCount > 0) && ($iCount % 100 == 0) ) {
			printf('%-5d' . "\n", $iCount);
		}
		$iCount++;
	} //while
	$us->close();
	$us->logout();
</script>
