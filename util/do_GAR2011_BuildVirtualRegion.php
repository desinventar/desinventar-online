#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
	Use this script to rebuild the Virtual Region Databases
	CAN, GranChaco
	2009-08-21 Jhon H. Caicedo <jhcaiced@desinventar.org>
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/diregiondb.class.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/diregionitem.class.php');
require_once(BASE . '/include/digeolevel.class.php');
require_once(BASE . '/include/digeocarto.class.php');
require_once(BASE . '/include/disync.class.php');
	
// GAR 2011 Virtual Region LATAM
$rlist = array();
//$rlist['GAR-ISDR-2011_COL'] = 'Colombia';
$rlist['GAR-ISDR-2011_ECU'] = 'Ecuador';

$RegionId        = 'DESINV-GAR-ISDR-2011_LATAM';
$RegionLabel     = 'GAR2011 Latin America Virtual Region';
$PeriodBeginDate = '1970-00-00';
$PeriodEndDate   = '2009-12-31';
$GeoLimitMinX    = -86;
$GeoLimitMaxX    = -53;
$GeoLimitMinY    = -25;
$GeoLimitMaxY    =  13;
$InfoGeneral     = ''; //file_get_contents('desc1.txt');

$us->login('diadmin','di8');
$us->open($RegionId);

$o = new DIRegionDB($us, $RegionId);
/*
$o->set('RegionLabel'    , $RegionLabel);
$o->set('RegionId'       , $RegionId);
$o->set('RegionStatus'   , CONST_REGIONACTIVE | CONST_REGIONPUBLIC);
$o->set('PeriodBeginDate', $PeriodBeginDate);
$o->set('PeriodEndDate'  , $PeriodEndDate);
$o->set('GeoLimitMinX'   , $GeoLimitMinX);
$o->set('GeoLimitMaxX'   , $GeoLimitMaxX);
$o->set('GeoLimitMinY'   , $GeoLimitMinY);
$o->set('GeoLimitMaxY'   , $GeoLimitMaxY);
$o->set('InfoGeneral'    , $InfoGeneral);
$o->set('IsCRegion'   , 1);
fb($o->get('IsCRegion'));
$o->update();
*/

//$iReturn = $o->createRegionDB('País');
// Add RegionItem
$o->clearSyncTable();
// Create GeoLevels
/*
$o->clearGeoLevelTable();
$o->createGeoLevel(0, 'País');
$o->createGeoLevel(1, 'Nivel 1');
$o->createGeoLevel(2, 'Nivel 2');
*/

// Personalized Events
$o->createEvent('10d35cac-d2e9-4aed-8481-fc2ae52760b8','Desboardamiento ECU');
$o->createEvent('31d7820b-180c-4374-b9dd-31f414f6bb61','Lahares');
$o->createEvent('59c36df4-cf51-4ca5-a3aa-0e225a3b93c3','Asentamientos ECU');
$o->createEvent('a77993f3-2e77-4c1b-bc71-bd9d4513c06c','Hundimiento ECU');
// Personalized Causes
$o->createCause('2091c421-0ac2-44bb-bbeb-a23debcb2116','Erupción ECU');
$o->createCause('3a23b6ac-7ea4-4030-a5cd-ef673b50cd79','Inundación ECU');
$o->createCause('3f985d85-04ec-48ab-855d-7264e5f69a3e','Lahares ECU');
$o->createCause('4961dda8-7503-4573-aee2-e52589d8fc09','Sin Nombre ECU');
$o->createCause('5a640417-3a03-4957-9cf0-8868e0c20138','Depresión Tropical COL');
$o->clearGeographyTable();

foreach($rlist as $RegionItemId => $RegionItemGeographyName) {
	printf("%-60s %-20s\n", $RegionItemId, $RegionItemGeographyName);
	$o->addRegionItem($RegionItemId, $RegionItemGeographyName);
}
$o->rebuildRegionData();

$us->close();
$us->logout();

</script>
