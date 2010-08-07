#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	// Find Duplicated Serials in Database

	require_once('../web/include/loader.php');
	require_once(BASE . '/include/dievent.class.php');
	require_once(BASE . '/include/dicause.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/didisaster.class.php');
	require_once(BASE . '/include/dieedata.class.php');
	require_once(BASE . '/include/diimport.class.php');
	require_once(BASE . '/include/diregion.class.php');
	
	//$RegionId = 'GAR-ISDR-2011_MEX';
	$RegionId = 'MEX-1250695136-mexico_inventario_historico_de_desastres';
	$us->login('diadmin','di8');
	$us->open($RegionId);
	$us->q->dreg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try {
		$sQuery = 'SELECT COUNT(DisasterId) FROM Disaster';
		$res = $us->q->dreg->query($sQuery);
		$TotalCountD = $res->fetchColumn();

		$sQuery = 'SELECT DisasterSerial, COUNT(DisasterId) AS C FROM Disaster GROUP BY DisasterSerial';
		$sth = $us->q->dreg->prepare($sQuery);
		$sth->execute();
		
		$iCount = 0;
		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$iCount++;
			//print $row['DisasterSerial'] . ' ' . $row['C'] . "\n";
			if ($row['C'] > 1) {
				printf('%-20s%4d', $row['DisasterSerial'], $row['C']);
				print "\n";
			}
		}
		print 'Total Registros        ' . $TotalCountD . "\n";
		print 'Seriales Unicos        ' . $iCount . "\n";
		$Dupl = $TotalCountD - $iCount;
		print 'Seriales Duplicados    ' . $Dupl  . "\n";
	} catch (Exception $e) {
		showErrorMsg("Error !: " . $e->getMessage());
	}
	$us->close();
	$us->logout();
</script>
