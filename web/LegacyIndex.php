<?php

namespace DesInventar;

use \DesInventar\Legacy\UserSession;
use \DesInventar\Legacy\DIRegion;
use \DesInventar\Legacy\DIRegionDB;
use \DesInventar\Legacy\DIRegionRecord;
use \DesInventar\Legacy\DIGraph;
use \DesInventar\Legacy\DICause;
use \DesInventar\Legacy\DIEvent;
use \DesInventar\Legacy\DIGeoCarto;
use \DesInventar\Legacy\DIGeography;
use \DesInventar\Legacy\DIGeoLevel;
use \DesInventar\Legacy\DIProfile;
use DesInventar\Common\Version;
use DesInventar\Common\Util;
use DesInventar\Common\QueryOperations;

use \qqFileUploader;
use \ZipArchive;

class LegacyIndex
{
    public function __construct($template, $session, $language, $config)
    {
        $this->template = $template;
        $this->session = $session;
        $this->language = $language;
        $this->config = $config;
        $this->template->assign('lang', $language);
        $this->template->assign('lg', $language);
        $this->template->assign('desinventarLang', $language);
    }

    public function getResponse($cmd)
    {
        if (empty($cmd)) {
            $cmd = getCmd();
        }
        if ($cmd == '') {
            if (isset($_POST['prmQuery']['Command'])) {
                $cmd = $_POST['prmQuery']['Command'];
            }
        }
        return $this->getIndexResponse($cmd, $this->template, $this->session, $this->language, $this->config);
    }

    public function getIndexResponse($cmd, $t, $us, $lg, $config)
    {
        $post = $_POST;
        $get  = $_GET;

        $RegionId = getParameter('r', getParameter('RegionId', getParameter('_REG'), ''));
        $RegionLabel = '';
        if ($cmd == '' && $RegionId == '') {
            $cmd = 'main';
        }
        $t->assign('desinventarRegionId', $RegionId);
        if (!empty($RegionId)) {
            $us->open($RegionId);
            $r = new DIRegion($us, $RegionId);
            $RegionLabel = $r->getRegionInfoValue('RegionLabel');
        }
        $t->assign('desinventarRegionLabel', $RegionLabel);

        $desinventarUserRole = $us->getUserRole($RegionId);
        $desinventarUserRoleValue = $us->getUserRoleValue($RegionId);
        $t->assign('desinventarUserRole', $desinventarUserRole);
        $t->assign('desinventarUserRoleValue', $desinventarUserRoleValue);
        $t->assign('appOptions', $appOptions);

        $util = new Util();

        switch ($cmd) {
            case 'test':
                $ydb = $us->getDateRange();
                print_r($ydb);
                break;
            case 'cmdUserLogin':
                $iReturn = ERR_DEFAULT_ERROR;
                $answer = array();
                $UserId = getParameter('UserId');
                $UserPasswd = getParameter('UserPasswd');
                if ($us->login($UserId, $UserPasswd, UserSession::PASSWORD_IS_HASHED) > 0) {
                    $iReturn = ERR_NO_ERROR;    // Login success
                    $user = array();
                    $user['Id']        = $us->UserId;
                    $user['FullName']  = $us->getUserFullName();
                    $user['Role']      = $us->getUserRole($RegionId);
                    $user['RoleValue'] = $us->getUserRoleValue($RegionId);
                    $answer['User'] = $user;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdUserGetInfo':
                $iReturn = ERR_DEFAULT_ERROR;
                $answer = array();
                $UserId = $us->UserId;
                if ($UserId != '') {
                    $iReturn = ERR_NO_ERROR;    // Login success
                    $user = array();
                    $user['Id']        = $us->UserId;
                    $user['FullName']  = $us->getUserFullName();
                    $answer['User'] = $user;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdUserLogout':
                $iReturn = $us->logout();
                $user = array();
                $user['Id']        = '';
                $user['FullName']  = '';
                $user['Role']      = $us->getUserRole($RegionId);
                $user['RoleValue'] = $us->getUserRoleValue($RegionId);

                $answer = array();
                $answer['Status'] = $iReturn;
                $answer['User']   = $user;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdUserAccount':
                return $t->fetch('main_user_account.tpl');
                break;
            case 'cmdUserPasswdUpdate':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                $UserId = getParameter('UserId');
                if ($UserId != $us->UserId) {
                    $iReturn = ERR_DEFAULT_ERROR;
                }
                if ($iReturn > 0) {
                    $UserPasswd = getParameter('UserPasswd', '');
                    $UserPasswd2 = getParameter('UserPasswd2', '');
                    if ($us->validateUser($us->UserId, $_POST['UserPasswd'], UserSession::PASSWORD_IS_HASHED) != '') {
                        $us->updateUserPasswd($us->UserId, $_POST['UserPasswd2']);
                    } else {
                        $iReturn = ERR_DEFAULT_ERROR;
                    }
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdAdminDatabaseGetList':
                $answer = array();
                $answer['Status']     = ERR_NO_ERROR;
                $answer['RegionList'] = $us->q->getRegionAdminList();
                return json_encode($answer);
                break;
            case 'cmdAdminDatabaseGetInfo':
                $answer = array();
                $RegionId = getParameter('RegionId', '');
                $r = new DIRegion($us, $RegionId);
                $answer['Status'] = ERR_NO_ERROR;
                $answer['Region'] = $r->getRegionInfoCore();
                return json_encode($answer);
                break;
            case 'cmdDatacards':
            case 'datacards':
                $ef1 = $us->q->queryLabelsFromGroup('Effect|People', $lg);
                $ef2 = $us->q->queryLabelsFromGroup('Effect|Affected', $lg);
                $ef3 = $us->q->queryLabelsFromGroup('Effect|Economic', $lg);
                $ef4 = $us->q->queryLabelsFromGroup('Effect|More', $lg);
                $sec = $us->q->queryLabelsFromGroup('Sector', $lg);
                $t->assign('ef1', $ef1);
                $t->assign('ef2', $ef2);
                $t->assign('ef3', $ef3);
                $t->assign('ef4', $ef4);
                $t->assign('sec', $sec);
                return $t->fetch('main_datacards.tpl');
                break;
            case 'cmdDatacardLock':
                $DisasterId = getParameter('DisasterId', '');
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_USER) {
                    $iReturn = ERR_DEFAULT_ERROR;
                }
                if ($iReturn > 0) {
                    // check if datacard is locked by some user
                    $answer['DisasterId'] = $DisasterId;
                    $reserv = $us->isDatacardLocked($DisasterId);
                    if ($reserv == '') {
                        // reserve datacard
                        $us->lockDatacard($DisasterId);
                        $answer['DatacardStatus'] = 'RESERVED';
                    } else {
                        $answer['DatacardStatus'] = 'BLOCKED';
                    }
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdDatacardRelease':
                $disaster_id = getParameter('DisasterId', '');
                if ($disaster_id != '') {
                    $us->releaseDatacard($disaster_id);
                }
                break;
            case 'cmdGeographyGetItemsById':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                $GeographyId = getParameter('GeographyId', '');
                $GeographyList = array();
                for ($level = 1; $level < strlen($GeographyId)/5 + 1; $level++) {
                    $GeographyParent = substr($GeographyId, 0, $level * 5);
                    $GeographyLevel  = $level;
                    $GeographyList[$GeographyParent] = $us->getGeographyItemsByLevel($GeographyLevel, $GeographyParent);
                }
                $answer['GeographyList'] = $GeographyList;
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdGeographyGetItemsByLevel':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                $GeographyLevel  = getParameter('GeographyLevel', '');
                $GeographyParent = getParameter('GeographyParent', '');
                $GeographyList = array();
                $GeographyList = $us->getGeographyItemsByLevel($GeographyLevel, $GeographyParent);
                $answer['GeographyList'] = $GeographyList;
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'events':
            case 'cmdDatabaseEvents':
                $t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
                return $t->fetch('main_database_events.tpl');
                break;
            case 'cmdDatabaseEventsGetList':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $EventListDefault = $us->q->loadEvents('PREDEF', null, $lg, $us->RegionLangIsoCode, false);
                    $EventListCustom  = $us->q->loadEvents('USER', null, $lg, $us->RegionLangIsoCode, false);
                    $answer['EventListDefault'] = $EventListDefault;
                    $answer['EventListCustom']  = $EventListCustom;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdDatabaseEventsUpdate':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $info = $_POST['Event'];
                    $o = new DIEvent($us, $info['EventId']);
                    if ($info['EventPredefined'] > 0) {
                        if ($o->get('EventName') != $info['EventName']) {
                            $info['EventPredefined'] = 2;
                        }
                    }
                    $o->setFromArray($info);
                    if ($o->get('EventId') == '') {
                        $o->set('EventId', $util->uuid4());
                        $iReturn = $o->insert();
                    }
                    $iReturn = $o->update();
                    if ($iReturn > 0) {
                        $EventListDefault = $us->q->loadEvents('PREDEF', null, $lg, $us->RegionLangIsoCode, false);
                        $EventListCustom  = $us->q->loadEvents('USER', null, $lg, $us->RegionLangIsoCode, false);
                        $answer['EventListDefault'] = $EventListDefault;
                        $answer['EventListCustom']  = $EventListCustom;
                    }
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'causes':
            case 'cmdDatabaseCauses':
                $t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
                return $t->fetch('main_database_causes.tpl');
                break;
            case 'cmdDatabaseCausesGetList':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $CauseListDefault = $us->q->loadCauses('PREDEF', null, $lg, $us->RegionLangIsoCode, false);
                    $CauseListCustom  = $us->q->loadCauses('USER', null, $lg, $us->RegionLangIsoCode, false);
                    $answer['CauseListDefault'] = $CauseListDefault;
                    $answer['CauseListCustom']  = $CauseListCustom;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdDatabaseCausesUpdate':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $info = $_POST['Cause'];
                    $o = new DICause($us, $info['CauseId']);
                    if ($info['CausePredefined'] > 0) {
                        if ($o->get('CauseName') != $info['CauseName']) {
                            $info['CausePredefined'] = 2;
                        }
                    }
                    $o->setFromArray($info);
                    if ($o->get('CauseId') == '') {
                        $o->set('CauseId', $util->uuid4());
                        $iReturn = $o->insert();
                    }
                    $iReturn = $o->update();
                    if ($iReturn > 0) {
                        $CauseListDefault = $us->q->loadCauses('PREDEF', null, $lg, $us->RegionLangIsoCode, false);
                        $CauseListCustom  = $us->q->loadCauses('USER', null, $lg, $us->RegionLangIsoCode, false);
                        $answer['CauseListDefault'] = $CauseListDefault;
                        $answer['CauseListCustom']  = $CauseListCustom;
                    }
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdDatabaseUsers':
                return $t->fetch('main_database_users.tpl');
                break;
            case 'cmdDatabaseUsersGetList':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $r = new DIRegion($us, $RegionId);
                    $r->update();
                    $info = [
                        'RegionStatus' => $r->get('RegionStatus'),
                        'RegionOrder' => $r->get('RegionOrder')
                    ];
                    $UserList     = $us->getUserList();
                    $UserRoleList = $us->getRegionRoleList($RegionId);
                    $answer['RegionInfo']   = $info;
                    $answer['UserList']     = $UserList;
                    $answer['UserRoleList'] = $UserRoleList;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdDatabaseUsersSetRole':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $r = new DIRegion($us, $RegionId);
                    $UserId = getParameter('UserId', '');
                    $UserRole = getParameter('UserRole', '');
                    if ($UserRole == 'ADMINREGION') {
                        $r->removeRegionUserAdmin();
                    }
                    $iReturn = $us->setUserRole($UserId, $us->RegionId, $UserRole);
                    if ($iReturn > 0) {
                        $UserRoleList = $us->getRegionRoleList($us->RegionId);
                        $answer['UserRoleList'] = $UserRoleList;
                    }
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdDatabaseUsersUpdateOptions':
                $iReturn = ERR_NO_ERROR;
                $answer = array();
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    $r = new DIRegion($us, $RegionId);
                    $r->set('RegionStatus', $_POST['RegionStatus']);
                    $r->set('RegionOrder', $_POST['RegionOrder']);
                    $iReturn = $r->update();
                    $info = [
                        'RegionStatus' => $r->get('RegionStatus'),
                        'RegionOrder' => $r->get('RegionOrder')
                    ];
                    $answer['RegionInfo'] = $info;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                break;
            case 'cmdGetLocaleList':
                $answer = array();
                if (empty($us->UserId)) {
                    return json_encode(array('Status' => ERR_UNKNOWN_ERROR));
                }
                $countryList = array();
                foreach ($us->q->getCountryList() as $isoCode => $name) {
                    if (empty($isoCode)) {
                        continue;
                    }
                    $countryList[] = array(
                        'id' => trim($isoCode),
                        'text' => trim($name)
                    );
                }
                $languageList = array();
                foreach ($us->q->loadLanguages(1) as $code => $name) {
                    $languageList[] = array(
                        'id' => $code,
                        'text' => $name
                    );
                }
                $answer['LanguageList'] = $languageList;
                $answer['CountryList'] = $countryList;
                $answer['Status'] = ERR_NO_ERROR;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdGetUserPermList':
                $answer = array();
                $iReturn = ERR_UNKNOWN_ERROR;
                if ($desinventarUserRoleValue >= ROLE_ADMINPORTAL) {
                    $UserList  = $us->getUserList();
                    $UserAdmin = $us->getRegionUserAdminInfo();
                    $iReturn = ERR_NO_ERROR;
                    $answer['UserList'] = $UserList;
                    $answer['UserAdmin'] = $UserAdmin;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'admin':
            case 'cmdAdminMain':
            case 'cmdAdminDB':
                $t->assign('CountryList', $us->q->getCountryList());
                $t->assign('ctl_adminreg', true);
                $t->assign('ctl_reglist', true);
                $t->assign('ctl_admregmess', true);
                return $t->fetch('main_region.tpl');
                break;
            case 'cmdAdminUsers':
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINPORTAL) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $CountryList = $us->q->getCountryList();
                    $t->assign('CountryList', $CountryList);
                    $t->assign('usrpa', $us->getUserInfo(''));
                }
                return $t->fetch('main_admin_users.tpl');
                break;
            case 'cmdAdminUsersGetList':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINPORTAL) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $UserList = $us->getUserInfo('');
                    $answer['UserList'] = $UserList;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdDatabaseLoadData':
                $answer = array();
                $iReturn = ERR_NO_ERROR;

                // Session Parameters
                $params = array();
                $params['LangIsoCode']   = $lg;
                $params['RegionId']      = $RegionId;
                $params['RegionLabel']   = $RegionLabel;
                $params['UserId']        = $us->UserId;
                $params['UserFullName']  = $us->getUserFullName();
                $params['UserRole']      = $desinventarUserRole;
                $params['UserRoleValue'] = $desinventarUserRoleValue;
                $answer['RegionId'] = $RegionId;
                if ($RegionId != '') {
                    if (DIRegion::existRegion($us, $RegionId)) {
                        $r = new DIRegion($us, $RegionId);

                        $GeolevelsList = $r->getGeolevelList();
                        $answer['GeolevelsList'] = $GeolevelsList;
                        $EventList     = $us->q->loadEvents('ALL', 'active', $lg, $us->RegionLangIsoCode);
                        $answer['EventList'] = $EventList;
                        $CauseList     = $us->q->loadCauses('ALL', 'active', $lg, $us->RegionLangIsoCode);
                        $answer['CauseList'] = $CauseList;
                        $RecordCount = $us->getDisasterCount();
                        $answer['RecordCount'] = $RecordCount;
                        $GeographyList = $us->getGeographyItemsByLevel(0, '');
                        $answer['GeographyList'] = $GeographyList;

                        $EEFieldList = $us->q->getEEFieldList('True');
                        $answer['EEFieldList'] = $EEFieldList;

                        $ydb = $us->getDateRange();
                        $params['MinYear']       = substr($ydb[0], 0, 4);
                        $params['MaxYear']       = substr($ydb[1], 0, 4);
                        $answer['info'] = $r->getAllInfo($us->q->dreg);
                    }
                }
                $us->clearLocks();
                $answer['params'] = $params;
                $answer['Status'] = $iReturn;
                return json_encode($answer);
                break;
            case 'cmdGeography':
                return $t->fetch('main_database_geography.tpl');
                break;
            case 'cmdGeographyGetList':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $geography_id = getParameter('GeographyId', '');
                    $geography_list = $us->q->loadGeoChilds($geography_id, GEOGRAPHY_ALL);
                    $answer['GeographyList'] = $geography_list;
                    $answer['GeographyListCount'] = count($geography_list);
                }
                $answer['GeoLevelId'] = $us->q->getNextLev($geography_id);
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdGeographyUpdate':
                $Geography = $_POST['Geography'];
                $parent_id = getParameter('ParentId', '');
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $geography_id = isset($Geography['GeographyId']) ? $Geography['GeographyId'] : '';
                    $o = new DIGeography($us, $geography_id);
                    $geography_name_old = $o->get('GeographyName');
                    $o->setFromArray($Geography);
                    if ($geography_id == '') {
                        $o->setGeographyId($parent_id);
                        $o->setGeographyFQName();
                        $iReturn = $o->insert();
                    } else {
                        $iReturn = $o->update();
                    }
                    if ($iReturn > 0) {
                        if ($geography_name_old != $o->get('GeographyName')) {
                            $g = new DIGeocarto($us, $o->get('GeographyLevel'));
                            geography_update_dbf_record(
                                $g->getDBFFilename(),
                                $g->get('GeoLevelLayerCode'),
                                $g->get('GeoLevelLayerName'),
                                $o->get('GeographyCode'),
                                $o->get('GeographyName')
                            );
                        }
                    }
                }
                if ($iReturn > 0) {
                    $geography_list = $us->q->loadGeoChilds($parent_id, GEOGRAPHY_ALL);
                    $answer['GeographyList'] = $geography_list;
                    $answer['GeographyListCount'] = count($geography_list);
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdGeographyExport':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    header('Content-type: text/x-csv');
                    header('Content-Disposition: attachment; filename=DesInventar_' . $RegionId . '_Geography.csv');
                    $labels = '';
                    if (isset($_POST['Labels'])) {
                        $labels = $_POST['Labels'];
                    }
                    return $labels . "\n" . geography_export_to_csv($us->q->dreg);
                } else {
                    $answer['Status'] = $iReturn;
                    return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                }
                break;
            case 'cmdGeolevels':
                return $t->fetch('main_database_geolevels.tpl');
                break;
            case 'cmdGeolevelsGetList':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $r = new DIRegion($us, $RegionId);
                    $GeolevelsList = $r->getGeolevelList();
                    $answer['GeolevelsList'] = $GeolevelsList;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdGeolevelsUpdate':
                $GeoLevel = $_POST['GeoLevel'];
                $GeoLevelId = $GeoLevel['GeoLevelId'];
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $o = new DIGeoLevel($us, $GeoLevelId);
                    $o->setFromArray($GeoLevel);
                    if ($o->exist() > 0) {
                        $iReturn = $o->update();
                    } else {
                        $GeoLevelId = $o->getMaxGeoLevel();
                        if ($GeoLevelId < 0) {
                            $GeoLevelId = 0;
                        } else {
                            $GeoLevelId = $GeoLevelId + 1;
                        }
                        $o->set('GeoLevelId', $GeoLevelId);
                        $iReturn = $o->insert();
                    }
                    if ($iReturn > 0) {
                        $o = new DIGeoCarto($us, $GeoLevelId);
                        if (isset($GeoLevel['GeoLevelLayerCode'])) {
                            $o->set('GeoLevelLayerCode', $GeoLevel['GeoLevelLayerCode']);
                        }
                        if (isset($GeoLevel['GeoLevelLayerName'])) {
                            $o->set('GeoLevelLayerName', $GeoLevel['GeoLevelLayerName']);
                        }
                        if (isset($GeoLevel['filename'])) {
                            $GeoLevelLayerFile = 'geocarto' . sprintf('%02d', $GeoLevelId);
                            $SrcDir = $config->paths['tmp_dir'] . '/' . $us->sSessionId;
                            $OutDir = $us->getRegionDir($RegionId);
                            foreach ($GeoLevel['filename'] as $ext => $filename) {
                                $srcFile = $SrcDir . '/' . $filename;
                                $dstFile = $OutDir . '/' . $GeoLevelLayerFile . '.' . strtolower($ext);
                                ;
                                if (file_exists($srcFile)) {
                                    copy($srcFile, $dstFile);
                                }
                            }
                            $o->set('GeoLevelLayerFile', $GeoLevelLayerFile);
                            $answer['GeoLevelLayerFile'] = $GeoLevelLayerFile;
                        }
                        if ($o->get('GeoLevelLayerFile') != '') {
                            if ($o->exist() > 0) {
                                $o->update();
                            } else {
                                $o->insert();
                            }
                        }
                    }
                }
                $answer['GeoLevelId'] = $GeoLevelId;
                if ($iReturn > 0) {
                    $geography_items_count = geography_get_items_count($us->q->dreg, $GeoLevelId);
                    $answer['GeographyItemsCount'] = $geography_items_count;
                    $r = new DIRegion($us, $RegionId);
                    $GeolevelsList = $r->getGeolevelList();
                    $answer['GeolevelsList'] = $GeolevelsList;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdGeolevelsImportGeography':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($RegionId == '') {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $GeoLevel = $_POST['GeoLevel'];
                    $GeoLevelId = $GeoLevel['GeoLevelId'];
                    $geography_items_count = geography_get_items_count($us->q->dreg, $GeoLevelId);
                    if ($GeoLevel['option']['ImportGeography'] < 1) {
                        if ($geography_items_count < 1) {
                            $GeoLevel['option']['ImportGeography'] = 1;
                        }
                    }
                    if ($GeoLevel['option']['ImportGeography'] > 0) {
                        $File  = 'geocarto' . sprintf('%02d', $GeoLevelId) . '.dbf';
                        $DBDir = $us->getRegionDir($RegionId);
                        geography_import_from_dbf(
                            $us,
                            $GeoLevelId,
                            $DBDir . '/' . $File,
                            $GeoLevel['GeoLevelLayerCode'],
                            $GeoLevel['GeoLevelLayerName'],
                            $GeoLevel['GeoLevelLayerParentCode']
                        );
                    }
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES, 'UTF-8');
                break;
            case 'cmdGeolevelsUpload':
                $answer = array();
                $answer['success'] = false;
                $iReturn = ERR_NO_ERROR;
                if ($us->UserId == '') {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    require_once '../external/valums-fileuploader/fileuploader.php';
                    $allowedExtensions = array('dbf','shp','shx');
                    $sizeLimit = 100 * 1024 * 1024;
                    $OutDir = $config->paths['tmp_dir'] . '/' . $us->sSessionId;
                    if (!is_dir($OutDir)) {
                        mkdir($OutDir);
                    }
                    $uploader = new QqFileUploader($allowedExtensions, $sizeLimit);
                    $answer = $uploader->handleUpload($OutDir . '/');
                    if (isset($answer['error'])) {
                        $answer['success'] = false;
                    }
                    if ($answer['success'] == true) {
                        $iReturn = ERR_NO_ERROR;
                        $filename = $answer['filename'];
                        $answer['filename_orig'] = getParameter('qqfile', '');

                        $path_info = pathinfo($filename);
                        if (strtolower($path_info['extension']) == 'dbf') {
                            $answer['DBFFields'] = geography_get_fields_from_dbffile($OutDir . '/' . $filename);
                        }
                    }
                }
                if ($answer['success'] == false) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                break;
            case 'getversion':
                return VERSION;
                break;
            case 'cmdSessionAwake':
                $iReturn = $us->awake();
                $answer = array();
                $answer['Status'] = $iReturn;
                return json_encode($answer);
                break;
            case 'cmdRegionBuildRegionId':
                $answer = array();
                $answer['Status']     = ERR_NO_ERROR;
                $answer['CountryIso'] = getParameter('CountryIso');
                $answer['RegionId']   = DIRegion::buildRegionId($answer['CountryIso']);
                return json_encode($answer);
                break;
            case 'cmdDatabaseCreate':
                $iReturn = ERR_NO_ERROR;
                $answer = array();
                if ($us->UserId == '') {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    $RegionId = $_POST['Database']['RegionId'];
                    $iReturn = \DesInventar\Legacy\DatabaseOperations::create($us, $RegionId, $_POST['Database']);
                }
                if ($iReturn > 0) {
                    $answer['RegionId'] = $RegionId;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                break;
            case 'cmdDatabaseUpdate':
                $iReturn = ERR_NO_ERROR;
                $answer = array();
                if ($desinventarUserRoleValue < ROLE_ADMINPORTAL) {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    if ($RegionId == '') {
                        $RegionId = $_POST['Database']['RegionId'];
                    }
                    $r = new DIRegionRecord($us, $RegionId);
                    $iReturn = $r->setFromArray($_POST['Database']);
                    if ($r->get('RegionId') == '') {
                        $iReturn = ERR_UNKNOWN_ERROR;
                    }
                }
                if ($iReturn > 0) {
                    $RegionId = $r->get('RegionId');
                    $iReturn = $r->update();
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                break;
            case 'cmdDatabaseCopy':
                $iReturn = ERR_NO_ERROR;
                $answer = array();
                if ($us->UserId == '') {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    $RegionId = $_POST['RegionId'];
                    $RegionLabel = $_POST['RegionLabel'];
                    if (DIRegion::existRegion($us, $RegionId) < 0) {
                        $iReturn = \DesInventar\Legacy\DatabaseOperations::create($us, $RegionId, '');
                        $us->open($RegionId);
                    }
                    $desinventarUserRole = $us->getUserRole($RegionId);
                    ;
                    $desinventarUserRoleValue = $us->getUserRoleValue($RegionId);
                }
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    $iReturn = \DesInventar\Legacy\DatabaseOperations::replace(
                        $us,
                        $RegionId,
                        $RegionLabel,
                        getParameter('Filename', ''),
                        $config->paths['tmp_dir']
                    );
                }
                if ($iReturn > 0) {
                    $answer['RegionId'] = $RegionId;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                break;
            case 'cmdDatabaseReplace':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    $iReturn = \DesInventar\Legacy\DatabaseOperations::replace(
                        $us,
                        $RegionId,
                        '',
                        getParameter('Filename', ''),
                        $config->paths['tmp_dir']
                    );
                }
                $answer['Status'] = $iReturn;
                return json_encode($answer);
                break;
            case 'cmdDatabaseReplaceCancel':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINREGION) {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    $OutDir = $config->paths['tmp_dir'] . '/' . $us->sSessionId;
                    $Filename = getParameter('Filename', '');
                    if (file_exists($OutDir . '/' . $Filename)) {
                        unlink($OutDir . '/' . $Filename);
                    }
                }
                $answer['Status'] = $iReturn;
                return json_encode($answer);
                break;
            case 'cmdDatabaseDelete':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($desinventarUserRoleValue < ROLE_ADMINPORTAL) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                if ($iReturn > 0) {
                    $iReturn = \DesInventar\Legacy\DatabaseOperations::delete($us->q->core, $RegionId);
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                break;
            case 'cmdDatabaseSetUserAdmin':
                $answer = array();
                $iReturn = ERR_UNKNOWN_ERROR;
                if ($desinventarUserRoleValue >= ROLE_ADMINPORTAL) {
                    $UserId = getParameter('UserId', '');
                    if ($UserId != '') {
                        $r = new DIRegion($us, $RegionId);
                        $r->removeRegionUserAdmin();
                        $iReturn = $us->setUserRole($UserId, $us->RegionId, 'ADMINREGION');
                        if ($iReturn > 0) {
                            $UserAdmin = $us->getRegionUserAdminInfo();
                            $answer['UserAdmin'] = $UserAdmin;
                        }
                    }
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                break;
            case 'dbzipimport':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                if ($us->UserId != 'root') {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    $RegionId = $_POST['RegionInfo']['RegionId'];
                    $Filename = $config->paths['tmp_dir']
                      . '/DesInventarFile_' . $us->sSessionId . '_' . $_POST['RegionInfo']['Filename'];
                    $iReturn = DIRegionDB::createRegionDBFromZip(
                        $us,
                        $_POST['RegionInfo']['Mode'],
                        $RegionId,
                        $_POST['RegionInfo']['RegionLabel'],
                        $Filename
                    );
                    if ($iReturn > 0) {
                        $r = new DIRegion($us, $RegionId);
                        if (DIRegion::existRegion($us, $RegionId) < 0) {
                            $r->insert();
                        } else {
                            $r->update();
                        }
                    }
                }
                $answer['Status'] = $iReturn;
                return json_encode($answer);
                break;
            case 'cmdDatabaseUpload':
                $LangIsoCode = getParameter('LangIsoCode', $lg);
                $answer = array();
                $answer['success'] = false;
                $iReturn = ERR_NO_ERROR;
                if ($us->UserId == '') {
                    $iReturn = ERR_ACCESS_DENIED;
                }
                if ($iReturn > 0) {
                    require_once '../external/valums-fileuploader/fileuploader.php';
                    $allowedExtensions = array('zip');
                    $sizeLimit = 100 * 1024 * 1024;
                    $OutDir = $config->paths['tmp_dir'] . '/' . $us->sSessionId;
                    if (!is_dir($OutDir)) {
                        mkdir($OutDir);
                    }
                    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
                    $answer = $uploader->handleUpload($OutDir . '/');
                    if (isset($answer['error'])) {
                        $answer['success'] = false;
                    }
                    if ($answer['success'] == true) {
                        $iReturn = ERR_NO_ERROR;
                        $Filename = $answer['filename'];
                        $zip = new ZipArchive();
                        $res = $zip->open($OutDir . '/' . $Filename);
                        if ($res == true) {
                            if (file_exists($OutDir . '/info.xml')) {
                                unlink($OutDir . '/info.xml');
                            }
                            $zip->extractTo($OutDir, 'info.xml');
                            $zip->close();
                            if (file_exists($OutDir . '/info.xml')) {
                                $r = new DIRegion($us, '', $OutDir . '/info.xml');
                                $info = array();
                                $UploadMode = getParameter('UploadMode', '');
                                if ($UploadMode == 'Copy') {
                                    if (DIRegion::existRegion($us, $r->get('RegionId')) > 0) {
                                        $RegionId = DIRegion::buildRegionId($r->get('CountryIso'));
                                    } else {
                                        $RegionId = $r->get('RegionId');
                                    }
                                }
                                $info['RegionId']         = $RegionId;
                                $info['RegionLabel']      = $r->get('RegionLabel');
                                $info['CountryIso']       = $r->get('CountryIso');
                                $info['CountryName']      = $us->q->getCountryName($info['CountryIso'], $LangIsoCode);
                                $info['RegionLastUpdate'] = substr($r->get('RegionLastUpdate'), 0, 10);
                                $info['NumberOfRecords']  = $r->get('NumberOfRecords');
                                $answer['RegionInfo'] = $info;
                                $answer['DBExist'] = DIRegion::existRegion($us, $info['RegionId']);
                            } else {
                                $iReturn = ERR_INVALID_ZIPFILE; //-130
                            }
                            if (file_exists($OutDir . '/info.xml')) {
                                unlink($OutDir . '/info.xml');
                            }
                        } else {
                            $iReturn = ERR_UNKNOWN_ERROR;
                        }
                    }
                }
                if ($answer['success'] == false) {
                    $iReturn = ERR_UNKNOWN_ERROR;
                }
                $answer['Status'] = $iReturn;
                return htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                break;
            case 'main2':
                $t->assign('lg', $lg);
                $LanguageList = $us->q->loadLanguages(1);
                $CountryList = $us->q->getCountryList();
                $RegionList = $us->listDB();
                $t->assign('LanguageList', $LanguageList);
                $t->assign('CountryList', $CountryList);
                $t->assign('regionlist', $RegionList);
                $template = 'index-' . $lg . '.tpl';
                $t->force_compile   = true;
                return $t->fetch($template);
                break;
            case 'cmdSearchCountryList':
                $answer = array();
                $CountryList = $us->searchCountryList();
                $answer['Status'] = 'OK';
                $answer['CountryList'] = $CountryList;
                $answerstr = htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                if (isset($_GET['callback'])) {
                    $answerstr = $_GET['callback'] . '(' . $answerstr . ')';
                }
                return $answerstr;
                break;
            case 'cmdSearchDB':
            case 'searchdb':
                $searchDBQuery   = getParameter('searchDBQuery', getParameter('searchdbquery', ''));
                $searchDBCountry = getParameter('searchDBCountry', getParameter('searchbycountry', ''));
                $searchDBType    = getParameter('searchDBType', '');
                $LangIsoCode     = getParameter('LangIsoCode', $lg);
                $reglst = $us->searchDB($searchDBQuery, $searchDBCountry);
                if ($searchDBType == 'FULLINFO') {
                    foreach ($reglst as $RegionId => $RegionInfo) {
                        $r = new DIRegion($us, $RegionId);
                        $a = $r->getDBInfo($LangIsoCode);
                        unset($r);
                        $RegionList[$RegionId] = array_merge($RegionInfo, $a);
                    }
                } else {
                    $RegionList = $reglst;
                }
                $RoleList = array();
                $answer = array(
                    'Status'        => ERR_NO_ERROR,
                    'NoOfDatabases' => count($RegionList),
                    'RoleList'      => $RoleList,
                    'RegionList'    => $RegionList
                );
                $answerstr = htmlspecialchars(json_encode($answer), ENT_NOQUOTES);
                if (isset($_GET['callback'])) {
                    $answerstr = $_GET['callback'] . '(' . $answerstr . ')';
                }
                return $answerstr;
                break;
            case 'getCountryName':
                    $LangIsoCode = getParameter('LangIsoCode', $lg);
                    $CountryIso = getParameter('CountryIso', '');
                    $CountryName = $us->q->getCountryName($CountryIso, $LangIsoCode);
                    $answer = array('Status' => 1,
                                    'CountryName' => $CountryName);
                if (isset($_GET['callback'])) {
                    return $_GET['callback'] . '(' . json_encode($answer) . ')';
                } else {
                    return json_encode($answer);
                }
                break;
            case 'cmdDatabaseGetLogo':
                header('Content-type: Image/png');
                $murl = VAR_DIR . '/database/'. $RegionId . '/logo.png';
                if (!file_exists($murl)) {
                    $murl = 'images/di_logo.png';
                }
                return file_get_contents($murl);
            break;
            case 'cmdDatabaseGetInfo':
            case 'cmdGetRegionInfo':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                $LangIsoCode = getParameter('LangIsoCode', $lg);

                if ($RegionId == '') {
                    $iReturn = ERR_NO_DATABASE;
                }

                if ($iReturn > 0) {
                    $r = new DIRegion($us, $RegionId);
                    $a = $r->getDBInfo($LangIsoCode);
                    $a['CountryIso']  = $r->get('CountryIso');
                    $a['CountryName'] = $us->q->getCountryName($r->get('CountryIso'), $LangIsoCode);
                    $answer['RegionInfo'] = $a;
                }
                $answer['Status'] = $iReturn;
                if (isset($_GET['callback'])) {
                    return $_GET['callback'] . '(' . json_encode($answer) . ')';
                } else {
                    return json_encode($answer);
                }
                break;
            case 'getRegionRecordCount':
                $RecordCount = $us->getDisasterCount();
                return json_encode(array('Status' => 'OK', 'RecordCount' => $RecordCount));
                break;
            case 'getGraphParameters':
                $t->force_compile   = true;
                return $t->fetch('graphparameters.tpl');
                break;
            case 'cmdDatabaseExport':
                $answer = array('Status'   => ERR_UNKNOWN_ERROR);
                if ($desinventarUserRoleValue > ROLE_NONE) {
                    $ShortName = 'DesInventar_' . date('Y-m-d') . '_' . $RegionId . '.zip';
                    $FileName = $config->paths['www_dir'] . '/' . $SessionId . '/' . $ShortName;
                    $URL      = $config->paths['www_uri'] . '/' . $SessionId . '/' . $ShortName;
                    $r = new DIRegion($us);
                    $iReturn = $r->createRegionBackup($FileName);
                    if ($iReturn > 0) {
                        $answer['Status'] = ERR_NO_ERROR;
                        $answer['URL'     ] = $URL;
                    } else {
                        $answer['Status'] = ERR_UNKNOWN_ERROR;
                    }
                }
                return json_encode($answer);
                break;
            case 'savequery':
            case 'cmdQuerySave':
                fixPost($post);
                unset($post['_CMD']);
                header('Content-type: text/xml');
                header('Content-Disposition: attachment; filename=Query_' . str_replace(' ', '', $RegionId) . '.xml');
                return '<?xml version="1.0" encoding="UTF-8"?>'. "\n" .
                    '<desinventar version="' . VERSION . '">' . "\n" .
                    '	<diquery>' .
                            '<version>1.1</version>' .
                            '<value>' . base64_encode(serialize($post)) . '</value>' .
                        '</diquery>' . "\n" .
                    '</desinventar>' . "\n";
                break;
            case 'cmdQueryOpen':
                $answer = array();
                $iReturn = ERR_NO_ERROR;
                $xml_filename = BASE . '/test/query_2.0_geography.xml';
                $xml_string = file_get_contents($xml_filename);

                // Attempt to read as 1.0 query version (malformed XML)
                $query = new QueryOperations();
                $iReturn = $query->isV1($xml_string);
                if ($iReturn > 0) {
                    $diquery = $query->readV1($xml_string);
                } else {
                    $iReturn = $query->isV2($xml_string);
                    if ($iReturn > 0) {
                        $queryObj = $query->convertV2toV1($xml_string);
                        $qd  = $us->q->genSQLWhereDesconsultar($queryObj);
                        $sqc = $us->q->genSQLSelectCount($qd);
                        $c   = $us->q->getresult($sqc);
                        $iNumberOfRecords = $c['counter'];
                    }
                }
                break;
            case 'cmdQueryOpen2':
                if (isset($_FILES['qry'])) {
                    $myfile = $_FILES['qry']['tmp_name'];
                    $handle = fopen($myfile, 'r');
                    $cq = fread($handle, filesize($myfile));
                    fclose($handle);
                    $xml = '<DIQuery />';
                    $pos = strpos($cq, $xml);
                    if (!empty($cq) &&  $pos != false) {
                        $qy = substr($cq, $pos + strlen($xml));
                        $qd = unserialize(base64_decode($qy));
                    } else {
                        exit();
                    }
                    $RegionId = $qd['_REG'];
                    $t->assign('qd', $qd);
                } elseif (isset($get['r']) && !empty($get['r'])) {
                    $RegionId = $get['r'];
                }
                break;
            case 'cmdReports':
                print_r($_GET);
                print "<br />\n";
                print_r($_POST);
                break;
            case 'profiles':
            case 'reports':
            case 'cmdProfiles':
                require_once($config->paths['jpgraph_dir'] . '/jpgraph.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_line.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_log.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_date.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_bar.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_pie.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_pie3d.php');
                require_once('include/math.class.php');
                require_once('include/date.class.php');
                require_once('include/graphic.class.php');
                require_once('include/diresult.class.php');
                require_once('include/digraph.class.php');
                require_once('include/digraphxml.class.php');
                require_once('include/diprofile.class.php');
                $post = $_POST;
                fixPost($post);
                $post['General']['LangIsoCode'] = $lg;

                $options = explode('/', $_GET['params']);
                $profile_id   = isset($options[0]) ? $options[0] : '';
                $profile_item = isset($options[1]) ? $options[1] : '';

                $xml_string = file_get_contents('../samples/profile.xml');
                $xml_doc = new SimpleXMLElement($xml_string);
                $xml_profile = reset($xml_doc->xpath('profile/item'));

                $p = new DIProfile($us, $xml_profile);
                $html = $p->execute();
                return $html;
                break;
            case 'cmdGraphShow':
            case 'cmdGraphSave':
                require_once($config->paths['jpgraph_dir'] . '/jpgraph.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_line.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_log.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_date.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_bar.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_pie.php');
                require_once($config->paths['jpgraph_dir'] . '/jpgraph_pie3d.php');
                require_once('include/math.class.php');
                require_once('include/date.class.php');
                require_once('include/graphic.class.php');
                require_once('include/diresult.class.php');
                require_once('include/digraph.class.php');
                $post = $_POST;
                fixPost($post);
                $post['General']['LangIsoCode'] = $lg;
                $graph = new DIGraph($us, $post, $config->graphs);
                $graph->execute();

                if ($cmd == 'cmdGraphShow') {
                    $t->assign('RegionLabel', $RegionLabel);
                    $t->assign('NumRecords', $graph->output['NumRecords']);
                    $t->assign('qdet', $graph->output['QueryDetails']);
                    $t->assign('image', $graph->output['ImageURL']);
                    $t->assign('ctl_showres', true);
                    $t->force_compile   = true;
                    return $t->fetch('graphic.tpl');
                } else {
                    // Save Graph as Image
                    $filename = 'DesInventar_'. str_replace(' ', '', $RegionLabel) .'_Graphic.png';
                    header('Content-type: Image/png');
                    header('Content-Disposition: attachment; filename=' . $filename);
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
                    header('Cache-Control: no-store, no-cache, must-revalidate');
                    header('Cache-Control: post-check=0, pre-check:0', false);
                    header('Pragma: no-cache');
                    return file_get_contents($graph->output['ImageFile']);
                }
                break;
            case 'main':
                $t->assign('LanguageList', $us->q->loadLanguages(1));
                $t->assign('CountryList', $us->q->getCountryList());

                $t->assign('LabelsDisaster', $us->q->queryLabelsFromGroup('Disaster', $lg));
                $t->assign('LabelsRecord1', $us->q->queryLabelsFromGroup('Record|1', $lg));
                $t->assign('LabelsEvent', $us->q->queryLabelsFromGroup('Event', $lg));
                $t->assign('LabelsCause', $us->q->queryLabelsFromGroup('Cause', $lg));

                $t->assign('rc2', $us->q->queryLabelsFromGroup('Record|2', $lg));

                $ef1 = $us->q->queryLabelsFromGroup('Effect|People', $lg);
                $ef2 = $us->q->queryLabelsFromGroup('Effect|Affected', $lg);
                $ef3 = $us->q->queryLabelsFromGroup('Effect|Economic', $lg);
                $ef4 = $us->q->queryLabelsFromGroup('Effect|More', $lg);
                $sec = $us->q->queryLabelsFromGroup('Sector', $lg);

                $sec['SectorTransport'][3]      = null;
                $sec['SectorCommunications'][3] = null;
                $sec['SectorRelief'][3]         = null;
                $sec['SectorAgricultural'][3]   = null;
                $sec['SectorWaterSupply'][3]    = null;
                $sec['SectorSewerage'][3]       = null;
                $sec['SectorEducation'][3]      = null;
                $sec['SectorPower'][3]          = null;
                $sec['SectorIndustry'][3]       = null;
                $sec['SectorHealth'][3]         = null;
                $sec['SectorOther'][3]          = null;
                $dic = array();
                $dic = array_merge($dic, $us->q->queryLabelsFromGroup('MapOpt', $lg));
                $dic = array_merge($dic, $us->q->queryLabelsFromGroup('Graph', $lg));
                $dic = array_merge($dic, $us->q->queryLabelsFromGroup('DB', $lg));
                $dic = array_merge($dic, $ef1);
                $dic = array_merge($dic, $ef2);
                $dic = array_merge($dic, $ef3);
                $dic = array_merge($dic, $sec);
                $t->assign('dic', $dic);
                $t->assign('ef1', $ef1);
                $t->assign('ef2', $ef2);
                $t->assign('ef3', $ef3);
                $t->assign('ef4', $ef4);
                $t->assign('sec', $sec);
                $dc2 = array();
                $dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Disaster', $lg));
                $dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Record|2', $lg));
                $dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Geography', $lg));
                $dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Event', $lg));
                $dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Cause', $lg));
                $dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Effect', $lg));
                $dc2 = array_merge($dc2, $us->q->queryLabelsFromGroup('Sector', $lg));
                $t->assign('dc2', $dc2);
                $fld = 'DisasterSerial,DisasterBeginTime,EventName,GeographyFQName,DisasterSiteNotes,'.
                    'DisasterSource,EffectNotes,EffectPeopleDead,EffectPeopleMissing,EffectPeopleInjured,'.
                    'EffectPeopleHarmed,EffectPeopleAffected,EffectPeopleEvacuated,EffectPeopleRelocated,'.
                    'EffectHousesDestroyed,EffectHousesAffected,EffectFarmingAndForest,EffectRoads,'.
                    'EffectEducationCenters,EffectMedicalCenters,EffectLiveStock,EffectLossesValueLocal,'.
                    'EffectLossesValueUSD,EffectOtherLosses,SectorTransport,SectorCommunications,SectorRelief,'.
                    'SectorAgricultural,SectorWaterSupply,SectorSewerage,SectorEducation,SectorPower,SectorIndustry,'.
                    'SectorHealth,SectorOther,EventDuration,EventMagnitude,CauseName,CauseNotes';
                $sda = explode(',', $fld);
                $t->assign('sda', $sda);
                $sda1 = explode(
                    ',',
                    'GeographyCode,DisasterLatitude,DisasterLongitude,RecordAuthor,' .
                    'RecordCreation,RecordUpdate,EventNotes'
                );
                $t->assign('sda1', $sda1);

                // Map parameters
                $range[] = array(     10, '1 - 10'          , 'ffff99');
                $range[] = array(    100, '11 - 100'        , 'ffff00');
                $range[] = array(   1000, '101 - 1000'      , 'ffcc00');
                $range[] = array(  10000, '1001 - 10000'    , 'ff6600');
                $range[] = array( 100000, '10001 - 100000'  , 'cc0000');
                $range[] = array(1000000, '100001 - 1000000', '660000');
                $range[] = array(''     , '1000001 ->'      , '000000');
                $t->assign('range', $range);
                // Statistics
                $st = array();
                $std = array();
                $std = array_merge($std, $us->q->queryLabelsFromGroup('Statistic', $lg));
                $std = array_merge($std, $st);
                $t->assign('std', $std);

                // Datacards
                $t->assign('usr', $us->UserId);
                $dic = $us->q->queryLabelsFromGroup('DB', $lg);
                switch ($desinventarUserRole) {
                    case 'ADMINREGION':
                        $t->assign('showconfig', true);
                        $dicrole = $dic['DBRoleAdmin'][0];
                        break;
                    case 'OBSERVER':
                        $t->assign('showconfig', true);
                        $dicrole = $dic['DBRoleObserver'][0];
                        break;
                    case 'SUPERVISOR':
                        $dicrole = $dic['DBRoleSupervisor'][0];
                        break;
                    case 'USER':
                        $dicrole = $dic['DBRoleUser'][0];
                        break;
                    default:
                        $dicrole = null;
                        break;
                }
                $t->assign('dicrole', $dicrole);
                $t->assign('ctl_effects', true);
                $dis = $us->q->queryLabelsFromGroup('Disaster', $lg);
                $dis = array_merge($dis, $us->q->queryLabelsFromGroup('Geography', $lg));
                $t->assign('dis', $dis);
                $t->assign('rc1', $us->q->queryLabelsFromGroup('Record|1', $lg));
                $t->assign('rc2', $us->q->queryLabelsFromGroup('Record|2', $lg));
                $t->assign('eve', $us->q->queryLabelsFromGroup('Event', $lg));
                $t->assign('cau', $us->q->queryLabelsFromGroup('Cause', $lg));
                $sc3 = $us->q->querySecLabelFromGroup('Effect|Affected', $lg);
                $t->assign('sc3', $sc3);
                $t->assign('dmg', $us->q->queryLabelsFromGroup('MetGuide', $lg));

                if (isset($_SERVER['WINDIR'])) {
                    $desinventarHasInternet = 0;
                } else {
                    $desinventarHasInternet = 1;
                }
                $t->assign('desinventarHasInternet', $desinventarHasInternet);
                $t->assign('configfile', $lg . '.conf');
                $template = 'index-' . $lg . '.tpl';
                return $t->fetch($template);
                break;
            default:
                return '';
                break;
        }
    }
}
