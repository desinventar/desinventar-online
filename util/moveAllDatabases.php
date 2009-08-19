#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO

 Utility to export databases from MySQL to SQLite

 2009-07-30 Jhon H. Caicedo <jhcaiced@desinventar.org>
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

$dbh = new PDO('mysql:host=localhost;dbname=di8db', '','',
   array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$RegionList = array();
foreach($dbh->query("SELECT * FROM Region ORDER BY CountryIsoCode,RegionUUID") as $row) {
	$RegionList[] = $row['RegionUUID'];
}
$RegionList = array('PERUINDECI');
foreach ($RegionList as $RegionUUID) {
	$InfoGeneral_eng = '';
	foreach($dbh->query("SELECT * FROM Region WHERE RegionUUID='" . $RegionUUID . "'") as $row) {
		$RegionNames['BOLIVIA']    = 'BOL-1248983224-bolivia_inventario_historico_de_desastres';
		$RegionNames['PANAMA' ]    = 'PAN-1250695231-panama_inventario_de_desastres_sinaproc';
		$RegionNames['PERUINDECI'] = 'PER-1250695309-peru_inventarios_de_desastres_indeci';
		if (array_key_exists($RegionUUID, $RegionNames)) {
			$RegionId = $RegionNames[$RegionUUID];
		} else {
			$RegionId = DIRegion::buildRegionId($row['CountryIsoCode'],$row['RegionLabel']);
		}
		$r = new DIRegion($us);
		$r->set('CountryIso'     , $row['CountryIsoCode']);
		$r->set('RegionLabel'    , $row['RegionLabel']);
		$r->set('RegionId'       , $RegionId);
		$r->set('InfoGeneral'    , $row['RegionDesc']);
		$r->setActive($row['RegionActive']);
		$r->setPublic($row['RegionPublic']);
		$LangIsoCode = $row['RegionLangCode'];
		if ($LangIsoCode == 'es') { $LangIsoCode = 'spa'; }
		if ($LangIsoCode == 'en') { $LangIsoCode = 'eng'; }
		if ($LangIsoCode == 'fr') { $LangIsoCode = 'fre'; }
		if ($LangIsoCode == 'pr') { $LangIsoCode = 'por'; }
		
		$r->set('LangIsoCode'      , $LangIsoCode);
		$r->set('CountryIso'       , $row['CountryIsoCode']);
		// 2009-08-19 Fix Period when moving databases
		$BeginDate = $row['PeriodBeginDate'];
		$EndDate   = $row['PeriodEndDate'];
		if (substr($BeginDate,0,4) == '0001') { $BeginDate = ''; }
		if (substr($EndDate,0,4) == '9999') { $EndDate = ''; }
		
		$r->set('PeriodBeginDate'  , $BeginDate);
		$r->set('PeriodEndDate'    , $EndDate);
		$r->set('OptionAdminURL'   , $row['OptionAdminURL']);
		$r->set('OptionOutOfPeriod', $row['OptionOutOfPeriod']);
		$r->set('OptionOldName'    , $row['RegionUUID']);
		$r->set('GeoLimitMinX'     , $row['GeoLimitMinX']);
		$r->set('GeoLimitMaxX'     , $row['GeoLimitMaxX']);
		$r->set('GeoLimitMinY'     , $row['GeoLimitMinY']);
		$r->set('GeoLimitMaxY'     , $row['GeoLimitMaxY']);
		$r->set('InfoCredits'      , $row['RegionCredits']);
		$InfoGeneral_eng = $row['RegionDescEN'];
	} //foreach
	printf("%-20s %-40s\n", $RegionUUID, $RegionId);
	$iReturn = $r->createRegionDB();
	if ($iReturn > 0) {
		$r->q->core->query("DELETE FROM Region WHERE RegionId='" . $RegionId . "'");
		$r->insert();
		if ($LangIsoCode != 'eng') {
			// Create a Record for Info in eng language
			$r->set('LangIsoCode', 'eng');
			$r->set('InfoGeneral', $InfoGeneral_eng);
			$r->saveInfoTrans('eng');
		}
	}
	$us->open($RegionId);
	$data_dir = '/var/lib/desinventar';
	$cmd = "./mysql2sqlite.pl -r " . $RegionUUID . " | sqlite3 " . $data_dir . "/" . $r->get('RegionId') . "/desinventar.db";
	system($cmd, $iCmdReturn);
	$iReturn = ! $iCmdReturn;
	if ($iReturn > 0) {	
		// Copy Cartography
		$Query = 'SELECT * FROM ' . $RegionUUID . '_GeoLevel';
		foreach($dbh->query($Query) as $geolevel) {
			$CartoFile = $geolevel['GeoLevelLayerFile'];
			foreach(array('dbf','shp','shx','prj') as $ext) {
				$file0 = $CartoFile . '.' . $ext;
				$file1 = $data_dir . '/carto/' . $RegionUUID . '/' . $file0;
				$file2 = $data_dir . '/' . $r->get('RegionId') . '/' . $file0;
				if (file_exists($file1)) {
					copy($file1, $file2);
				}
			} //foreach
		} //foreach
	}
	if ($iReturn > 0) {
		$r->q->core->query("DELETE FROM RegionAuth WHERE RegionId='" . $RegionId . "'");
		foreach($dbh->query("SELECT * FROM RegionAuth WHERE RegionUUID='" . $RegionUUID . "'") as $row) {
			$u = new DIUser($us, $row['UserName']);
			$a = new DIRegionAuth($us, $RegionId, $u->get('UserId'), $row['AuthKey'], $row['AuthValue'], $row['AuthAuxValue']);
			$a->insert();
		} //foreach
	}
	
	if ($iReturn > 0) {
		$d = new DIDisaster($us);
		foreach (split(',', $d->sFieldQDef) as $Field) {
			$oItem = split('/', $Field);
			$sFieldQName = $oItem[0];
			$sFieldType = $oItem[1];
			$sFieldName = substr($sFieldQName, 0, -1);
			$Query = "UPDATE Disaster SET $sFieldQName=$sFieldName WHERE $sFieldName>0";
			$us->q->dreg->query($Query);
			$Query = "UPDATE Disaster SET $sFieldQName=0 WHERE $sFieldName<=0";
			$us->q->dreg->query($Query);
		} //foreach
	} //if
} //foreach
$dbh = null;

</script>
