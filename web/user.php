<script language="php">
/*
 **********************************************
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
 **********************************************
*/

require_once('include/loader.php');

function form2user($val) {
	$dat = array();
	if (!isset($val['UserId']))
		$ifo = current($val);
	$dat['UserId']			= isset($val['UserId'])? $val['UserId']: key($val);
	$dat['UserFullName']	= isset($val['UserFullName'])? $val['UserFullName']: $ifo[2];
	$dat['UserEMail']		= isset($val['UserEMail'])? $val['UserEMail']: $ifo[0];
	$dat['UserCountry']		= isset($val['UserCountry'])? $val['UserCountry']: $ifo[4];
	$dat['UserCity']		= isset($val['UserCity'])? $val['UserCity']: $ifo[5];
	if (isset($val['UserActive'])) {
		if (($val['UserActive'] == "on") || $val['UserActive'])
			$dat['UserActive']	= true;
		else
			$dat['UserActive']	= false;
	}
	else
		$dat['UserActive'] 	= 1;
	$dat['NUserPasswd']		= isset($val['NUserPasswd'])? $val['NUserPasswd']: '';
	$dat['NUserPasswd2']	= isset($val['NUserPasswd2'])? $val['NUserPasswd2']: '';
	if (!empty($dat['NUserPasswd']) && ($dat['NUserPasswd'] == $dat['NUserPasswd2']))
		$dat['UserPasswd'] 		= $dat['NUserPasswd'];
	else if (isset($val['UserPasswd']) && !empty($val['UserPasswd']))
		$dat['UserPasswd'] 		= $val['UserPasswd'];
	else if (isset($ifo[1]) && !empty($ifo[1]))
		$dat['UserPasswd'] 		= $ifo[1];
	else
		$dat['UserPasswd'] 		= 'di8welcome'; // default password
	return $dat;
}

$cmd = getParameter('cmd','');
switch ($cmd) {
	case "login":
		// LOGIN: CONTROL USER ACCESS
		if ($us->login($_GET['userid'], $_GET['password']) > 0) {
			echo "OK";	// Login success
		} else {
			echo "ERROR";
		}
	break;
	case "relogin":
		// RELOGIN: Previous session exists, reconnect to the same session
		$t->assign ("user", $us->UserId);
		$t->assign ("ctl_logged", true);    // Success: User is logged
		$t->display("user.tpl");
	break;
	case "logout":
		// LOGOUT : Logut current user and show the login panel again
		$us->logout();
		echo "OK";
	break;
	case "passlost":
		// PASSLOST: Allows to recover a user's password by sending 
		// an e-mail with the login information
		if (isset($_GET['opt']) && ($_GET['opt']) == "sendnewpass") {
			if ($us->sendPasswdReminder($_GET['UserEMail']) != '') {
				$t->display('user_msgsend.tpl');
			} else {
				$t->display('user_errsend.tpl');
			}
		} else {
			$t->display('user_passwdreminder.tpl');
		}
	break;
	// WELCOME: Shows default window when user's login was sucessfull
	case "welcome":
		// Shows the list of databases available for each user.
		if ($us->UserId != '') {
			$t->assign ("ctl_welcome", true);
			$t->assign ("fullname", $us->getUserFullName());
			// Enable access only Valid Role
			$rol1 = $us->getUserRole('');
			if ($rol1 == "ADMINPORTAL")
				$t->assign ("ctl_portalperms", true);
			// Find regions where user has permissions
			//$role = $us->getUserRole('_ALL_');
			$role  = $us->getUserRoleList();
			$radm = array();
			$robs = array();
			$rusr = array();
			$rsup = array();
			$hrole = false;
			foreach ($role as $k=>$v) {
				$sRegionId    = $k;
				$sRole        = $v['Role'];
				$sRegionLabel = $v['RegionLabel'];
				$hrole = true;
				switch ($sRole) {
					case "ADMINREGION":		$radm[$sRegionId] = $sRegionLabel;
					break;
					case "USER":			$rusr[$sRegionId] = $sRegionLabel;
					break;
					case "SUPERVISOR":		$rsup[$sRegionId] = $sRegionLabel;
					break;
					case "OBSERVER":		$obs[$sRegionId] = $sRegionLabel;
					break;
					default:				$hrole = false;
					break;
				}
			} // foreach
			if ($hrole)
				$t->assign ("ctl_showreg", true);
			$t->assign ("radm", $radm);//empty($radm) ? false : $radm);
			$t->assign ("rusr", $rusr);//empty($rusr) ? false : $rusr);
			$t->assign ("rsup", $rsup);//empty($rsup) ? false : $rsup);
			$t->assign ("robs", $robs);//empty($robs) ? false : $robs);
			$t->display("user.tpl");
		} else {
			// Error logging user, send to password lost form
			$t->display('user_passwdreminder.tpl');
		}
	break; // end WELCOME
	case 'getUserInfo':
		$user = null;
		$UserId = getParameter('UserId', '');
		if ($UserId != '') {
			$user = new DIUser($us, $UserId);
			print json_encode($user->oField['info']);
		}
	break;
	case "adminusr":
		// USERADMIN: Register new user form, only for AdminPortal
		$t->assign ("cnt", $us->q->getCountryList());
		$t->assign ("ctl_adminusr", true);
		$t->assign ("usrpa", $us->getUserInfo(''));
		$t->assign ("ctl_usrlist", true);
		$t->display("useradmin.tpl");
	break;
	case "viewpref":
		// PREFERENCES: View User Account Options
		$t->assign ("ctl_viewpref", true);
		$t->assign ("usri", form2user($us->getUserInfo($us->UserId)));
		$t->display("user.tpl");
	break;
	case "chklogin":
		// USERADMIN: check if UserId exists...
		$t->assign ("ctl_chklogin", true);
		if ($us->existUser($_GET['userid'])) {
			$t->assign ("clogin", true);
		}
		$t->display("user.tpl");
	break;
	case "chkpasswd":
		// Check if password is correct (ask to dicore). if is OK show dialog to change it.
		if (!iserror($us->validateUser($us->UserId, $_GET['UserPasswd']))) {
			$t->assign ("ctl_chkpasswd", true);
			$t->assign ("usri", form2user($us->getUserInfo($us->UserId)));
		} else {
			$t->assign ("ctl_msgupdate", true);
			$t->assign ("errbadpass", true);
		}
		$t->display("user.tpl");
	break;
	case 'insert':
		$bReturn = ERR_NO_ERROR;
		// This function is valid only for ADMINPORTAL User (root)
		$Role = $us->getUserRole('');
		if ($Role != 'ADMINPORTAL') {
			$bReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($bReturn) {
			$data = $_POST['User'];
			$UserId = $data['UserId'];
		}
		
		if ($UserId == '') {
			$bReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($bReturn) {
			$u = new DIUser($us, $UserId);
			// set a Default passwd for new users...
			$data['UserPasswd'] = md5('di8welcome');
			$u->setFromArray($data);
			$bReturn = $u->insert();
		}
		header('Content-type: text/json');
		if ($bReturn > 0) {
			$oReturn = array('Status'  => $bReturn, 
			                 'Message' => 'New User Created');
		} else {
			$oReturn = array('Status'  => $bReturn,
			                 'Message' => 'Error creating user');
		}
		print json_encode($oReturn);
	break;
	// USERADMIN: update selected user..
	case 'update':
		$bReturn = ERR_NO_ERROR;
		// This function is valid only for ADMINPORTAL User (root)
		$Role = $us->getUserRole('');
		if ($Role != 'ADMINPORTAL') {
			$bReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($bReturn) {
			$data = $_POST['User'];
			$UserId = $data['UserId'];
		}
		
		if ($UserId == '') {
			$bReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($bReturn) {
			$u = new DIUser($us, $UserId);
			// Do not change passwd here !!
			unset($data['UserPasswd']);
			$u->setFromArray($data);
			$bReturn = $u->update();
		}
		header('Content-type: text/json');
		if ($bReturn > 0) {
			$oReturn = array('Status'  => $bReturn, 
			                 'Message' => 'User information updated');
		} else {
			$oReturn = array('Status'  => $bReturn,
			                 'Message' => 'Error updating user information');
		}
		print json_encode($oReturn);
	break;
	case "list":
		// USERADMIN: reload list..
		$t->assign ("usrpa", $us->getUserInfo(''));
		$t->assign ("ctl_usrlist", true);
		$t->display("userlist.tpl");
	break;
	default:
		// View login window
		if (checkAnonSess() || $us->UserId == '') {
			$t->display('user_login.tpl');
		} else {
			$t->assign("ctl_logged", true);
			$t->assign("user", $us->UserId);
			$t->display("user.tpl");
		}
	break;
} // end share session

</script>