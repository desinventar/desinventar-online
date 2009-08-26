#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	$_SERVER["DI8_WEB"] = '../web';
	require_once($_SERVER["DI8_WEB"] . "/include/loader.php");
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
	require_once(BASE . "/include/diregionitem.class.php");
	require_once(BASE . "/include/diregionauth.class.php");
	require_once(BASE . "/include/diuser.class.php");
	require_once(BASE . "/include/disync.class.php");

	// loader.php creates a UserSession when loaded...
	//$us = new UserSession();
	$r = ERR_NO_ERROR;
	$RegionId = 'DESINV-1249040429-can_subregion_andina';
	//$RegionId = 'DESINV-1249126759-subregion_gran_chaco';
	$us->open($RegionId);
	$o = new DIRegion($us, $RegionId);
	$o->rebuildRegionData();
	$us->close();
</script>
