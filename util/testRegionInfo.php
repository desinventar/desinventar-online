#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . '/include/diregioninfo.class.php');

	$RegionId = 'GTM-1257291154-guatemala_inventario_historico_de_desastres';	
	$us->login('diadmin','di8');
	$us->open($RegionId);
	
	$r = new DIRegionInfo($RegionId);
	$r->loadFromXML();
	print_r($r->info);
	
	$us->close();
	$us->logout();
</script>
