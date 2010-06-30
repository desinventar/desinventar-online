#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2010 Corporacion OSSO
  
  2009-06-30 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Creates a loop to lock the Disaster table for testing load....
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/didisaster.class.php');

$RegionId = 'BOL-1248983224-bolivia_inventario_historico_de_desastres';
$RegionId = 'COL-1250123456-colombia_base_de_prueba';
$us->login('diadmin','di8');
$r = $us->open($RegionId);
if ($r < 0) {
	print 'No se encontro base de datos ' . "\n";
	exit();
}

print $RegionId . "\n";
$i = 0;
foreach($us->q->dreg->query('SELECT DisasterId from Disaster LIMIT 20') as $row) {
	$DisasterId = $row['DisasterId'];
	$d = new DIDisaster($us, $DisasterId);
	$r = $d->update();
	printf('%4d %2d %10s %s' . "\n", $i, $r, $d->get('DisasterSerial'), $DisasterId);
	$i++;
	sleep(1);
}

$us->close($RegionId);
$us->logout();

</script>
