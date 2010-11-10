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
	
	$RegionId = 'GAR-ISDR-2011_MEX';
	$us->login('diadmin','di8');
	$us->open($RegionId);
	//$r = new DIRegion($us, $RegionId);
	//$r->copyEvents('spa');
	//$r->copyCauses('spa');
	$i = new DIImport($us);
	//$a = $i->importFromCSV('/tmp/mx_event.csv', DI_EVENT, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_cause.csv', DI_CAUSE, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_geography.csv', DI_GEOGRAPHY, true, 0);
	//$a = $i->importFromCSV('/tmp/MEX_geography2.csv', DI_GEOGRAPHY, true, 0);
	//$a = $i->importFromCSV('/tmp/mx_disaster.csv', DI_DISASTER, true, 0);
	$us->close();
	$us->logout();
</script>
