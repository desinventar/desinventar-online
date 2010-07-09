#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-01-21 Jhon H. Caicedo <jhcaiced@desinventar.org>

	Update DGR records updating EffectNotes field...

*/

require_once('../web/include/loader.php');

$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
$us->login('diadmin','di8');
$us->open($RegionId);

//createEEFields();

$line = 1;
$a = fgetcsv(STDIN, 1000, ',');
$us->q->dreg->query('BEGIN TRANSACTION');
while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 1000, ',');
	if (count($a) > 1) {
		$DisasterSerial = $a[0];
		$p = $us->getDisasterIdFromSerial($DisasterSerial);
		$DisasterId = $p['DisasterId'];
		$EffectNotes = str_replace('"','', trim($a[46]));
		
		if ($DisasterId != '') {
			$sQuery = 'UPDATE Disaster SET EffectNotes="' . $EffectNotes . '" WHERE DisasterId="' . $DisasterId . '"';
			fb(sprintf('%4d %-15s %s', $line, $DisasterSerial, $DisasterId));
			$us->q->dreg->query($sQuery);
		}
	} //if
	$line++;
} //while
$us->q->dreg->query('END TRANSACTION');
$us->close();
$us->logout();
exit();

</script>
