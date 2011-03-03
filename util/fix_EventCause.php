#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2011 Corporacion OSSO
  
  2011-03-03 Jhon H. Caicedo <jhcaiced@desinventar.org>

*/

$_SERVER["DI8_WEB"] = '../web';
require_once('../web/include/loader.php');
require_once('../web/include/didisaster.class.php');

$q = new Query();
$RegionList = array();
foreach($q->core->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
$RegionList = array('COL-1250694506-colombia_inventario_historico_de_desastres', 'GUY-20100727000000');
foreach ($RegionList as $RegionId) {
	$us->open($RegionId);
	print $RegionId . "\n";
	$q->setDBConnection($RegionId);
	
	$sQuery = 'SELECT * FROM Event WHERE EventPredefined>0 AND RecordUpdate > RecordCreation AND RecordUpdate>"2010-01-01"';
	$sth = $q->dreg->prepare($sQuery);
	$sth->execute();
	while ($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		print "\t" . $row['EventId'] . ' ' . $row['EventPredefined'] . "\n";
		$sQuery = 'UPDATE Event SET EventPredefined=2 WHERE EventId="' . $row['EventId'] . '"';
		$q->dreg->query($sQuery);
	}

	$sQuery = 'SELECT * FROM Cause WHERE CausePredefined>0 AND RecordUpdate > RecordCreation AND RecordUpdate>"2010-01-01"';
	$sth = $q->dreg->prepare($sQuery);
	$sth->execute();
	while ($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		print "\t" . $row['CauseId'] . ' ' . $row['CausePredefined'] . "\n";
		$sQuery = 'UPDATE Cause SET CausePredefined=2 WHERE CauseId="' . $row['CauseId'] . '"';
		$q->dreg->query($sQuery);
	}
}

$q = null;
</script>
