<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporación OSSO
*/
	require_once('../web/include/fb.php');
	require_once('../web/include/common.php');
	require_once('../web/include/loader.php');

	// Configure DI8 (web) application location
	if (isset($_SERVER["REDIRECT_DI8_URL"])) {
		$_SERVER["DI8_URL"] = $_SERVER["REDIRECT_DI8_URL"];
	}
	$desinventarURL = $_SERVER["DI8_URL"];
	
	/* Smarty configuration */
	define("SMARTYDIR", "/usr/share/php/Smarty");
	define("SMTY_DIR", "/var/cache/Smarty"); // Smarty temp dir
	require_once(SMARTYDIR . '/Smarty.class.php');
	fb(SMARTYDIR);

	$t->config_dir = 'conf';
	$t->template_dir = 'templates';
	$t->assign('desinventarURL'         , $desinventarURL);
	
	// 2009-09-23 (jhcaiced) Choose which ISDR logo to use...
	$isdrlogo = 'logo-isdr.gif';
	if ($lg == 'spa') { $isdrlogo = 'logo-eird.gif'; }
	$t->assign("isdrlogo", $isdrlogo);
	$t->assign('imgCANMap', 'subreg_can_' . $lg . '.png');
	$t->display ("index.tpl");
</script>
