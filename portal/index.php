<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/
	require_once('../web/include/fb.php');
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

	$t->assign ("DIver", "8.2.0.50");
	$t->assign ("DImode", "online");
	
	$t->assign("stat", "on");

// PAGES: Show Information for selected Page from top menu
if (isset($_GET['p'])) {
	fb('Set ' . $_GET['p']);
	if ($_GET['p'] == 'init') {
		if (file_exists('default/index.php')) {
			include("default/index.php");
			exit();
		} else {
			fb('regionlist');
			$reglst = array();
			$result = $d->core->query("SELECT RegionId, RegionLabel FROM Region WHERE RegionStatus=3 ORDER BY RegionLabel, RegionOrder");
			while ($row = $result->fetch(PDO::FETCH_OBJ))
				$reglst[$row->RegionId] = $row->RegionLabel;
			$t->assign ("ctl_init", true);
			$t->assign ("reglst", $reglst);
		}
	} else {
		fb('init');
		$t->assign ("ctl_pages", true);
		$t->assign ("menu", $d->queryLabelsFromGroup('MainPage', $lg));
		$t->assign ("page", $_GET['p']);
	}
} else {
	fb('Unset');
	// Default portal: init session and get country list
	//$t->assign ("menu", $d->queryLabelsFromGroup('MainPage', $lg));
	// load languages available list
	//$t->assign ("lglst", $d->loadLanguages(1));
	// load available countries with databases
	$ctlst = array();
	/*
	$result = $d->core->query("SELECT CountryIso FROM Region WHERE RegionStatus=3 GROUP BY CountryIso");
	while ($row = $result->fetch(PDO::FETCH_OBJ))
		$ctlst[] = $row->CountryIso;
	*/
	$t->assign ("ctlst", $ctlst);
}
$t->display ("index.tpl");

</script>
