#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2009 Corporacion OSSO
  
  2009-07-29 Jhon H. Caicedo <jhcaiced@desinventar.org>
 
  Fix Info Table, for each database in the system
  sets Info('RegionId') to the name of the Directory
  Must usually be run as root in order to have write
  access to the databases.

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
$RegionList = array('BOL-1248830153-bolivia_inventario_historico_de_desastres');
foreach ($RegionList as $RegionId) {
	$us->open($RegionId);
	fb($RegionId);
	$q->setDBConnection($RegionId);
	$d = new DIDisaster($us);
	foreach (split(',', $d->sFieldQDef) as $Field) {
		$oItem = split('/', $Field);
		$sFieldQName = $oItem[0];
		$sFieldType = $oItem[1];
		$sFieldName = substr($sFieldQName, 0, -1);
		$Query = "UPDATE Disaster SET $sFieldQName=$sFieldName WHERE $sFieldName>0";
		$q->dreg->query($Query);
		$Query = "UPDATE Disaster SET $sFieldQName=0 WHERE $sFieldName<=0";
		$q->dreg->query($Query);
	}
}

$q = null;
</script>
