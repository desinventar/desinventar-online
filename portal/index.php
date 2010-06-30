<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporación OSSO
*/
	require_once('../web/include/loader.php');

	$t->config_dir = 'conf';
	$t->template_dir = 'templates';

	$t->assign("stat"  , "on");
	
	// 2009-09-23 (jhcaiced) Choose which ISDR logo to use...
	$isdrlogo = 'logo-isdr.gif';
	if ($lang == 'spa') { $isdrlogo = 'logo-eird.gif'; }
	$t->assign("isdrlogo", $isdrlogo);
	$t->assign('imgCANMap', 'subreg_can_' . $lang . '.png');
	$t->display ("index.tpl");
</script>
