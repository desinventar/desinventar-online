#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-11-22 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  VACUUM Databases to optimize performance

*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/dieefield.class.php');

$RegionList = array();
foreach($us->q->core->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('DESINV-GAR-ISDR-2011_LATAM');
foreach ($RegionList as $RegionId) {
	print $RegionId . "\n";
	$us->open($RegionId);
	$us->q->dreg->query('VACUUM');
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
