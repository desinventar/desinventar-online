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
$rlist['GAR-ISDR-2011_ARG'] = 'Argentina';
$rlist['GAR-ISDR-2011_BOL'] = 'Bolivia';
$rlist['GAR-ISDR-2011_CHL'] = 'Chile';
$rlist['GAR-ISDR-2011_COL'] = 'Colombia';
$rlist['GAR-ISDR-2011_CRI'] = 'Costa Rica';
$rlist['GAR-ISDR-2011_ECU'] = 'Ecuador';
$rlist['GAR-ISDR-2011_GTM'] = 'Guatemala';
$rlist['GAR-ISDR-2011_MEX'] = 'México';
$rlist['GAR-ISDR-2011_PAN'] = 'Panamá';
$rlist['GAR-ISDR-2011_PER'] = 'Perú';
$rlist['GAR-ISDR-2011_SLV'] = 'El Salvador';
$rlist['GAR-ISDR-2011_VEN'] = 'Venezuela';

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

$o->createCause('acf374ef-2c4d-4491-ab4b-6b7006185825','ONDA FRIA (MEX)');
$o->createCause('01b61cfa-947c-4e33-b9a3-9af9eb1a62df','Norte (MEX)');
$o->createCause('03488106-9a03-4a09-aa2c-33615f05c34b','Accidente (MEX)');
$o->createCause('1e336f2c-3ecd-4da3-8269-8fa617694e49','Tormenta Eléctrica (MEX)');
$o->createCause('227af961-162d-4584-9998-67be3aad2876','Marejada (MEX)');
$o->createCause('24801dbe-ccd8-4375-9b47-cacd2636a4ea','Quema (MEX)');
$o->createCause('26ebdda1-e4b3-42e1-ab94-d4e54263221f','Granizada (MEX)');
$o->createCause('27007076-4401-47cc-85d0-6dff1d1c3309','Falla Geotécnica (MEX)');
$o->createCause('29aa9449-f527-439b-bd34-62ddcd2a7540','Huracán (MEX)');
$o->createCause('76b07f89-e1b2-423d-a535-f760bb8fdd5a','Tormenta Tropical (MEX)');
$o->createCause('98b382a2-ae08-458d-a312-049205441708','Avenida Torrencial (MEX)');
$o->createCause('a73d0b34-8056-4bf4-8981-38ba80399ff6','Ola de Calor (MEX)');
$o->createCause('b54909e2-1136-457b-8da7-0ae1c3d57c47','Incendio (MEX)');
$o->createCause('ec8c5fab-3228-4049-921d-3cf9bbf37b60','Provocado (MEX)');
$o->createEvent('3cc22906-21be-4ba9-baed-591c06ad2bea','Hundimiento (MEX)');
$o->createEvent('af6863db-b11d-49b8-b8e4-f42f0811bbf8','Onda fría (MEX)');

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
