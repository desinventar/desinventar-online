#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO

 Utility to export databases from MySQL to SQLite
 Create databases in di-8.2 from list of databases in di-8.1

 2009-11-12 Jhon H. Caicedo <jhcaiced@desinventar.org>
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
	$RegionList[$row['RegionUUID']] = $row['RegionId'];
}
$RegionList = array();
$RegionList['PANAMA'] = 'PAN-1250695231-panama_inventario_de_desastres_sinaproc';
//$RegionList['GUATEMALA'] = '';
foreach ($RegionList as $RegionUUID => $RegionId) {
	$bCreateRegion = false;
	/*
	if ($RegionId != '') {
		$DBDir = VAR_DIR . '/database/' . $RegionId;
		if (!file_exists($DBDir)) {
			$bCreateRegion = true;
		}
	} else {
		$bCreateRegion = true;
	}
	*/
	$bCreateRegion = true;
	if ($bCreateRegion) {
		$InfoGeneral_eng = '';
		$r = new DIRegion($us,$RegionId);
		foreach($dbh->query("SELECT * FROM Region WHERE RegionUUID='" . $RegionUUID . "'") as $row) {
			$RegionId = $row['RegionId'];
			if ($RegionId == '') {
				$RegionId = DIRegion::buildRegionId($row['CountryIsoCode'],$row['RegionLabel']);
			}
			$RegionLangCode = $row['RegionLangCode'];

			// Get LangIsoCode for new database
			switch($RegionLangCode) {
				case 'es':  $LangIsoCode = 'spa'; break;
				case 'en':  $LangIsoCode = 'eng'; break;
				case 'pt':  $LangIsoCode = 'por'; break;
				case 'fr':  $LangIsoCode = 'fre'; break;
				default:    $LangIsoCode = 'eng'; break;
			}
			$r->set('RegionId', $RegionId);
			$r->set('RegionLabel', $row['RegionLabel']);
			$r->set('CountryIso', $row['CountryIsoCode']);
			$r->set('OptionOldName', $RegionUUID);
			$r->set('LangIsoCode', $LangIsoCode);	
			$r->set('RegionStatus', 3);
			$r->addLanguageInfo($LangIsoCode);
		} //foreach
		// Update Region Information on desinventar-8.1
		$query = "UPDATE Region SET RegionId='" . $RegionId . "' WHERE RegionUUID='" . $RegionUUID . "'";
		$dbh->query($query);
		
		// Re-create database on desinventar-8.2.0 and update information
		printf("%-20s %-40s\n", $RegionUUID, $RegionId);
		$iReturn = $r->createRegionDB();
		$r->insert();
		$us->setUserRole('root',$RegionId,'ADMINREGION');
	} //if
} //foreach
$dbh = null;

</script>
