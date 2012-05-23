<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
	require_once('include/loader.php');
	$mod = 'metguide';
	$pages = array(
		'intro','whatis','aboutdesinv','regioninfo','geography',
		'events','causes','extraeffects','datacards','references'
	);
	$metguide = array();
	foreach($pages as $pagekey)
	{
		$metguide[$pagekey]  = $us->q->queryLabel($mod, $pagekey , $lg);
	}
	$t->assign('metguide', $metguide);
	$t->force_compile   = true; # Force this template to always compile
	$t->display('metguide.tpl');
</script>
