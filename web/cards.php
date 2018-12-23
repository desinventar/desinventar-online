<?php
require_once('include/loader.php');
require_once('include/query.class.php');

use DesInventar\Service\Datacard;
use DesInventar\Legacy\Model\Disaster;

$RegionId = getParameter('RegionId', getParameter('r', ''));
if ($RegionId == '') {
    exit(0);
}

$desinventarUserRole = $us->getUserRole($RegionId);
$desinventarUserRoleValue = $us->getUserRoleValue($RegionId);

// 2009-08-07 (jhcaiced) Validate if Database Exists...
if (! file_exists($us->q->getDBFile($RegionId))) {
    echo '<h3>Requested Region not found<br />';
    exit();
}

$us->open($RegionId);
$t->assign('RegionId', $RegionId);

// UPDATER: If user is still connected, awake session so it will not expire
if (isset($_GET['u'])) {
    $res = $us->awake();
    if (!iserror($res)) {
        $status = 'green';
    } else {
        $status = 'red';
    }
    $t->assign('stat', $status);
    $t->force_compile   = true;
    $t->display('cards_updater.tpl');
} else {
    $cmd = getParameter('cmd', getParameter('DatacardCommand', ''));
    $value = getParameter('value', '');
    // Commands in GET mode: lists, checkings..
    switch ($cmd) {
        case 'getNextSerial':
            $service = new Datacard($us->q->dreg);
            $nextSerial = $service->nextSerial(
                $year,
                $value,
                getParameter('length', 5),
                getParameter('separator', ':')
            );
            echo json_encode(array('Status' => 'OK', 'DisasterSerial' => $nextSerial));
            break;
        case 'getDisasterIdPrev':
            $answer = $us->getDisasterIdPrev($value);
            echo json_encode($answer);
            break;
        case 'getDisasterIdNext':
            $answer = $us->getDisasterIdNext($value);
            echo json_encode($answer);
            break;
        case 'getDisasterIdFirst':
            $answer = $us->getDisasterIdFirst();
            echo json_encode($answer);
            break;
        case 'getDisasterIdLast':
            $answer = $us->getDisasterIdLast();
            echo json_encode($answer);
            break;
        case 'getDisasterIdFromSerial':
            $answer = $us->getDisasterIdFromSerial($value);
            echo json_encode($answer);
            break;
        case 'existDisasterSerial':
            $DisasterSerial = getParameter('DisasterSerial');
            $answer = $us->existDisasterSerial($DisasterSerial);
            echo json_encode($answer);
            break;
        case 'getDatacard':
            // Read Datacard Info and return in JSON
            $DisasterId = getParameter('DisasterId', '');
            $d = new Disaster($us, $DisasterId);
            $dcard = $d->oField['info'];
            $dcard['DisasterBeginTime[0]'] = substr($dcard['DisasterBeginTime'], 0, 4);
            $dcard['DisasterBeginTime[1]'] = '';
            $dcard['DisasterBeginTime[2]'] = '';
            if (strlen($dcard['DisasterBeginTime']) > 4) {
                $dcard['DisasterBeginTime[1]'] = substr($dcard['DisasterBeginTime'], 5, 2);
            }
            if (strlen($dcard['DisasterBeginTime']) > 7) {
                $dcard['DisasterBeginTime[2]'] = substr($dcard['DisasterBeginTime'], 8, 2);
            }
            $gItems = $us->getGeographyItemsById($dcard['GeographyId']);
            $dcard['GeographyItems'] = $gItems;
            echo json_encode($dcard);
            break;
        case 'insertDICard':
        case 'updateDICard':
            // Update Existing Datacard
            $answer = array();
            $answer['Status']     = 'OK';
            $answer['StatusCode'] = 'UPDATEOK';
            if ($cmd == 'insertDICard') {
                $answer['StatusCode'] = 'INSERTOK';
            }
            $answer['StatusMsg']  = '';
            $answer['ErrorCode']  = ERR_NO_ERROR;
            $service = new Datacard($us->q->dreg);
            if ($cmd == 'insertDICard') {
                $data = $service->form2disaster($_POST, Datacard::CMD_NEW);
                $data['RecordAuthor'] = $us->UserId;
            } else {
                $data = $service->form2disaster($_POST, CMD_UPDATE);
            }
            $o = new Disaster($us, $data['DisasterId']);
            $o->setFromArray($data);
            if ($cmd == 'insertDICard') {
                $o->set('RecordCreation', gmdate('c'));
            }
            $o->set('RecordUpdate', gmdate('c'));
            try {
                if ($cmd == 'insertDICard') {
                    $i = $o->insert();
                } else {
                    $i = $o->update();
                }
            } catch (Exception $e) {
                $i = ERR_DEFAULT_ERROR;
            }
            if ($i > 0) {
                $us->releaseDatacard($_POST['DisasterId']);
            } else {
                $answer['Status']     = 'ERROR';
                $answer['StatusCode'] = 'ERROR';
                $answer['StatusMsg']  = showerror($i) . '(' . $i . ')';
                $answer['ErrorCode']  = $i;
            }
            if ($answer['Status'] == 'OK') {
                $answer['DisasterId']      = $o->get('DisasterId');
                $answer['DisasterSerial']  = $o->get('DisasterSerial');
                $answer['RecordPublished'] = $us->getNumDisasterByStatus('PUBLISHED');
                $answer['RecordReady']     = $us->getNumDisasterByStatus('READY');
            }

            if ($cmd == 'insertDICard') {
                $answer['RecordCount'] = $us->getDisasterCount();
            }
            echo json_encode($answer);
            break;
        default:
            break;
    }
}
