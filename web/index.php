<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

// Load required Functions
require_once('include/loader.php');

$t->config_dir = 'include';

$d = new Query();

$t->assign ("DIver", "8.2.0-23");
$t->assign ("DImode", MODE);
// 2009-01-20 (jhcaiced) At this point, loader.php should have
// created or loaded the UserSession in the $us variable
$us->awake();
$t->assign("stat", "on");

// PAGES: Show Information for selected Page from top menu
if (isset($_GET['p'])) {
  $t->assign ("ctl_pages", true);
  $t->assign ("menu", $d->queryLabelsFromGroup('MainPage', $lg));
  $t->assign ("page", $_GET['p']);
}
// Default portal: init session and get country list
else {
  $t->assign ("menu", $d->queryLabelsFromGroup('MainPage', $lg));
  $cmd = "";
  $t->assign ("cmd", $cmd);
}

// 2009-01-19 (jhcaiced) This should keep the UserSession info between pages
$_SESSION['sessioninfo'] = $us;
$t->display ("index.tpl");

</script>
