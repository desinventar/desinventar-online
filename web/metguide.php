<?php
use Aura\Session\SessionFactory;
use DesInventar\Common\Language;

require_once('include/loader.php');

$sessionFactory = new SessionFactory();
$session = $sessionFactory->newInstance($_COOKIE);
$segment = $session->getSegment('');
$lg = (new Language())->getLanguageIsoCode($segment->get('language'), Language::ISO_639_2);
$t->assign('lg', $lg);

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
    $t->force_compile   = true;
}
$t->display('metguide-' . $lg . '.tpl');
