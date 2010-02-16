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
	
	//$pass = generatePasswd();
	//print($pass) . "<br />\n";
	//print md5('di8welcome') . "<br />\n";
	
	//$RegionId = 'HTI-1263415699-haiti_disaster_database';
	$RegionId = 'HTI-1263415555-haiti_disaster_database_gadm';
	
	$us->login('diadmin','di8');
	$us->open($RegionId);
	$r = new DIRegion($us, $RegionId);
	$r->copyEvents('fre');
	$r->copyCauses('fre');
	//$i = new DIImport($us);
	//$a = $i->importFromCSV('/tmp/2010-11-15_DI8_HTI_GeographyCodes.csv', DI_GEOGRAPHY, true, 0);
	$us->close();
	$us->logout();
</script>
