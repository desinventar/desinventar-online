#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-11-09 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Process GAR2011 databases fix RegionStatus on All of Them (they are not public)

*/

require_once('../web/include/loader.php');

$RegionList = array();
foreach($us->q->core->query("SELECT * FROM Region WHERE RegionId LIKE 'GAR-%'") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('GAR-ISDR-2011_MEX');
foreach ($RegionList as $RegionId) {
	print $RegionId . "\n";
	$us->open($RegionId);
	$r = new DIRegion($us, $RegionId);
	$r->set('RegionPublic', 0);
	$r->set('RegionActive', 1);
	$r->update();
	$us->close();
} //foreach
exit();

</script>
