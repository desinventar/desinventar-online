<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

	require_once('./include/fb.php');
	require_once('./include/common.php');
	
	// Auto detect language for portal interface
	$lang = getBrowserClientLanguage();
	
	// Load required Functions
	define("SMARTYDIR", "/usr/share/php/Smarty");
	define("SMTY_DIR", "/var/cache/Smarty"); // Smarty temp dir
	/* Smarty configuration */
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

	$t->assign("stat", "on");
	$t->assign("lang", $lang);
	// Available languages

	$t->display ("index.tpl");

</script>
