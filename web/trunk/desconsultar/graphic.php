<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

require_once('../include/loader.php');
require_once('../include/dictionary.class.php');
require_once('../include/query.class.php');
require_once('../include/graphic.class.php');

if (isset($_POST['_REG']) && !empty($_POST['_REG']))
  $reg = $_POST['_REG'];
else
  exit();

$q = new Query($reg);
$rinfo = $q->getDBInfo();
$regname = $rinfo['RegionLabel'];
$post = $_POST;

$d = new Dictionary(VAR_DIR);
// load levels to display in totalizations
foreach ($q->loadGeoLevels("") as $k=>$i)
  $st["GraphDisasterGeographyId_". $k] = array($i[0], $i[1]);
$dic = array_merge(array(), $st);
$dic = array_merge($dic, $d->queryLabelsFromGroup('Graph', $lg));
$dic = array_merge($dic, $d->queryLabelsFromGroup('Effect', $lg));
$dic = array_merge($dic, $d->queryLabelsFromGroup('Sector', $lg));
$dic = array_merge($dic, $q->getEEFieldList("True"));
$t->assign ("dic", $dic);
$t->assign ("regname", $regname);

if (isset($post['_G+cmd'])) {
  // Process QueryDesign Fields and count results
  $qd = $q->genSQLWhereDesconsultar($post);
  $sqc = $q->genSQLSelectCount($qd);
  $c = $q->getresult($sqc);
  $cou = $c['counter'];
  // Process Configuration options to Graphic
  $ele = array();
  // Prepare Group to complete query
  foreach (explode("|", $post['_G+Type']) as $itm) {
    if ($itm == "D.DisasterBeginTime") {
      if (isset($post['_G+Stat']) && strlen($post['_G+Stat'])>0)
        $ele[] = $post['_G+Stat'] ."|". $itm;
      else
        $ele[] = $post['_G+Period'] ."|". $itm;
    }
    elseif (substr($itm, 2, 19) == "DisasterGeographyId") {
      $gl = explode("_", $itm);
      $ele[] = $gl[1] ."|". $gl[0];// "0|$itm"; 
    }
    else
      $ele[] = "|". $itm;
  }
  $opc['Group'] = $ele;
  $opc['Field'] = $post['_G+Field'];
  $sql = $q->genSQLProcess($qd, $opc);
  $dislist = $q->getassoc($sql);
  if (!empty($dislist)) {
    // Process results data
    $dl = $q->prepareGraphic($dislist);
    $gl = array();
    // Traduce Labels
    foreach ($dl as $k=>$i) {
      $kk = substr($k, 0, -1); // Select Hay marked like EffectsXX_
      if (isset($dic['Graph'. $k][0]))
        $dk = $dic['Graph'. $k][0];
      elseif (isset($dic[$k][0]))
        $dk = $dic[$k][0];
      elseif (isset($dic[$kk][0]))
        $dk = $dic[$kk][0];
      else
        $dk = $k;
      $gl[$dk] = $i;
    }
    if ($post['_G+cmd'] == "export") {
      header("Content-type: Image/png");
      header("Content-Disposition: attachment; filename=DI8_". str_replace(" ", "", $regname) ."_Graphic.png");
    }
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check:0', false);
    header('Pragma: no-cache');
    $g = new Graphic($post['_G+Kind'], $post, $gl);
    $image = "../tmp/di8graphic_". $_SESSION['sessionid'] ."_.png";
    if ($post['_G+cmd'] == "export") {
      readfile($image);
      exit();
    }
    else {
      $t->assign ("qdet", $q->getQueryDetails($dic, $post));
      $t->assign ("image", "$image?". rand(1,3000));
      $t->assign ("ctl_showres", true);
    }
  }
}
$t->display ("graphic.tpl");

</script>
