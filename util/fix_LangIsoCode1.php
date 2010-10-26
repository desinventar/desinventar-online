#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2010 Corporacion OSSO
  
  2010-20-24 Jhon H. Caicedo <jhcaiced@desinventar.org>
 
  Fix LangIsoCode to be the same in info.xml and all tables
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/digeography.class.php');
$q = new Query();
$RegionList = array();
foreach($q->core->query("SELECT * FROM Region ORDER BY RegionId") as $row) {
	$RegionList[] = $row['RegionId'];
}
//$RegionList = array('IND-1250695040-india_orissa_historic_inventory_of_disasters');
foreach ($RegionList as $RegionId) {
	//print $RegionId . "\n";
	$us->open($RegionId);
	$r = new DIRegion($us, $RegionId);
	$LangIsoCode = $r->get('LangIsoCode');	
	foreach(array('Event','Cause','GeoLevel','Geography','GeoCarto') as $Table) {
		$sQuery = 'UPDATE ' . $Table . ' SET LangIsoCode="' . $LangIsoCode . '"';
		try {
			$us->q->dreg->query($sQuery);
		} catch (Exception $e) {
			print $RegionId . ' : ' . $e->getMessage() . "\n";
		}
	} //foreach
}
$q = null;
</script>
