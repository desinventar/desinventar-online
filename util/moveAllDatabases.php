#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO

 Utility to export databases from MySQL to SQLite
 Create databases in di-8.2 from list of databases in di-8.1

 2009-08-20 Jhon H. Caicedo <jhcaiced@desinventar.org>
*/
$_SERVER["DI8_WEB"] = '../web';
require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/diregionauth.class.php');
require_once(BASE . '/include/diuser.class.php');
require_once(BASE . '/include/didisaster.class.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/digeolevel.class.php');
require_once(BASE . '/include/digeocarto.class.php');

$dbh = new PDO('mysql:host=localhost;dbname=di8db', '','',
   array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$RegionList = array();
foreach($dbh->query("SELECT * FROM Region ORDER BY CountryIsoCode,RegionUUID") as $row) {
	$RegionList['RegionUUID'] = '';
}
$RegionList = array();
$RegionList['PANAMA'] = 'PAN-1250695231-panama_inventario_de_desastres_sinaproc';
//$RegionList['GUATEMALA'] = '';
foreach ($RegionList as $RegionUUID => $RegionId) {
	$InfoGeneral_eng = '';
	foreach($dbh->query("SELECT * FROM Region WHERE RegionUUID='" . $RegionUUID . "'") as $row) {
		if ($RegionId == '') {
			$RegionId = DIRegion::buildRegionId($row['CountryIsoCode'],$row['RegionLabel']);
		}
		$RegionLangCode = $row['RegionLangCode'];
	} //foreach
	
	// Get LangIsoCode for new database
	switch($RegionLangCode) {
		case 'es':  $LangIsoCode = 'spa'; break;
		case 'en':  $LangIsoCode = 'eng'; break;
		case 'pt':  $LangIsoCode = 'por'; break;
		case 'fr':  $LangIsoCode = 'fre'; break;
		default:    $LangIsoCode = 'eng'; break;
	}
	$r = new DIRegion($us);
	$r->set('RegionId', $RegionId);
	$r->set('OptionOldName', $RegionUUID);
	$r->set('LangIsoCode', $LangIsoCode);	
	printf("%-20s %-40s\n", $RegionUUID, $RegionId);
	$iReturn = $r->createRegionDB();
} //foreach
$dbh = null;

</script>
