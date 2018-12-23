<?php
function form2user($val)
{
    $dat = array();
    if (!isset($val['UserId'])) {
        $ifo = current($val);
    }
    $dat['UserId']          = isset($val['UserId'])? $val['UserId']: key($val);
    $dat['UserFullName']    = isset($val['UserFullName'])? $val['UserFullName']: $ifo[2];
    $dat['UserEMail']       = isset($val['UserEMail'])? $val['UserEMail']: $ifo[0];
    $dat['UserCountry']     = isset($val['UserCountry'])? $val['UserCountry']: $ifo[4];
    $dat['UserCity']        = isset($val['UserCity'])? $val['UserCity']: $ifo[5];
    if (isset($val['UserActive'])) {
        if (($val['UserActive'] == 'on') || $val['UserActive']) {
            $dat['UserActive']  = true;
        } else {
            $dat['UserActive']  = false;
        }
    } else {
        $dat['UserActive']  = 1;
    }
    $dat['NUserPasswd']     = isset($val['NUserPasswd'])? $val['NUserPasswd']: '';
    $dat['NUserPasswd2']    = isset($val['NUserPasswd2'])? $val['NUserPasswd2']: '';
    if (!empty($dat['NUserPasswd']) && ($dat['NUserPasswd'] == $dat['NUserPasswd2'])) {
        $dat['UserPasswd']      = $dat['NUserPasswd'];
    } elseif (isset($val['UserPasswd']) && !empty($val['UserPasswd'])) {
        $dat['UserPasswd']      = $val['UserPasswd'];
    } elseif (isset($ifo[1]) && !empty($ifo[1])) {
        $dat['UserPasswd']      = $ifo[1];
    } else {
        $dat['UserPasswd']      = 'desinventar'; // default password
    }
    return $dat;
}
