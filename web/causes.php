<?php
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/dicause.class.php');

require_once 'include/cause_operations.php';

use DesInventar\Common\Util;

use \DesInventar\Legacy\DICause;

$get = $_POST;
$RegionId = getParameter('r', getParameter('RegionId'));
if ($RegionId == '') {
    exit();
}
$us->open($RegionId);
$cmd = getParameter('cmd', '');

$dat = form2cause($get);
switch ($cmd) {
    case 'cmdCauseInsert':
        if ($us->UserRoleValue >= ROLE_ADMINREGION) {
            $info = $_POST['Info'];
            if (! isset($info['CauseActive'])) {
                $info['CauseActive'] = 'off';
            }
            $o = new DICause($us);
            $o->setFromArray($info);
            $util = new Util();
            $o->set('CauseId', $util->uuid4());
            $o->set('CausePredefined', 0);
            $i = $o->insert();
            showResult($i, $t);
        }
        break;
    case 'cmdCauseUpdate':
        if ($us->UserRoleValue >= ROLE_ADMINREGION) {
            $o = new DICause($us, $info['CauseId']);
            $info = $_POST['Info'];
            if (! isset($info['CauseActive'])) {
                $info['CauseActive'] = 'off';
            }
            if ($info['CausePredefined'] > 0) {
                if ($o->get('CauseName') != $info['CauseName']) {
                    $info['CausePredefined'] = 2; // Predefined but Localized
                }
            }
            $o->setFromArray($info);
            $i = $o->update();
            showResult($i, $t);
        }
        break;
    case 'list':
        // reload list from local SQLITE
        $prmType = getParameter('predef');
        if ($prmType == '1') {
            $t->assign('ctl_caupred', true);
            $t->assign('caupredl', $us->q->loadCauses('PREDEF', null, $lg, $us->RegionLangIsoCode, false));
        } else {
            $t->assign('ctl_caupers', true);
            $t->assign('cauuserl', $us->q->loadCauses('USER', null, $lg, $us->RegionLangIsoCode, false));
        }
        break;
    case 'chkname':
        $t->assign('ctl_chkname', true);
        $CauseId = getParameter('CauseId');
        $CauseName = getParameter('CauseName');
        if ($us->q->isvalidObjectName($CauseId, $CauseName, DI_CAUSE)) {
            $t->assign('chkname', true);
        }
        break;
    case 'chkstatus':
        $t->assign('ctl_chkstatus', true);
        $CauseId = getParameter('CauseId');
        if ($us->q->isvalidObjectToInactivate($CauseId, DI_CAUSE)) {
            $t->assign('chkstatus', true);
        }
        break;
    case 'cmdDBInfoCause':
        $t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
        $urol = $us->getUserRole($RegionId);
        if ($urol == 'OBSERVER') {
            $t->assign('ro', 'disabled');
        }
        $t->assign('ctl_show', true);
        $t->assign('ctl_caupred', true);
        $t->assign('caupredl', $us->q->loadCauses('PREDEF', null, $lg, $us->RegionLangIsoCode, false));
        $t->assign('ctl_caupers', true);
        $t->assign('cauuserl', $us->q->loadCauses('USER', null, $lg, $us->RegionLangIsoCode, false));
        break;
    default:
        break;
} //switch
$t->assign('reg', $RegionId);
$t->force_compile   = true;
$t->display('causes.tpl');
