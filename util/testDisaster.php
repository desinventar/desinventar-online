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

	$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
	$us->login('diadmin','di8');
	$us->open($RegionId);
	
	$d = new DIDisaster($us,'bd5dc04d-e9b6-47eb-bbb2-87b5ff8405f8');
	/*
	$d->set('CauseId','ERUPTION');
	$d->set('DisasterBeginTime','2010-01-01');
	$d->set('DisasterSerial','2010-0008');
	$d->set('EventId','ACCIDENT');
	$d->set('GeographyId','00016');
	$d->set('DisasterSource','DEMO');
	$d->set('EffectPeopleDead', 2);
	*/

	//$i = $d->validateCreate(1);
	//fb($i);
	$i = $d->validateEffects(-61, 1);
	fb($i);
	$i = $d->validateUpdate(1);
	fb($i);
	//$i = $d->insert();
	//fb('insert : ' . $i);
	$us->close();
	$us->logout();
</script>
