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
	
	$RegionId = 'MLI-1272020970-mali_disaster_database';
	$us->login('diadmin','di8');
	$us->open($RegionId);
	//$r = new DIRegion($us, $RegionId);
	//$r->copyEvents('spa');
	//$r->copyCauses('spa');
	$i = new DIImport($us);
	//$a = $i->importFromCSV('/tmp/MLI_geography.csv', DI_GEOGRAPHY, true, 0);
	//$a = $i->importFromCSV('/tmp/gg_disaster.csv', DI_DISASTER, true, 0);
	$us->close();
	$us->logout();
</script>
