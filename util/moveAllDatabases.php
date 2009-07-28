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
$RegionList = array('COLOMBIA');
foreach ($RegionList as $RegionUUID) {
	foreach($pdo->query("SELECT * FROM Region WHERE RegionUUID='" . $RegionUUID . "'") as $row) {
		$r = new DIRegion($us);
		$r->set('CountryIso'     , $row['CountryIsoCode']);
		$r->set('RegionLabel'    , $row['RegionLabel']);
		$RegionId = $r->buildRegionId();
		$r->set('RegionId'       , $RegionId);
		$r->set('InfoGeneral'    , $row['RegionDesc']);
		$r->set('InfoGeneral_eng', $row['RegionDescEN']);
		$r->setActive($row['RegionActive']);
		$r->setPublic($row['RegionPublic']);
		$LangIsoCode = $row['RegionLangCode'];
		if ($LangIsoCode == 'es') { $LangIsoCode = 'spa'; }
		if ($LangIsoCode == 'en') { $LangIsoCode = 'eng'; }
		$r->set('LangIsoCode'      , $LangIsoCode);
		$r->set('CountryIso'       , $row['CountryIsoCode']);
		$r->set('PeriodBeginDate'  , $row['PeriodBeginDate']);
		$r->set('PeriodEndDate'    , $row['PeriodEndDate']);
		$r->set('OptionAdminURL'   , $row['OptionAdminURL']);
		$r->set('OptionOutOfPeriod', $row['OptionOutOfPeriod']);
		$r->set('GeoLimitMinX'     , $row['GeoLimitMinX']);
		$r->set('GeoLimitMaxX'     , $row['GeoLimitMaxX']);
		$r->set('GeoLimitMinY'     , $row['GeoLimitMinY']);
		$r->set('GeoLimitMaxY'     , $row['GeoLimitMaxY']);
		$r->set('InfoCredits'      , $row['RegionCredits']);
		print $r->get('RegionId') . "\n";
		//$r->createRegionDB();
		//$r->insert();
		$data_dir = '/var/lib/desinventar';
		$cmd = "./mysql2sqlite.pl -r " . $row['RegionUUID'] . " | sqlite3 " . $data_dir . "/" . $r->get('RegionId') . "/desinventar.db";
		print "$cmd\n";
	}
}
$pdo = null;
</script>
