<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporación OSSO
*/
	require_once('../web/include/loader.php');
	
	$portaltype = getParameter('portaltype', 'desinventar');
	if (isset($_SERVER['DI8_GAR2011'])) {
		$portaltype = 'gar2011';
	}
	if (isset($_SERVER['DI8_GAR2009'])) {
		$portaltype = 'gar2009';
	}
	$t->assign('portaltype', $portaltype);
	$t->display('index.tpl');
</script>
