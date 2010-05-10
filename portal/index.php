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
	// Auto detect language for portal interface
	$lang = getParameter('lang', getBrowserClientLanguage());
	
	/* Smarty configuration */
	define("SMARTYDIR", "/usr/share/php/Smarty");
	define("SMTY_DIR", "/var/cache/Smarty"); // Smarty temp dir
	require_once(SMARTYDIR . '/Smarty.class.php');

	/* SMARTY template */
	/*
	$t = new Smarty();
	$t->debugging = false;
	$t->force_compile = true;
	$t->caching = false;
	$t->compile_check = true;
	$t->cache_lifetime = -1;
	$t->compile_dir = SMTY_DIR;
	$t->left_delimiter = '{-';
	$t->right_delimiter = '-}';
	*/
	$t->config_dir = 'conf';
	$t->template_dir = 'templates';

	$t->assign("stat"  , "on");

	$t->assign("lang"  , $lang);

	$t->assign('desinventarURL'    , $desinventarURL);
	$t->assign('desinventarVersion', VERSION);
	$t->assign('desinventarLang'   , $lang);
	$t->assign('desinventarUserId' , $us->UserId);
	$t->assign('desinventarModule' , 'portal');
	
	// 2009-09-23 (jhcaiced) Choose which ISDR logo to use...
	$isdrlogo = 'logo-isdr.gif';
	if ($lang == 'spa') { $isdrlogo = 'logo-eird.gif'; }
	$t->assign("isdrlogo", $isdrlogo);
	$t->assign('imgCANMap', 'subreg_can_' . $lang . '.png');
	$t->display ("index.tpl");
</script>
