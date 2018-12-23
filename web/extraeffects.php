<?php
use Aura\Session\SessionFactory;
use DesInventar\Common\Language;
use DesInventar\Legacy\Model\EEField;

require_once('include/loader.php');
require_once('include/query.class.php');

$get = $_POST;
$RegionId = getParameter('RegionId', getParameter('r', ''));
if ($RegionId == '') {
    exit();
}
$sessionFactory = new SessionFactory();
$session = $sessionFactory->newInstance($_COOKIE);
$segment = $session->getSegment('');
$lg = (new Language())->getLanguageIsoCode($segment->get('language'), Language::ISO_639_2);
$t->assign('lg', $lg);

$cmd = getParameter('cmd', '');
$us->open($RegionId);
switch ($cmd) {
    case 'cmdEEFieldInsert':
    case 'cmdEEFieldUpdate':
        $status = 0;
        if ($get['EEField']['EEFieldActive'] == 'on') {
            $status |= CONST_REGIONACTIVE;
        } else {
            $status &= ~CONST_REGIONACTIVE;
        }
        if ($get['EEField']['EEFieldPublic'] == 'on') {
            $status |= CONST_REGIONPUBLIC;
        } else {
            $status &= ~CONST_REGIONPUBLIC;
        }
        $get['EEField']['EEFieldStatus'] = $status;
        $o = new EEField($us, $get['EEField']['EEFieldId']);
        $EEFieldId = $o->get('EEFieldId');
        $o->setFromArray($get['EEField']);
        $o->set('EEFieldId', $EEFieldId);
        $o->set('RegionId', $RegionId);
        if ($cmd == 'cmdEEFieldInsert') {
            if ($EEFieldId == '') {
                $EEFieldId = $o->getNextEEFieldId();
                $o->set('EEFieldId', $EEFieldId);
            }
            $stat = $o->insert();
        } elseif ($cmd == 'cmdEEFieldUpdate') {
            $stat = $o->update();
        }
        $answer = array();
        if (!iserror($stat)) {
            $answer['Status'] = 'OK';
        } else {
            $answer['Status'] = 'ERROR';
            $answer['ErrorMsg'] = showerror($stat);
        }
        echo json_encode($answer);
        break;
    case 'cmdEEFieldList':
        // reload list from local SQLITE
        $t->assign('eef', $us->q->getEEFieldList(''));
        $t->assign('ctl_eeflist', true);
        $t->force_compile   = true;
        $t->display('extraeffects.tpl');
        break;
    default:
        $t->assign('reg', $RegionId);
        $t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
        $urol = $us->getUserRole($RegionId);
        $t->assign('ctl_admineef', true);
        $eef =  $us->q->getEEFieldList('');
        $t->assign('eef', $eef);
        $t->assign('ctl_eeflist', true);
        $t->force_compile   = true;
        $t->display('extraeffects.tpl');
        break;
}
