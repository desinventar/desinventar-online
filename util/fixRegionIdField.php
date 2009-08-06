#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2009 Corporacion OSSO
  
  2009-08-05 Jhon H. Caicedo <jhcaiced@desinventar.org>
 
  Fix database tables, adding a RegionId field,
  which will be used to synchronize and rebuild
  database information.
*/

$_SERVER["DI8_WEB"] = '../web';
require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
$q = new Query();
$RegionList = array();
foreach($q->core->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('BOL-1248983224-bolivia_inventario_historico_de_desastres');
foreach ($RegionList as $RegionId) {
	print $RegionId . "\n";
	$t = DIRegion::getRegionTables();
	//$i = array_search('GeoCarto', $t);
	//unset($t[$i]);
	array_push($t, 'Sync');
	foreach($t as $TableName) {
		$q->setDBConnection($RegionId);
		if ($TableName != 'GeoCarto') {
			$Query = "ALTER TABLE $TableName ADD COLUMN RegionId VARCHAR(50);";
			//fb($Query);
			$q->dreg->query($Query);
		}
		$Query = "UPDATE $TableName SET RegionId='" . $RegionId . "'";
		//fb($Query);
		$q->dreg->query($Query);
	}
}

$q = null;
</script>
