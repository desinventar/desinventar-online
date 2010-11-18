#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
	Use this script to rebuild the Virtual Region Databases
	CAN, GranChaco
	2009-08-21 Jhon H. Caicedo <jhcaiced@desinventar.org>
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/diregionrecord.class.php');
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
$rlist['GAR-ISDR-2011_IDN'] = 'Indonesia';
$rlist['GAR-ISDR-2011_IND_ORISSA']    = 'India - Orissa';
$rlist['GAR-ISDR-2011_IND_TAMILNADU'] = 'India - Tamil Nadu';
$rlist['GAR-ISDR-2011_IRN'] = 'Iran';
$rlist['GAR-ISDR-2011_JOR'] = 'Jordan';
$rlist['GAR-ISDR-2011_LKA'] = 'Sri Lanka';
$rlist['GAR-ISDR-2011_MOZ'] = 'Mozambique';
$rlist['GAR-ISDR-2011_NPL'] = 'Nepal';
$rlist['GAR-ISDR-2011_SYR'] = 'Syria';

$RegionId        = 'DESINV-GAR-ISDR-2011_VR';
$RegionLabel     = 'GAR2011 Asia - Africa';
$PeriodBeginDate = '1970-00-00';
$PeriodEndDate   = '2009-12-31';
$GeoLimitMinX    = -86;
$GeoLimitMaxX    = -53;
$GeoLimitMinY    = -25;
$GeoLimitMaxY    =  13;
$InfoGeneral     = ''; //file_get_contents('desc1.txt');

$us->login('diadmin','di8');
$us->open($RegionId);

/*
$r = new DIRegionRecord($us, $RegionId);
$r->copyEvents('eng');
$r->copyCauses('eng');
*/

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
$o->update();
*/
//$iReturn = $o->createRegionDB('País');
// Add RegionItem
$o->clearSyncTable();

// Create GeoLevels
/*
$o->clearGeoLevelTable();
$o->createGeoLevel(0, 'Country');
$o->createGeoLevel(1, 'Nivel 1');
$o->createGeoLevel(2, 'Nivel 2');
*/

$o->createCause('5a143225-f4ec-4e15-89f4-9a40552b4e9b','Human Behavior (IDN)');
$o->createCause('6e0fa032-1b60-4ccb-8a4e-ab626c70b621','Human Mistake (IDN)');
$o->createcause('d4877438-e026-428a-b3cb-08fdfa37a29a','El Niño (IDN)');

$o->createCause('0dfe5d38-9803-426f-9c7d-9300d91d3837','Tropical Depresion (IND_I2)');
$o->createCause('14bee772-9d85-4eda-afa2-1b4d5759efb0','Overexploiting (IND_I2)');
$o->createCause('4f948d38-2325-4a45-a45d-1f401a4debda','Lightining (IND_I2)');
$o->createCause('cca1f61f-bec3-4f53-bb1b-de4966e75a32','Ground Fire (IND_I2)');
$o->createEvent('2377778e-cd66-4382-b9ba-ea0b9961528c','COLD (IND_I2)');
$o->createEvent('b7a25e31-e312-40e1-96c2-e751a7aadfa2','CLOUD BURST (IND_I2)');

$o->createCause('3e4d7a97-8ba6-4997-9c19-07c37a1e5bc5','Tempest (IRN)');
$o->createCause('1f086849-1d27-4057-b64f-bc102be73c3f','Strong wind (IRN)');

$o->createCause('36dffe7c-41f7-4fd4-bbd1-8d9f79005e50','Increase of Temperature (JOR)');
$o->createCause('44f07918-5aac-4606-a3be-30d4a41a79b8','Low Temperature (JOR)');
$o->createCause('4fa2eaef-0889-474a-9c17-08cd7e657a2c','Cholera (JOR)');
$o->createCause('65d1e773-9ce8-4478-ad4c-e3bcc822e1b4','Heavy snow (JOR)');
$o->createCause('7583e6df-3d08-43a6-86ed-60b31477e92f','Other Cause (JOR)');
$o->createCause('8d50f7ec-4071-43c3-89bd-6dd41e974dc3','M. Meningitis (JOR)');
$o->createCause('b163b6b2-c311-4680-a11e-ddb983ea10bc','Agricultural damage (JOR)');
$o->createCause('b955acb2-880f-4925-a442-b7a32ef199bb','Seismic activity (JOR)');
$o->createCause('cbeef3fa-dafc-408f-bf61-f248723673eb','Birds Flu (JOR)');
$o->createEvent('348f42b3-2099-452c-8ec0-8f2c79f561ae','COLD WAVE (JOR)');
$o->createEvent('83e96f52-e69f-4699-a24c-b2973ed7bf69','FLASH FLOOD (JOR)');
$o->createEvent('b806a048-9595-4a0b-8e7f-52331f454a76','HEAT WAVE (JOR)');
$o->createEvent('f5a3ce35-6ad3-4b5d-978b-a243ed5253f6','RAINS (JOR)');

$o->createCause('2b98b827-ec25-421b-8ad1-ad2eb1372789','Rock Fall (LKA)');
$o->createCause('384f6917-eafd-4fe5-824a-c5ed04b52f89','Rabies (LKA)');
$o->createCause('70597243-3c16-445d-8c4b-e3fd066dcc9f','Human Mistake (LKA)');
$o->createCause('b7a9292f-dd2e-4fca-9876-93485185d77d','Elephant Attack (LKA)');
$o->createCause('f4f32ff4-2128-4888-8983-a109179e44e3','Cutting Failure (LKA)');
$o->createCause('f575e3c2-42ad-4a58-920b-2be62fca1180','Sea Surge (LKA)');
$o->createCause('f8848152-a357-4a7e-8673-9b544baa418b','Retaining WF (LKA)');
$o->createCause('fb5282a4-a9d2-490c-8e5a-56dce41d2ce8','Cyclone (LKA)');
$o->createEvent('01e6476a-d514-42ec-b8d8-10d545beb87f','LAND SUBSIDENCE (LKA)');
$o->createEvent('510b9be3-9457-4fe3-a196-d52280eb8d59','URBAN FLOOD (LKA)');

$o->createCause('1e4284a1-9fa0-4419-881d-bd03048f90ca','Excesso (MOZ)');
$o->createCause('37499114-09b2-4fc3-a1d1-1cbfb4adf0d5','Ciclone (MOZ)');
$o->createCause('6bada6cf-e226-4b28-86f8-3f87f435137f','Depressão Tropical (MOZ)');
$o->createCause('cc5a172e-d03f-4d1c-a223-11ac936649b0','Fogo (MOZ)');

$o->createCause('03aac809-071b-4743-906e-f529a528422c','HUMAN MISTAKE (NPL)');
$o->createCause('5655d976-5d41-4523-b188-ad95cbeb48fa','ELECTRIC SHOCK (NPL)');
$o->createCause('58eff645-1f3a-421f-86f7-98c639728765','DEFORESTATION (NPL)');
$o->createCause('c4ad5cb3-0742-4b66-ba8b-4af96e90f582','TECHNICAL FAILU (NPL)');
$o->createCause('ELECTRICSTORM','ELECTRICSTORM (NPL)');
$o->createCause('STRUCTURE','STRUCTURE (NPL)');
$o->createEvent('f3b7f67e-10bf-4d07-afba-f62bae2bb561','COLD WAVE (NPL)');

$o->createCause('ddcdc556-261d-4595-b892-85e85da9b694','Lightning (SYR)');
$o->createCause('fbd89d2e-23b9-4930-b23e-f627344fbc12','Electric Storm (SYR)');
$o->createEvent('12b70b0f-2ba0-4595-966e-a28b72f34b63','COLD WAVE (SYR)');
$o->createEvent('7229e65d-374e-4e2c-ba75-e4faa21da7e7','THUNDER STORM (SYR)');

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
