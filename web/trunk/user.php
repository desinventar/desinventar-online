<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

require_once('include/loader.php');
require_once('include/user.class.php');
require_once('include/query.class.php');
//require_once('include/region.class.php');
$t->config_dir = 'include';

// Get Regions Information
$q = new Query('');

function form2user($val) {
  $dat = array();
  // set values using Dicore: RpcUserOperations: getUsersInfo() function
  if (!isset($val['UserName']))
   $ifo = current($val); 
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
  // LOGIN: CONTROL USER ACCESS
  if ($_GET['cmd'] == "login") {
    $u = new User('login', $_GET['username'], $_GET['password']);
    if (!iserror($u->status)) {
      $t->assign ("user", $_SESSION['username']);
      $t->assign ("ctl_logged", true);		// Sucess: show logged!
    }
    else {
      $t->assign ("ctl_invalid", true);		// Fail in login process..
      $t->assign ("ctl_login", true);
    }
  }
  // RELOGIN: SESSION EXISTS. CONNECT TO EXISTS SESSION..
  else if ($_GET['cmd'] == "relogin") {
    $t->assign ("user", $_SESSION['username']);
    $t->assign ("ctl_logged", true);    // Sucess: show logged!
  }
  // LOGOUT: Close auth session and open one new session
  else if ($_GET['cmd'] == "logout") {
    $u = new User('logout', '', '');
    $u = new User('init', '', '');
    $t->assign ("ctl_login", true);
  }
  // Open default connection to share session:
  else {
   $u = new User('', '', '');	// Get session info..
   switch ($_GET['cmd']) {
    // PASSLOST: Recover password sending reminder..
    case "passlost":
      if (isset($_GET['opt']) && ($_GET['opt']) == "sendnewpass") {
        // compare with all usernames and useremail. If match, get password
        if (strlen($_GET['UserNameEmail']) > 0) {
          $myuser = $u->findUserPass($_GET['UserNameEmail']);
          //$msg = "Saludos,\n\n Su nueva clave es: XXXXX" ;
          // sendMail($to, $msg);
          if (!empty($myuser)) {
            $t->assign ("rempass", $myuser);
            $t->assign ("ctl_msgsend", true);
          }
          else
            $t->assign ("ctl_errsend", true);
        }
        else
          $t->assign ("ctl_errsend", true);
      }
      else
        $t->assign ("ctl_passlost", true);
    break;
    // WELCOME: Default window when user was connect sucessfull
    case "welcome":
      // User login check. Show Welcome Page 
      if (checkUserSess()) {
        $t->assign ("ctl_welcome", true);
        $fulln = current($u->getUsername($_SESSION['username']));
        $t->assign ("fullname", $fulln);
        // Enable access only Valide Role
        $rol1 = $u->getUserRole('');
        if ($rol1 == "ADMINPORTAL")
          $t->assign ("ctl_portalperms", true);
        // Find regions where user has permissions
        $role = $u->getUserRole('_ALL_');
        $radm = array();
        $robs = array();
        $rusr = array();
        $rsup = array();
        $hrole = false;
        foreach ($role as $k=>$v) {
          $res = $q->getRegionFieldByID($k, "RegionLabel");
          if (!empty($res)) {
            $hrole = true;
            if ($v=="ADMINREGION") {
              $radm = array_merge ($radm, $res);
            }
            else if ($v=="USER")
              $rusr = array_merge ($rusr, $res);
            else if ($v=="SUPERVISOR")
              $rsup = array_merge ($rsup, $res);
            else if ($v=="OBSERVER")
              $robs = array_merge ($robs, $res);
            else
              $hrole = false;
          }
          else
            echo showerror($res);
        }
        if ($hrole)
          $t->assign ("ctl_showreg", true);
        $t->assign ("radm", $radm);//empty($radm) ? false : $radm);
        $t->assign ("rusr", $rusr);//empty($rusr) ? false : $rusr);
        $t->assign ("rsup", $rsup);//empty($rsup) ? false : $rsup);
        $t->assign ("robs", $robs);//empty($robs) ? false : $robs);
      }
      // Error logging user, send to password lost form
      else
        $t->assign ("ctl_passlost", true);
    break; // end WELCOME
    // USERADMIN: Register new user form, only for AdminPortal
    case "adminusr":
      $t->assign ("cnt", $q->getCountryList());
      $t->assign ("ctl_adminusr", true);
      $t->assign ("usrpa", $u->getUserInfo(''));
      $t->assign ("ctl_usrlist", true);
    break;
    // PREFERENCES: View User Account Options
    case "viewpref":
      if (checkUserSess()) {
        $t->assign ("ctl_viewpref", true);
        $usri = form2user($u->getUserInfo($_SESSION['username']));
        $t->assign ("usri", $usri);
      }
      else
        $t->assign ("ctl_passlost", true);
    break;
    // USERADMIN: check if username exists...
    case "chklogin":
      $t->assign ("ctl_chklogin", true);
      if ($u->chkLogin($_GET['UserName']))
        $t->assign ("clogin", true);
    break;
    case "chkpasswd":
      // Check if password is correct (ask to dicore). if is OK show dialog to change it.
      if ($u->chkPasswd($_GET['UserPasswd'])) {
        $t->assign ("ctl_chkpasswd", true);
        $usri = form2user($u->getUserInfo($_SESSION['username']));
        $t->assign ("usri", $usri);
      }
      else {
        $t->assign ("ctl_msgupdate", true);
        $t->assign ("errbadpass", true);
      }
    break;
    // USERADMIN: insert new user
    case "insert":
      $data = form2user($_GET);
      $t->assign ("ctl_msginsert", true);
      $t->assign ("username", $data['UserName']);
      if ($u->chkLogin($data['UserName']))
        // Create user if login not exists
        $ret = $u->insertUser($data['UserName'], $data['UserFullName'], $data['UserEMail'], 
              $data['UserPasswd'], $data['UserCountry'], $data['UserCity'], $data['UserActive']);
      else
        $ret = ERR_OBJECT_EXISTS;
      $t->assign ("insstat", $ret);
      if (!iserror($ret))
        $t->assign ("noerrorins", true);
      else
        $t->assign ("errinsuser", true);
    break;
    // USERADIN: update selected user..
    case "update":
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
          if (!iserror($ret))
            $t->assign ("noerrorupd", true);
          else
            $t->assign ("errupduser", true);
        }
        else
          $t->assign ("errnomatch", true);
      }
      else
        $t->assign ("errbadpass", true);
    break;
    // USERADMIN: reload list..
    case "list":
      $t->assign ("usrpa", $u->getUserInfo(''));
      $t->assign ("ctl_usrlist", true);
    break;

    // DEFAULT: View login window
    default:
      if (checkAnonSess())
        $t->assign ("ctl_login", true);
    break;
   } // end switch
  } // end share session
}

$t->display ("user.tpl");

</script>

