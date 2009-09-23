<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/
	require_once('../web/include/fb.php');
	require_once('../web/include/common.php');

	// Configure DI8 (web) application location
	if (isset($_SERVER["REDIRECT_DI8_URL"])) {
		$_SERVER["DI8_URL"] = $_SERVER["REDIRECT_DI8_URL"];
	}
	$di_url = $_SERVER["DI8_URL"];
	
	// Auto detect language for portal interface
	$lang = getParameter('lang', getBrowserClientLanguage());
	
	/* Smarty configuration */
	define("SMARTYDIR", "/usr/share/php/Smarty");
	define("SMTY_DIR", "/var/cache/Smarty"); // Smarty temp dir
	require_once(SMARTYDIR . '/Smarty.class.php');

	/* SMARTY template */
	$t = new Smarty();
	$t->debugging = false;
	$t->force_compile = true;
	$t->caching = false;
	$t->compile_check = true;
	$t->cache_lifetime = -1;
	$t->config_dir = 'include';
	$t->template_dir = 'templates';
	$t->compile_dir = SMTY_DIR;
	$t->left_delimiter = '{-';
	$t->right_delimiter = '-}';

	$t->assign("stat"  , "on");
	$t->assign("lang"  , $lang);
	$t->assign("di_url", $di_url);
	
	// 2009-09-23 (jhcaiced) Choose which ISDR logo to use...
	$isdrlogo = 'logo-isdr.gif';
	if ($lang == 'spa') { $isdrlogo = 'logo-eird.gif'; }
	$t->assign("isdrlogo", $isdrlogo);

	$t->display ("index.tpl");
</script>
