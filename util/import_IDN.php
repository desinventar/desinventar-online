#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . '/include/dievent.class.php');
	require_once(BASE . '/include/dicause.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/didisaster.class.php');
	require_once(BASE . '/include/dieedata.class.php');
	require_once(BASE . '/include/diimport.class.php');
	require_once(BASE . '/include/diregion.class.php');
	require_once(BASE . '/include/diregionrecord.class.php');
	
	$RegionId = 'GAR-ISDR-2011_IDN';
	$us->login('diadmin','di8');
	$us->open($RegionId);
	
	$e = new DIEvent($us, '');
	//fb($e->getIdByName($us,'FLOOD'));
	//$e->set('EventId', 'FLOOD');
	//$e->load();
	//print_r($e->oField);
	$r = new DIRegionRecord($us, $RegionId);
	//$r->copyEvents('eng');
	//$r->copyCauses('eng');
	$i = new DIImport($us);
	//$a = $i->importFromCSV('/tmp/IDN_event.csv', DI_EVENT, true, 0);
	//$a = $i->importFromCSV('/tmp/IDN_cause.csv', DI_CAUSE, true, 0);
	//$a = $i->importFromCSV('/tmp/IDN_geolevel.csv', DI_GEOLEVEL, true, 0);
	$a = $i->importFromCSV('/tmp/IDN_geography.csv', DI_GEOGRAPHY, true, 0);
	//$a = $i->importFromCSV('/tmp/IDN_disaster.csv', DI_DISASTER, true, 0);
	$us->close();
	$us->logout();
</script>
