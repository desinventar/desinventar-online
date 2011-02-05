<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2010 Corporación OSSO
*/
	require_once('../web/include/loader.php');

	$portaltype = getParameter('portaltype', 'desinventar');
	$t->assign('desinventarPortalType', $portaltype);
	$t->display('index.tpl');
</script>
