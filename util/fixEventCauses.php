#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2009 Corporacion OSSO
  
  2009-08-06 Jhon H. Caicedo <jhcaiced@desinventar.org>
 
  Fix Cause table, re-import predefined Causes
  in each database.
*/

$_SERVER["DI8_WEB"] = '../web';
require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dicause.class.php');
$q = new Query();
$RegionList = array();
foreach($q->core->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('BOL-1248983224-bolivia_inventario_historico_de_desastres');
foreach ($RegionList as $RegionId) {
	print $RegionId . "\n";
	$q->setDBConnection($RegionId);
	$r = new DIRegion($us, $RegionId);
	$r->copyEvents($r->get('LangIsoCode'));
	$r->copyCauses($r->get('LangIsoCode'));
}
$q = null;
</script>
