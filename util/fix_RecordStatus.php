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
	$us->q->dreg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try
	{
		$sQuery = 'SELECT DisasterId,DisasterSerial,RecordStatus FROM Disaster';
		$sth = $us->q->dreg->prepare($sQuery);
		$sth->execute();
		
		$iCount = 0;
		while ($row = $sth->fetch(PDO::FETCH_ASSOC))
		{
			$d = new DIDisaster($us, $row['DisasterId']);
			$v = $d->validateEffects(-61);
			if ($v < 0)
			{
				$iCount++;
				print $row['DisasterId'] . ' ' . $row['DisasterSerial'] . ' ' . $v . "\n";
				$Query = 'UPDATE Disaster SET RecordStatus="DRAFT" WHERE DisasterId="' . $row['DisasterId'] . '"';
				$us->q->dreg->query($Query);
			}			
		}
	}
	catch (Exception $e)
	{
		showErrorMsg("Error !: " . $e->getMessage());
	}
	$us->close();
	$us->logout();
</script>
