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
require_once('../web/include/diregion.class.php');

$RegionList = array();
foreach($us->q->core->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('BOL-1248983224-bolivia_inventario_historico_de_desastres');

// Countries with eng as Language...
$eng_country = array('IND','IRN','LKA','NPL','VUT');
foreach ($RegionList as $RegionId) {
	$us->open($RegionId);
	$r = new DIRegion($us, $RegionId);
	$LangIsoCode = $r->get('LangIsoCode');
	$CountryIso = $r->get('CountryIso');
	if (array_search($CountryIso,$eng_country)) {
		$LangIsoCode = 'eng';
	} else {
		$LangIsoCode = 'spa';
	}
	printf("%-3s %-3s %-70s\n",  $LangIsoCode, $CountryIso, $RegionId);
	$r->set('LangIsoCode', $LangIsoCode);
	$r->update();
}
</script>
