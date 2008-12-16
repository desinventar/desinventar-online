<script language="php">
  
require_once('../include/loader.php');
require_once('../include/dictionary.php');

//-----
if (!extension_loaded('json')) {
 dl( "json.so" );
}

define('DEFAULT_CHARSET', 'iso8859-1');

function json_safe_encode($var) {
   return json_encode(json_fix_cyr($var));
}

function json_fix_cyr($var) {
   if (is_array($var)) {
       $new = array();
       foreach ($var as $k => $v) {
           $new[json_fix_cyr($k)] = json_fix_cyr($v);
       }
       $var = $new;
   } elseif (is_object($var)) {
       $vars = get_class_vars(get_class($var));
       foreach ($vars as $m => $v) {
           $var->$m = json_fix_cyr($v);
       }
   } elseif (is_string($var)) {
       $var = iconv(DEFAULT_CHARSET, 'utf-8', $var);
   }
   return $var;
}
//------------

//  error_reporting(E_ALL ^ E_NOTICE);

$d = new Dictionary($dbs_dir);

$sAction = '';
if ($_REQUEST['action']) {
    $sAction = $_REQUEST['action'];
}
if ($sAction == "loadLabel") {
    header('Content-type: text/json');
    $data = $_REQUEST["data"]; // data is $labgrp|$labname|$langID
    $data = str_replace("\\\"","\"",$data);
    $lbl = explode("|", $data);
    $value = $d->queryLabel($lbl[0],$lbl[1],$lbl[2]);
    print json_safe_encode($value);
}
else {
  $statusmessage = '';
  $sCommand = '';

  if ($_POST['command']) {
    $sCommand = $_POST['command'];
  }
  if ($sCommand == 'addlang') {
    $sLangID = $_POST['LangID'];
    $sLangName = $_POST['LangName'];
    $sLangNameEN = $_POST['LangNameEN'];
    $sLangNotes = $_POST['LangNotes'];
    $sLangAdmin = $_POST['LangAdmin'];
    $res = $d->insertLang($sLangID, $sLangName, $sLangNameEN, $sLangNotes, $sLangAdmin);
    if ($res)
      $statusmessage = "New Language -". $sLangName ."- Saved!";
    else 
      $statusmessage = "Error saving -". $sLangName ."-.. sorry!";
  }
  else if ($sCommand == 'addtraduction') {
    $sLGName = $_POST['LGName'];
    $sLabelName = $_POST['LabelName'];
    $sDicTranslation = $_POST['DicTranslation'];
    $sDicTechHelp = $_POST['DicTechHelp'];
    $sDicBasDesc = $_POST['DicBasDesc'];
    $sDicFullDesc = $_POST['DicFullDesc'];
    $sLangID = $_POST['LangID'];
    $res = $d->updateDicLabel($sLGName,$sLabelName,$sDicTranslation,$sDicTechHelp,$sDicBasDesc,$sDicFullDesc,$sLangID);
    if ($res)
      $statusmessage = "Traduction Label -". $sLabelName ."- Saved!";
    else 
      $statusmessage = "Error saving -". $sLabelName ."-.. sorry!";
  }

  $lng = $d->loadAllLang();
  $label = $d->loadAllLabels();
  $grp = array();
  foreach ($label as $k=>$v) {
    $data = explode("|", $v);
    if (array_key_exists($data[0], $grp))
      array_push($grp[$data[0]], $data[count($data)-1]);
    else
      $grp[$data[0]] = array($data[count($data)-1]);
    $data = null;
  }

  $t->assign ("grp", $grp);
  $t->assign ("lng", $lng);
  $t->assign ("sm", $statusmessage);
  $t->display ("dictionary.tpl");
}

</script>
