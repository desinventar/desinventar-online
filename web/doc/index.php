<script language="php">
  require_once('../include/loader.php');
  require_once('../include/dictionary.class.php');
  require_once('../include/query.class.php');
  $d = new Dictionary(DICT_DIR);
  if (isset($_GET['m']))
    $mod = $_GET['m'];
  else
    $mod = "DI8Info";
  if (isset($_GET['p'])) {
    $pag = $_GET['p'];
    $q = new Query("");
    if ($pag == "events")
      $t->assign ("eve", $q->loadEvents("BASE", null, $lg));
    if ($pag == "causes")
      $t->assign ("cau", $q->loadCauses("BASE", null, $lg));
  }
  else
    $pag = "main";
  $t->assign ("ctl_page", $pag);
  $t->assign ("ctl_module", $mod);
  if ($mod == "DI8Info" && $pag == "intro")
    $show = $d->queryLabel('MainPage', 'DI8', $lg);
  else
    $show = $d->queryLabel($mod, $pag, $lg);
  if (isset($_GET['title']) && $_GET['title'] == "no")
    $t->assign ("title", false);
  else
    $t->assign ("title", true);
  if (is_array($show)) {
    $t->assign ("pagetitle", $show['DicTranslation']);
    $t->assign ("pagedesc", $show['DicBasDesc']);
    $t->assign ("pagefull", $show['DicFullDesc']);
    $t->assign ("urlinfo", $show['DicTechHelp']);
  }
  $t->display ("index.tpl");
</script>
