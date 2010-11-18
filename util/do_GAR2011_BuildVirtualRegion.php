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
//$rlist['GAR-ISDR-2011_ARG'] = 'Argentina';
//$rlist['GAR-ISDR-2011_BOL'] = 'Bolivia';
//$rlist['GAR-ISDR-2011_CHL'] = 'Chile';
//$rlist['GAR-ISDR-2011_COL'] = 'Colombia';
//$rlist['GAR-ISDR-2011_CRI'] = 'Costa Rica';
//$rlist['GAR-ISDR-2011_ECU'] = 'Ecuador';
//$rlist['GAR-ISDR-2011_GTM'] = 'Guatemala';
$rlist['GAR-ISDR-2011_MEX'] = 'México';
//$rlist['GAR-ISDR-2011_PAN'] = 'Panamá';
//$rlist['GAR-ISDR-2011_PER'] = 'Perú';
//$rlist['GAR-ISDR-2011_SLV'] = 'El Salvador';
//$rlist['GAR-ISDR-2011_VEN'] = 'Venezuela';

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

$o->createEvent('OLA DE FRIO', 'OLA DE FRIO (PER)');

$o->createEvent('ab7ce63f-30a3-4f1b-9e2c-cc8d895c960f','Hundimiento (SLV)');
$o->createEvent('23d9410f-1a2e-42cd-804c-520d499f5bb7','Lahar (SVL)');

// Personalized Causes
$o->createCause('Llu y desb', 'Llu y desb (ARG)');
$o->createCause('Llu y vie', 'Llu y vie (ARG)');
$o->createCause('Corto circuito', 'Corto circuito (ARG)');

$o->createCause('7a4c6a34-c425-4e67-bdbf-4e969e7dd0d6','Granizada (BOL)');
$o->createCause('Equivocación', 'Equivocación (CHL)');
$o->createCause('Pobreza', 'Pobreza (CHL)');

$o->createCause('5a640417-3a03-4957-9cf0-8868e0c20138','Depresión Tropical COL');

$o->createCause('2091c421-0ac2-44bb-bbeb-a23debcb2116','Erupción ECU');
$o->createCause('3a23b6ac-7ea4-4030-a5cd-ef673b50cd79','Inundación ECU');
$o->createCause('3f985d85-04ec-48ab-855d-7264e5f69a3e','Lahares ECU');
$o->createCause('4961dda8-7503-4573-aee2-e52589d8fc09','Sin Nombre ECU');

$o->createCause('corto circuito','Corto Circuito (GTM)');
$o->createCause('fuga de gas propano','Fuga de gas propano (GTM)');
$o->createCause('8e9262ba-effd-4130-ad8d-efa73614df15','Tormenta Tropical Stan (GTM)');
$o->createCause('1b42c371-6624-4cb5-a295-7958900dd40f','Huracán Mitch (GTM)');

$o->createCause('Alcantarillados','Alcantarillados (PAN)');
$o->createCause('06ff9db7-ea3b-412d-8d74-07e80749f621','Depresión Tropical (SLV)');
$o->createCause('18948256-9d3c-4b81-be6a-304e62f1f74d','Sobre Explotación (SLV)');

$o->createCause('Cond. Atmosfer','Condición Atmosférica (VEN)');

$o->clearGeoCartoTable();
$o->clearGeographyTable();

foreach($rlist as $RegionItemId => $RegionItemGeographyName) {
	printf("%-60s %-20s\n", $RegionItemId, $RegionItemGeographyName);
	$o->addRegionItem($RegionItemId, $RegionItemGeographyName);
}
$o->rebuildRegionData();

$us->close();
$us->logout();

</script>
