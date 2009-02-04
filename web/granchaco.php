<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 ***********************************************/

// Load required Functions
if (!isset($_GET['lang']))
  $_GET['lang'] = "es";
require_once('include/loader.php');
$t->config_dir = 'include';
if (isset($_GET['p']))
  $t->assign ("page", $_GET['p']);

$t->assign("menutitle", "Gran&nbsp;Chaco");
$menuitem = array(
              'SubNacionales' => array(
                "GCPAR" => "Paraguay - El Chaco",
                "GCARG" => "Argentina - El Chaco", 
                "GCBOL" => "Bolivia - El Chaco"
              ),
              'Nacionales' => array(
                "PARAGUAY"  => "Paraguay - Nacional"
              )
            );
$virtualitem = array(
				"GRANCHACO" => "SubRegión Gran Chaco"
               );           
$t->assign("virtualitem", $virtualitem);            
$t->assign("menuitem", $menuitem);

// Select which logos to display
$t->assign("links_predecan", false);
$t->assign("links_can", true);
$t->assign("links_desaprender", true);
$t->assign("links_gar", false);
$t->display ("main.tpl");
</script>
