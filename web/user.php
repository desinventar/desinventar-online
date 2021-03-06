<?php
require_once('include/loader.php');
require_once 'include/user_operations.php';

use Aura\Session\SessionFactory;
use DesInventar\Common\Language;
use DesInventar\Legacy\UserSession;
use DesInventar\Legacy\Model\User;

$sessionFactory = new SessionFactory();
$session = $sessionFactory->newInstance($_COOKIE);
$segment = $session->getSegment('');
$lg = (new Language())->getLanguageIsoCode($segment->get('language'), Language::ISO_639_2);
$t->assign('lg', $lg);

$cmd = getParameter('cmd', '');
switch ($cmd) {
    case 'login':
        // LOGIN: CONTROL USER ACCESS
        $Answer = array('Status' => 'ERROR');
        $UserId = getParameter('UserId');
        $UserPasswd = getParameter('UserPasswd');
        if ($us->login($UserId, $UserPasswd, UserSession::PASSWORD_IS_HASHED) > 0) {
            $Answer['Status'] = ERR_NO_ERROR;   // Login success
            $Answer['UserId'] = $us->UserId;
            $Answer['UserFullName'] = $us->getUserFullName();
        }
        echo json_encode($Answer);
        break;
    case 'logout':
        // LOGOUT : Logut current user and show the login panel again
        $us->logout();
        echo json_encode(array('Status' => ERR_NO_ERROR));
        break;
    case 'getUserInfo':
        $user = null;
        $UserId = getParameter('UserId', '');
        if ($UserId != '') {
            $user = new User($us, $UserId);
            echo json_encode($user->getInfo());
        }
        break;
    case 'chklogin':
        $UserId = getParameter('UserId');
        // USERADMIN: check if UserId exists...
        $Answer = 'NO';
        if ($us->doUserExist($UserId) > 0) {
            $Answer = 'YES';
        }
        echo $Answer;
        break;
    case 'updatepasswd':
        // Check if password is correct (ask to dicore). if is OK show dialog to change it.
        if (!$us->validateUser($us->UserId, $_POST['UserPasswd'], UserSession::PASSWORD_IS_HASHED)) {
            echo 'ERRORPASSWD';
        } else {
            $us->updateUserPasswd($us->UserId, $_POST['UserPasswd2']);
            echo 'OK';
        }
        break;
    // USERADMIN: update selected user..
    case 'insert':
    case 'update':
        $bReturn = ERR_NO_ERROR;
        // This function is valid only for ADMINPORTAL User (root)
        if ($us->UserId != 'root') {
            $bReturn = ERR_UNKNOWN_ERROR;
        }
        if ($bReturn > 0) {
            $data = $_POST['User'];
            $UserId = $data['UserId'];
            if ($UserId == '') {
                $bReturn = ERR_UNKNOWN_ERROR;
            }
        }

        if ($cmd == 'insert') {
            if ($us->doUserExist($UserId) > 0) {
                $bReturn = ERR_USER_DUPLICATE_ID;
            }
        }
        if ($bReturn > 0) {
            $u = new User($us, $UserId);
            if ($cmd == 'insert') {
                // set a Default passwd for new users...
                $data['UserPasswd'] = md5('desinventar');
            } else {
                // Do not change passwd here !!
                unset($data['UserPasswd']);
            }
            $u->setFromArray($data);
            if ($cmd == 'insert') {
                $bReturn = $u->insert();
            }
            $bReturn = $u->update();
        }
        if ($bReturn > 0) {
            if (!empty($data['new_passwd'])) {
                $u->updatePasswd($UserId, $data['new_passwd']);
            }
        }
        $Answer = array('Status' => $bReturn);
        echo json_encode($Answer);
        break;
    default:
        // View login window
        if (checkAnonSess() || $us->UserId == '') {
            $t->force_compile   = true;
            $t->display('user_login.tpl');
        } else {
            $t->assign('user', $us->UserId);
            $t->force_compile   = true;
            $t->display('user_mainpage.tpl');
        }
        break;
}
