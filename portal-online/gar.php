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

$t->assign("menutitle", "GAR-ISDR");
$menuitem = array(
              'Asia'          => array(
                "INORISSA"    => "India - Orissa",
                "INTAMILNADU" => "India - Tamil Nadu", 
                "IRAN"        => "Iran",
                "NEPAL"       => "Nepal", 
                "SRILANKA"    => "Srilanka"),
              'Latin America' => array(
                "ARGENTINA"   => "Argentina", 
                "BOLIVIA"     => "Bolivia",
                "COLOMBIA"    => "Colombia",
                "COSTARICA"   => "Costa Rica",
                "ECUADOR"     => "Ecuador",
                "SALVADOR"    => "El Salvador",
                "MEXICO"      => "México",
                "PERU"        => "Perú",
                "VENEZUELA"   => "Venezuela"),
              'Cities'        => array(
                "COLCALI"     => "Cali - Colombia")
             );
$virtualitem = array(
				"CAN" => "SubRegión Andina"
               );             
$t->assign("virtualitem", $virtualitem);             
$t->assign("menuitem", $menuitem);

// Select which logos to display
$t->assign("links_gar", true);
$t->display ("main.tpl");
</script>
