#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
	Use this script to rebuild the Virtual Region Databases
	CAN, GranChaco
	2009-08-21 Jhon H. Caicedo <jhcaiced@desinventar.org>
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/diregionitem.class.php');
require_once(BASE . '/include/digeolevel.class.php');
require_once(BASE . '/include/digeocarto.class.php');
require_once(BASE . '/include/disync.class.php');
	
// GAR 2011 Virtual Region LATAM
$RegionItems = array('GAR-ISDR-2011_COL' => 'Colombia',
					 'GAR-ISDR-2011_ECU' => 'Ecuador'
					);
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

$o = new DIRegion($us, $RegionId);
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

//Open database
$us->open($RegionId);

/*
$iReturn = $o->createRegionDB('País');
// Add RegionItem
$o->clearSyncTable();
foreach($RegionItems as $RegionItemId => $RegionItemGeographyName) {
	printf("%-60s %-20s\n", $RegionItemId, $RegionItemGeographyName);
	$o->addRegionItem($RegionItemId, $RegionItemGeographyName);
}
$o->rebuildRegionData();
*/

$us->close();
$us->logout();

</script>
