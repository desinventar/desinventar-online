#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2009 Corporacion OSSO
  
  2009-07-29 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Updates info.xml file for all databases
*/
require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/diregionrecord.class.php');
$q = new Query();
$RegionList = array();
foreach($q->core->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('ARG-1250695025-argentina_gran_chaco');
foreach ($RegionList as $RegionId) {
	$us->open($RegionId);
	$XMLFile = $us->getDBDir() . '/info.xml';
	if (! file_exists($XMLFile)) {
		print $RegionId . "\n";
		$r = new DIRegionRecord($us, $RegionId);
		//print $r->get('RegionLabel') . "\n";
		//$r->loadFromXML($FileName);
		//$r->saveToXML($FileName);
	}
}
$q = null;
</script>
