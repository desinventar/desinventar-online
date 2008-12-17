<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

// Load required Functions
if (!isset($_GET['lang']))
  $_GET['lang'] = "en";
require_once('include/loader.php');
$t->config_dir = 'include';
if (isset($_GET['p']))
  $t->assign ("page", $_GET['p']);

$t->display ("gar.tpl");
</script>
