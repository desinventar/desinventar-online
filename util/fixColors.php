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

$q = new Query();
$RegionList = array();
foreach($q->core->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('BOL-1248830153-bolivia_inventario_historico_de_desastres');
foreach ($RegionList as $RegionId) {
	$us->open($RegionId);
	fb($RegionId);
	$q->setDBConnection($RegionId);
	$q->dreg->query("ATTACH DATABASE '" . CONST_DBBASE . "' AS Base");
	$Query = " UPDATE Event SET EventRGBColor=(SELECT EventRGBColor from Base.Event E WHERE Event.EventId=E.EventId AND Event.LangIsoCode=E.LangIsoCode) " .
	         " WHERE EXISTS (SELECT EventRGBColor from Base.Event E WHERE Event.EventId=E.EventId AND Event.LangIsoCode=E.LangIsoCode)";
	$q->dreg->query($Query);
	$q->dreg->query('DETACH DATABASE Base');
}

$q = null;
</script>
