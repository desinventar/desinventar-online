<?php

function form2cause($form)
{
    $data = array ();
    if (isset($form['CauseId']) && !empty($form['CauseId'])) {
        $data['CauseId'] = $form['CauseId'];
    } else {
        $data['CauseId'] = '';
    }
    if (isset($form['CauseName'])) {
        $data['CauseName'] = $form['CauseName'];
    }
    if (isset($form['CauseDesc'])) {
        $data['CauseDesc'] = $form['CauseDesc'];
    }
    if (isset($form['CauseActive']) && $form['CauseActive'] == 'on') {
        $data['CauseActive'] = 1;
    } else {
        $data['CauseActive'] = 0;
    }
    if (isset($form['CausePredefined']) && $form['CausePredefined'] == '1') {
        $data['CausePredefined'] = 1;
    } else {
        $data['CausePredefined'] = 0;
    }
    return $data;
}

function showResult($stat, &$tp)
{
    if (!iserror($stat)) {
        $tp->assign('ctl_msgupdcau', true);
    } else {
        $tp->assign('ctl_errupdcau', true);
        $tp->assign('updstatcau', $stat);
        $tp->assign('ctl_chkname', true);
        $tp->assign('ctl_chkstatus', true);
        if ($stat != ERR_OBJECT_EXISTS) {
            $tp->assign('chkname', true);
        }
        if ($stat != ERR_CONSTRAINT_FAIL) {
            $tp->assign('chkstatus', true);
        }
    }
}
