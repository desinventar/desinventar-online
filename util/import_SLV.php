#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../include/loader.php');
	require_once(BASE . '/include/dievent.class.php');
	require_once(BASE . '/include/dicause.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/didisaster.class.php');
	require_once(BASE . '/include/dieedata.class.php');
	require_once(BASE . '/include/diimport.class.php');
	
	//$pass = generatePasswd();
	//print($pass) . "<br />\n";
	//print md5('di8welcome') . "<br />\n";
	
	$RegionId = 'SLV-1250695592-el_salvador_inventario_historico_de_desastres';
	
	$us->login('diadmin','di8');
	$us->open($RegionId);
	
	$i = new DIImport($us);
	//$r = $i->importFromCSV('STDIN', DI_DISASTER, false, 0);
	//$r = $i->importFromCSV('/tmp/SLV_disaster.csv', DI_DISASTER, true, 0);
	$r = $i->importFromCSV('/tmp/SLV_disaster_earthquake2001.csv', DI_DISASTER, true, 0);
	$us->close();
	$us->logout();
</script>
