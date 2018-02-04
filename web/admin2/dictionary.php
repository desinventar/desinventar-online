<?php

require_once '../include/loader.php';

if (!extension_loaded('json')) {
    dl("json.so");
}

$util = new DesInventar\Common\Util();
$d = new Dictionary($dbs_dir);

$sAction = '';
if ($_REQUEST['action']) {
    $sAction = $_REQUEST['action'];
}
if ($sAction == "loadLabel") {
    header('Content-type: text/json');
    $data = $_REQUEST["data"]; // data is $labgrp|$labname|$langID
    $data = str_replace("\\\"", "\"", $data);
    $lbl = explode("|", $data);
    $value = $d->queryLabel($lbl[0], $lbl[1], $lbl[2]);
    print $util->jsonSafeEncode($value);
} else {
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
        if ($res) {
            $statusmessage = "New Language -". $sLangName ."- Saved!";
        } else {
            $statusmessage = "Error saving -". $sLangName ."-.. sorry!";
        }
    } elseif ($sCommand == 'addtraduction') {
        $sLGName = $_POST['LGName'];
        $sLabelName = $_POST['LabelName'];
        $sDicTranslation = $_POST['DicTranslation'];
        $sDicTechHelp = $_POST['DicTechHelp'];
        $sDicBasDesc = $_POST['DicBasDesc'];
        $sDicFullDesc = $_POST['DicFullDesc'];
        $sLangID = $_POST['LangID'];
        $res = $d->updateDicLabel(
            $sLGName,
            $sLabelName,
            $sDicTranslation,
            $sDicTechHelp,
            $sDicBasDesc,
            $sDicFullDesc,
            $sLangID
        );
        if ($res) {
            $statusmessage = "Traduction Label -". $sLabelName ."- Saved!";
        } else {
            $statusmessage = "Error saving -". $sLabelName ."-.. sorry!";
        }
    }

    $lng = $d->loadAllLang();
    $label = $d->loadAllLabels();
    $grp = array();
    foreach ($label as $k => $v) {
        $data = explode("|", $v);
        if (array_key_exists($data[0], $grp)) {
            array_push($grp[$data[0]], $data[count($data)-1]);
        } else {
            $grp[$data[0]] = array($data[count($data)-1]);
        }
        $data = null;
    }

    $t->assign("grp", $grp);
    $t->assign("lng", $lng);
    $t->assign("sm", $statusmessage);
    $t->display("dictionary.tpl");
}
