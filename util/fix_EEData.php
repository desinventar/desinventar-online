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
	
	$RegionId = 'COL-HUILA';
	$us->login('diadmin','di8');
	$us->open($RegionId,'desinventar.db');
	//$r = new DIRegion($us, $RegionId);
	//$r->copyEvents('spa');
	//$r->copyCauses('spa');
	//$i = new DIImport($us);
	//$a = $i->importFromCSV('/tmp/mx_event.csv', DI_EVENT, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_cause.csv', DI_CAUSE, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_geography.csv', DI_GEOGRAPHY, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_disaster.csv', DI_DISASTER, true, 0);
	$us->q->dreg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try {
		$sQuery = 'SELECT COUNT(DisasterId) FROM Disaster';
		$res = $us->q->dreg->query($sQuery);
		$TotalCountD = $res->fetchColumn();

		$sQuery = 'SELECT COUNT(DisasterId) FROM EEData';
		$res = $us->q->dreg->query($sQuery);
		$TotalCountE = $res->fetchColumn();

		print 'Disaster Records : ' . $TotalCountD . "\n";
		print 'EEData Records   : ' . $TotalCountE . "\n";

		$sQuery = 'SELECT DisasterId FROM EEData';
		$sth = $us->q->dreg->prepare($sQuery);
		$sth->execute();
		
		$sQuery = 'SELECT DisasterId FROM Disaster WHERE DisasterId=:DisasterId';
		$sth2 = $us->q->dreg->prepare($sQuery);

		$sQuery = 'DELETE FROM EEData WHERE DisasterId=:DisasterId';
		$sth3 = $us->q->dreg->prepare($sQuery);
		
		$iCount = 0;
		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$iCount++;
			
			$RecordCount = 0;
			$sth2->execute(array('DisasterId' => $row['DisasterId']));
			while($line = $sth2->fetch(PDO::FETCH_ASSOC)) {
				$RecordCount++;
			}
			if ($RecordCount == 0) {
				$sth3->execute(array('DisasterId' => $row['DisasterId']));
				printf('%5d %s', $iCount, $row['DisasterId']);
				print ' ' . $RecordCount . ' ';
				print 'DELETE : ' . $row['DisasterId'];
				print "\n";
			}
		}

		$sQuery = 'SELECT DisasterId FROM Disaster';
		$sth = $us->q->dreg->prepare($sQuery);
		$sth->execute();
		
		$sQuery = 'SELECT DisasterId FROM EEData WHERE DisasterId=:DisasterId';
		$sth2 = $us->q->dreg->prepare($sQuery);

		$sQuery = 'INSERT INTO EEData (DisasterId) VALUES (:DisasterId)';
		$sth3 = $us->q->dreg->prepare($sQuery);
		
		$iCount = 0;
		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$iCount++;			
			$RecordCount = 0;
			$sth2->execute(array('DisasterId' => $row['DisasterId']));
			while($line = $sth2->fetch(PDO::FETCH_ASSOC)) {
				$RecordCount++;
			}
			if ($RecordCount == 0) {
				$sth3->execute(array('DisasterId' => $row['DisasterId']));
				printf('%5d %s', $iCount, $row['DisasterId']);
				print ' ' . $RecordCount . ' ';
				print 'INSERT : ' . $row['DisasterId'];
				print "\n";
			}
		}
	} catch (Exception $e) {
		showErrorMsg("Error !: " . $e->getMessage());
	}
	$us->close();
	$us->logout();
</script>
