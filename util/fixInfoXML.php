#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2009 Corporacion OSSO
  
  2009-07-29 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Updates info.xml file for all databases
*/

$_SERVER["DI8_WEB"] = '../web';
require_once('../web/include/loader.php');
require_once('../web/include/diregion.class.php');

$q = new Query();
$RegionList = array();
foreach($q->core->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('BOL-1248830153-bolivia_inventario_historico_de_desastres');
foreach ($RegionList as $RegionId) {
	$us->open($RegionId);
	print $RegionId . "\n";
	$r = new DIRegion($us, $RegionId);
	$r-saveToXML($r->getDBDir() . '/info.xml');
}

$q = null;
</script>
