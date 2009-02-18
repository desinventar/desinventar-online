<script language="php">
/*
 **********************************************
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
 **********************************************
*/

require_once('include/loader.php');
 //require_once('include/usersession.class.php');
 //require_once('include/query.class.php');

$t->config_dir = 'include';

function form2user($val) {
  $dat = array();
  if (!isset($val['UserName'])) {
  	$ifo = current($val); 
  }
  $dat['UserName'] 			= isset($val['UserName'])? $val['UserName']: key($val);
  $dat['UserFullName'] 	= isset($val['UserFullName'])? $val['UserFullName']: $ifo[2];
  $dat['UserEMail'] 		= isset($val['UserEMail'])? $val['UserEMail']: $ifo[0];
  $dat['UserCountry'] 	= isset($val['UserCountry'])? $val['UserCountry']: $ifo[4];
  $dat['UserCity'] 			= isset($val['UserCity'])? $val['UserCity']: $ifo[5];
  if (isset($val['UserActive'])) {
    if (($val['UserActive'] == "on") || $val['UserActive'])
      $dat['UserActive'] = true;
    else
      $dat['UserActive'] = false;
  }
  else
    $dat['UserActive'] = $ifo[9];
  $dat['NUserPasswd'] 	= isset($val['NUserPasswd'])? $val['NUserPasswd']: '';
  $dat['NUserPasswd2'] 	= isset($val['NUserPasswd2'])? $val['NUserPasswd2']: '';
  if (!empty($dat['NUserPasswd']) && ($dat['NUserPasswd'] == $dat['NUserPasswd2'])) 
    $dat['UserPasswd'] = $dat['NUserPasswd'];
  else if (isset($val['UserPasswd']) && !empty($val['UserPasswd']))
    $dat['UserPasswd'] = $val['UserPasswd'];
  else if (isset($ifo[1]) && !empty($ifo[1]))
    $dat['UserPasswd'] = $ifo[1];
  else
    $dat['UserPasswd'] = 'di8welcome';
  return $dat;
}

if (isset($_GET['cmd'])) {
	if ($_GET['cmd'] == "") {
		if ($us->sUserName == "") {
			$t->assign("ctl_login", true);
		} else {
			$t->assign("ctl_logged", true);
			$t->assign("user", $us->sUserName);
		}
	} //if
	                                                                
	// LOGIN: CONTROL USER ACCESS
	if ($_GET['cmd'] == "login") {
		if ($us->login($_GET['username'], $_GET['password'])) {
			$t->assign ("user", $us->sUserName);
			$t->assign ("ctl_logged", true);		// Login Sucess !!
		} else {
			$t->assign ("ctl_invalid", true);		// Login failed
			$t->assign ("ctl_login", true);
		} // if
	} elseif ($_GET['cmd'] == "relogin") {
		// RELOGIN: Previous session exists, reconnect to the same session
		$t->assign ("user", $us->sUserName);
		$t->assign ("ctl_logged", true);    // Success: User is logged
	} elseif ($_GET['cmd'] == "logout") {
		// LOGOUT : Logut current user and show the login panel again
		$us->logout();
		$t->assign ("ctl_login", true);
	} else {
		// Default Case, Show Session Information
		switch ($_GET['cmd']) {
		case "passlost":
			// PASSLOST: Allows to recover a user's password by sending 
			// an e-amil with the login information
			if (isset($_GET['opt']) && ($_GET['opt']) == "sendnewpass") {
				if ($us->sendPasswdReminder($_GET['UserNameEMail']) != '') {
					$t->assign ("ctl_msgsend", true);
				} else {
					$t->assign ("ctl_errsend", true);
				}
			} else {
				$t->assign ("ctl_passlost", true);
			}
			break;
		case "welcome":
			// WELCOME: Shows default window when user's login was sucessfull
			// Shows the list of databases available for each user.
			if ($us->sUserName != '') {
				$t->assign ("ctl_welcome", true);
				$t->assign ("fullname", $us->getUserFullName());
				// Enable access only Valid Role
				$rol1 = $us->getUserRole('');
				if ($rol1 == "ADMINPORTAL") {
					$t->assign ("ctl_portalperms", true);
				}
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
					if ($sRole == "ADMINREGION") {
						$radm[$sRegionId] = $sRegionLabel;
					} else if ($sRole == "USER") {
						$rusr[$sRegionId] = $sRegionLabel;
					} else if ($sRole == "SUPERVISOR") {
						$rsup[$sRegionId] = $sRegionLabel;
					} else if ($sRole == "OBSERVER") {
						$obs[$sRegionId] = $sRegionLabel;
					} else {
						$hrole = false;
					}
				} // foreach
				if ($hrole) {
					$t->assign ("ctl_showreg", true);
				}
				$t->assign ("radm", $radm);//empty($radm) ? false : $radm);
				$t->assign ("rusr", $rusr);//empty($rusr) ? false : $rusr);
				$t->assign ("rsup", $rsup);//empty($rsup) ? false : $rsup);
				$t->assign ("robs", $robs);//empty($robs) ? false : $robs);
			} else {
				// Error logging user, send to password lost form
				$t->assign ("ctl_passlost", true);
			}
			break; // end WELCOME
		case "adminusr":
			// Get Regions Information
			$q = new Query();
			// USERADMIN: Register new user form, only for AdminPortal
			$t->assign ("cnt", $q->getCountryList());
			$t->assign ("ctl_adminusr", true);
			$t->assign ("usrpa", $us->getUserInfo(''));
			$t->assign ("ctl_usrlist", true);
			break;
		case "viewpref":
			// PREFERENCES: View User Account Options
			if (checkUserSess()) {
				$t->assign ("ctl_viewpref", true);
				$usri = form2user($us->getUserInfo($us->sUserName));
				$t->assign ("usri", $usri);
			} else {
				$t->assign ("ctl_passlost", true);
			}
			break;
		case "chklogin":
			// USERADMIN: check if username exists...
			$t->assign ("ctl_chklogin", true);
			if ($u->chkLogin($_GET['UserName'])) {
				$t->assign ("clogin", true);
			}
			break;
		case "chkpasswd":
			// Check if password is correct (ask to dicore). if is OK show dialog to change it.
			if ($u->chkPasswd($_GET['UserPasswd'])) {
				$t->assign ("ctl_chkpasswd", true);
				$usri = form2user($us->getUserInfo($us->sUserName));
				$t->assign ("usri", $usri);
			} else {
				$t->assign ("ctl_msgupdate", true);
				$t->assign ("errbadpass", true);
			}
			break;
		case "insert":
			// USERADMIN: insert new user
			$data = form2user($_GET);
			$t->assign ("ctl_msginsert", true);
			$t->assign ("username", $data['UserName']);
			if ($u->chkLogin($data['UserName'])) {
				// Create user if login not exists
				$ret = $u->insertUser($data['UserName'], $data['UserFullName'], $data['UserEMail'], 
				      $data['UserPasswd'], $data['UserCountry'], $data['UserCity'], $data['UserActive']);
			} else {
				$ret = ERR_OBJECT_EXISTS;
			}
			$t->assign ("insstat", $ret);
			if (!iserror($ret)) {
				$t->assign ("noerrorins", true);
			} else {
				$t->assign ("errinsuser", true);
			}
			break;
		case "update":
			// USERADIN: update selected user..
			$data = form2user($_GET);
			$t->assign ("ctl_msgupdate", true);
			$t->assign ("username", $data['UserName']);
			// check passwd first or adminportal admited
			$rol1 = $u->getUserRole('');
			if ((isset($_GET['UserPasswd']) && $u->chkPasswd($_GET['UserPasswd'])) || ($rol1 == "ADMINPORTAL")) {
				// if password match, please update..
				if ($data['NUserPasswd'] == $data['NUserPasswd2']) {
					$ret = $u->updateUser($data['UserName'], $data['UserFullName'], $data['UserEMail'], 
					 $data['UserPasswd'], $data['UserCountry'], $data['UserCity'], $data['UserActive']);
					$t->assign ("updstat", $ret);
					if (!iserror($ret)) {
						$t->assign ("noerrorupd", true);
					} else {
						$t->assign ("errupduser", true);
					}
				} else {
					$t->assign ("errnomatch", true);
				}
			} else {
				$t->assign ("errbadpass", true);
			}
			break;
		case "list":
			// USERADMIN: reload list..
			$t->assign ("usrpa", $us->getUserInfo(''));
			$t->assign ("ctl_usrlist", true);
			break;
		default:
			// DEFAULT: View login window
			if (checkAnonSess()) {
				$t->assign ("ctl_login", true);
			}
			break;
		} // switch
	} // end share session
}

// 2009-01-19 (jhcaiced) This should keep the UserSession info between pages
$_SESSION['sessioninfo'] = $us;

$t->display ("user.tpl");

</script>

