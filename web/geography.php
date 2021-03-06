<?php
use Aura\Session\SessionFactory;
use DesInventar\Common\Language;
use DesInventar\Legacy\Model\GeographyItem;

require_once('include/loader.php');
require_once('include/query.class.php');

$get = $_GET;

$RegionId = getParameter('RegionId', getParameter('r', ''));
if ($RegionId == '') {
    exit();
}

$sessionFactory = new SessionFactory();
$session = $sessionFactory->newInstance($_COOKIE);
$segment = $session->getSegment('');
$lg = (new Language())->getLanguageIsoCode($segment->get('language'), Language::ISO_639_2);
$t->assign('lg', $lg);

$us->open($RegionId);
$cmd = getParameter('cmd', '');

// Set Variables to insert or update
$dat = array();
$dat['GeographyId'] = isset($get['GeographyId']) ? $get['GeographyId']: '';
$dat['GeoParentId'] = isset($get['GeoParentId']) ? $get['GeoParentId']: '';
$dat['GeographyLevel'] = isset($get['GeographyLevel']) ? $get['GeographyLevel']: '';
$dat['GeographyCode'] = isset($get['GeographyCode']) ? $get['GeographyCode']:'';
$dat['GeographyName'] = isset($get['GeographyName']) ? $get['GeographyName']:'';
if (isset($get['GeographyActive']) && $get['GeographyActive'] == 'on') {
    $dat['GeographyActive'] = 1;
} else {
    $dat['GeographyActive'] = 0;
}

switch ($cmd) {
    case 'cmdGeographyInsert':
    case 'cmdGeographyUpdate':
        $answer = array();
        $GeographyId = $_POST['data']['GeographyId'];
        $o = new GeographyItem($us, $GeographyId);
        $o->setFromArray($_POST['data']);
        if ($cmd == 'cmdGeographyInsert') {
            $o->setGeographyId($_POST['data']['GeoParentId']);
            $i = $o->insert();
        } else {
            $i = $o->update();
        }
        $answer['Status'] = $i;
        echo json_encode($answer);
        break;
    case 'list':
        $lev = $us->q->getNextLev($get['GeographyId']);
        $t->assign('lev', $lev);
        $t->assign('levmax', $us->q->getMaxGeoLev());
        $t->assign('levname', $us->q->loadGeoLevById($lev));
        $t->assign('geol', $us->q->loadGeoChilds($get['GeographyId']), GEOGRAPHY_ALL);
        $t->assign('ctl_geolist', true);
        break;
    case 'chkcode':
        $t->assign('ctl_chkcode', true);
        if ($us->q->isvalidObjectName($get['GeographyId'], $get['GeographyCode'], DI_GEOGRAPHY)) {
            $t->assign('chkcode', true);
        }
        break;
    case 'chkstatus':
        $t->assign('ctl_chkstatus', true);
        if ($us->q->isvalidObjectToInactivate($get['GeographyId'], DI_GEOGRAPHY)) {
            $t->assign('chkstatus', true);
        }
        break;
    case 'cmdDBInfoGeography':
        $t->assign('ctl_admingeo', true);
        $lev = 0;
        $levmax = $us->q->getMaxGeoLev();
        $levname = $us->q->loadGeoLevById($lev);
        $geol = $us->q->loadGeography($lev, GEOGRAPHY_ALL);
        $t->assign('lev', $lev);
        $t->assign('levmax', $levmax);
        $t->assign('levname', $levname);
        $t->assign('geol', $geol);
        $t->assign('ctl_geolist', true);
        $urol = $us->getUserRole($reg);
        break;
    default:
        break;
}
$t->assign('reg', $reg);
$t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
$t->force_compile   = true;
$t->display('geography.tpl');
