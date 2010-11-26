#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . "/include/query.class.php");
	require_once(BASE . "/include/diobject.class.php");
	require_once(BASE . "/include/dievent.class.php");
	require_once(BASE . "/include/dicause.class.php");
	require_once(BASE . "/include/digeolevel.class.php");
	require_once(BASE . "/include/digeocarto.class.php");
	require_once(BASE . "/include/digeography.class.php");
	require_once(BASE . "/include/didisaster.class.php");
	require_once(BASE . "/include/dieedata.class.php");
	require_once(BASE . "/include/diimport.class.php");
	require_once(BASE . "/include/dieefield.class.php");
	require_once(BASE . "/include/diregion.class.php");
	require_once(BASE . '/include/diregiondb.class.php');
	require_once(BASE . "/include/diregionitem.class.php");
	require_once(BASE . "/include/diregionauth.class.php");
	require_once(BASE . "/include/diuser.class.php");
	require_once(BASE . "/include/disync.class.php");

	$r = ERR_NO_ERROR;
	$RegionId = 'DESINV-1249040429-can_subregion_andina';
	//$RegionId = 'DESINV-1249126759-subregion_gran_chaco';
	$us->open($RegionId);
	$o = new DIRegionDB($us, $RegionId);
	$o->rebuildRegionData();
	
	$us->q->dreg->query('DELETE FROM Disaster WHERE EventId="8d1f7788-2766-47f2-8472-deea68515086"');
	$us->q->dreg->query('DELETE FROM Disaster WHERE EventId="f8280625-4320-4033-81cb-8feb19bd2f2e"');
	$us->q->dreg->query('DELETE FROM Disaster WHERE EventId="fb1184be-1912-46c3-b4de-22011701e26c"');
	$us->close();
</script>
