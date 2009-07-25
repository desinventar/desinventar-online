#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO

 Utility to export databases from MySQL to SQLite

 2009-07-22 Jhon H. Caicedo <jhcaiced@desinventar.org>
*/
$_SERVER["DI8_WEB"] = '/home/jhcaiced/devel/di8/web';
require_once('../web/include/loader.php');
require_once('../web/include/diregion.class.php');
$pdo = new PDO(
   'mysql:host=localhost;dbname=di8db',
   '','',
   array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
   );
$RegionList = array();
foreach($pdo->query("SELECT * FROM Region") as $row) {
	$RegionList[] = $row['RegionUUID'];
}
foreach ($RegionList as $RegionUUID) {
	foreach($pdo->query("SELECT * FROM Region WHERE RegionUUID='" . $RegionUUID . "'") as $row) {
		$r = new DIRegion($us);
		$r->set('CountryIso', $row['CountryIsoCode']);
		$r->set('RegionLabel', $row['RegionLabel']);
		$RegionId = $r->buildRegionId();
		printf("%20s %30s\n", $row['RegionUUID'], $RegionId);
	}
}
$pdo = null;
</script>
