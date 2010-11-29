#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-11-19 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Process GAR2011 databases, create an EEField called RiskType and update the value
  using the RecordStatus of datacards

*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/dieefield.class.php');

$RegionList = array();
foreach($us->q->core->query("SELECT * FROM Region WHERE RegionId LIKE 'GAR-ISDR-2011_%'") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
$RegionList = array('DESINV-GAR-ISDR-2011_LATAM');
foreach ($RegionList as $RegionId) {
	print $RegionId . "\n";
	$us->open($RegionId);
	$sQuery = 'SELECT COUNT(*) AS C FROM Disaster';
	foreach($us->q->dreg->query($sQuery) as $row) {
		$iTotal = $row['C'];
	}
	print 'Total Records : ' . $iTotal . "\n";
	$EEFieldId = createEEField($us, 'Risk Type', 'INTEGER');
	if ($EEFieldId == '') {
		print "Error creating field...\n";
	} else {
		print 'EEField created : ' . $EEFieldId . "\n";
		$sQuery = 'SELECT * FROM Disaster';
		$sth = $us->q->dreg->prepare($sQuery);
		//$sth->bindParam(':SessionId', $prmSessionId, PDO::PARAM_STR);
		$sth->execute();
		$iCount = 0;
		while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$DisasterId = $row['DisasterId'];
			$RecordStatus = $row['RecordStatus'];
			$RiskType = 0;
			if ($RecordStatus == 'PUBLISHED') { $RiskType = 1; }
			if ($RecordStatus == 'READY') { $RiskType = 2; }
			$sQuery = 'UPDATE EEData SET ' . $EEFieldId . '=' . $RiskType . ' WHERE DisasterId="' . $DisasterId . '"';
			$us->q->dreg->query($sQuery);
			$iCount++;
			if ($iCount % 100 == 0) {
				printf('%6d %6d' . "\n", $iCount, $iTotal);
			}
		} //while
	} 
	$us->close();
} //foreach
exit();

function createEEField($prmSession, $EEFieldLabel, $EEFieldType, $EEFieldSize='') {
	$f = new DIEEField($prmSession);
	$f->set('EEGroupId', 'GAR2011');
	$f->set('EEFieldLabel', $EEFieldLabel);
	$f->set('EEFieldType', $EEFieldType);
	if ($EEFieldSize != '') {
		$f->set('EEFieldSize', $EEFieldSize);
	}
	$sAnswer = '';
	$i = $f->insert();
	if ($i > 0) {
		$sAnswer = $f->get('EEFieldId');
	}
	return $sAnswer;
}

</script>
