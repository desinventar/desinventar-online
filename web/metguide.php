<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
require_once('include/loader.php');
$mod = 'metguide';
$pages = array(
    'intro','whatis','aboutdesinv','regioninfo','geography',
    'events','causes','extraeffects','datacards','references'
);
$metguide = array();
foreach ($pages as $pagekey) {
    $metguide[$pagekey]  = $us->q->queryLabel($mod, $pagekey, $lg);
}
$t->assign('metguide', $metguide);

# EventList
$EventListDefault = $us->q->loadEvents('BASE', null, $lg, $us->RegionLangIsoCode, false);
$t->assign('EventListDefault', $EventListDefault);

$CauseListDefault = $us->q->loadCauses('BASE', null, $lg, $us->RegionLangIsoCode, false);
$t->assign('CauseListDefault', $CauseListDefault);

$ef1 = $us->q->queryLabelsFromGroup('Effect|People', $lg);
$ef2 = $us->q->queryLabelsFromGroup('Effect|Affected', $lg);
$ef3 = $us->q->queryLabelsFromGroup('Effect|Economic', $lg);
$ef4 = $us->q->queryLabelsFromGroup('Effect|More', $lg);
$sec = $us->q->queryLabelsFromGroup('Sector', $lg);
$EffectList = array_merge($ef1, $ef2, $ef3, $ef4, $sec);
$t->assign('EffectList', $EffectList);

if ($config->flags['mode'] == 'devel') {
    $t->force_compile   = true; # Force this template to always compile
}
$t->display('metguide-' . $lg . '.tpl');
