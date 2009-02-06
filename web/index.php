<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

// Load required Functions
require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/user.class.php');
require_once('include/dictionary.class.php');
$t->config_dir = 'include';

$d = new Dictionary(VAR_DIR);

$t->assign ("DIver", "8.2.0-1");

// UPDATER: If user keep connect the session will not expire..
if (isset($_GET['u'])) {
  $u = new User('', '', '');
  $t->assign ("ctl_updater", true);
//  if (checkUserSess()) {
  $res = $u->awakeUserSession();
  if (!iserror($res) || (checkAnonSess() && $res == ERR_ACCESS_DENIED))
    $status = "on";
  else
    $status = "off";
  $t->assign ("stat", $status);
//  }
//    $t->assign ("stat", $_GET['u'] . " min");
}
// PAGES: Show Information for selected Page from top menu
else if (isset($_GET['p'])) {
  $t->assign ("ctl_pages", true);
  $t->assign ("menu", $d->queryLabelsFromGroup('MainPage', $lg));
  $t->assign ("page", $_GET['p']);
}
// Default portal: init session and get country list
else {
  $t->assign ("menu", $d->queryLabelsFromGroup('MainPage', $lg));
  // reconnect with exist session.. uhmm
  if (checkUserSess()) {
    $u = new User('', '', '');
    $cmd = "relogin";
  }
  // Start anonymous session
  else {
    $u = new User('init', '', '');
    $cmd = "";
  }
  $t->assign ("cmd", $cmd);
}

if (LNX)
  $t->assign ("shw_vreg", true);
else
  $t->assign ("shw_vreg", false);
    
$t->display ("index.tpl");

</script>
