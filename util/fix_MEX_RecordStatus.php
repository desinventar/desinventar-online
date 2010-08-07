#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-08-07 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Fix RecordStatus in GAR-ISDR-2011_MEX database.

*/

require_once('../web/include/loader.php');

$RegionId = 'GAR-ISDR-2011_MEX';
$us->open($RegionId);
print $RegionId . "\n";

$us->q->dreg->query("ATTACH DATABASE '/var/lib/desinventar-8.2/database/GAR-ISDR-2011_MEX/desinventar_GAR.db' AS GAR");
$sQuery = 'SELECT * FROM Disaster';
$sth = $us->q->dreg->prepare($sQuery);
$sth->execute();

$sQuery = 'SELECT RecordStatus FROM GAR.Disaster WHERE DisasterId=:DisasterId';
$sth2 = $us->q->dreg->prepare($sQuery);

$sQuery = 'UPDATE Disaster SET RecordStatus=:RecordStatus WHERE DisasterId=:DisasterId';
$sth3 = $us->q->dreg->prepare($sQuery);

$iCount = 0;
while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	$sth2->execute(array('DisasterId' => $row['DisasterId']));
	while ($rec = $sth2->fetch(PDO::FETCH_ASSOC)) {
		$iCount++;
		$sth3->execute(array('DisasterId' => $row['DisasterId'],
		                     'RecordStatus' => $rec['RecordStatus']));
	}
}
print 'Fichas Procesadas : ' . $iCount . "\n";
$us->q->dreg->query("DETACH DATABASE GAR");
$us->close($RegionId);
$us->logout();
</script>
