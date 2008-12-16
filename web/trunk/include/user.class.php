<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

/* Class User manage remote users functions.
 */ 

class User 
{
  var $status = 0;

  // User : start a anonymous or user define session
  function User($cmd, $user, $pass) {
    if ($cmd == "init") {
      // check if exist anonymous session
      $rpcarg = array("", "");
      $res = callRpcDICore('RpcUserOperations.openUserSession', $rpcarg);
      if (!iserror($res)) {
        $_SESSION['username'] = "";
        $_SESSION['sessionid'] = $res;
        $this->status = ERR_NO_ERROR;
      }
      else {
        $_SESSION['sessionid'] = "";
        $this->status = ERR_INVALID_PASSWD;
      }
    }
    if ($cmd == "login") {
      if (empty($user) || empty($pass))
        $this->status = ERR_ACCESS_DENIED;
      else {
        // Get anonymous or user defined session
        $rpcarg = array($user, $pass);
        $res = callRpcDICore('RpcUserOperations.openUserSession', $rpcarg);
        if (!iserror($res)) {
          // invalid authentication
          if ($res == "") 
            $this->status = ERR_INVALID_PASSWD;
          // close anonymous session; welcome user; 
          else {
            $rpcarg = array($_SESSION['sessionid']);
            callRpcDICore('RpcUserOperations.closeUserSession', $rpcarg);
            $_SESSION['username'] = $user;
            $_SESSION['sessionid'] = $res;
            $this->status = ERR_NO_ERROR;
          }
        }
        // error in connection to dicore
        else 
          $this->status = $res;
      }
    }
    if ($cmd == "logout") {
      $this->logout();
    }
  }
  
  /* logout: finish a user define session. begin u->init()
   */
  function logout () {
    if (checkUserSess()) {
      $rpcarg = array($_SESSION['sessionid']);
      return callRpcDICore('RpcUserOperations.closeUserSession', $rpcarg);
    }
    else
      return ERR_ACCESS_DENIED;
  }
  
  /* end   : finish anonymouse session. Destroy session
   */
  function finnal () {
    $rpcarg = array($_SESSION['sessionid']);
    $res = callRpcDICore('RpcUserOperations.closeUserSession', $rpcarg);
    if (!iserror($res))
      session_destroy();
  }
  
  /* Password Lost : show page to recover username/password of user define */
  function userpasslost ($user)  {
    $rpcarg = array($user);
    $res = callRpcDICore('RpcUserOperations.rememberUserPasswd', $rpcarg);
    return $res;
  }

  // Awake connection of users..
  function awakeUserSession() {
    if (checkUserSess()) {  
      $rpcarg = array($_SESSION['sessionid']);
      $res = callRpcDICore('RpcUserOperations.awakeUserSession', $rpcarg);
      return $res;
    }
    return ERR_ACCESS_DENIED;
  }

  /* FUNCTIONS TO MANAGE PERMISSIONS AND ROLES */
  // Check User Permissions.. 
  function getAllPermbyUser() {
    if (checkUserSess()) {  
      $rpcarg = array($_SESSION['sessionid']);
      $res = callRpcDICore('RpcUserOperations.getAllPermsByUser', $rpcarg);
      return $res;
    }
    return ERR_ACCESS_DENIED;
  }

  // Check user role according with DICORE and recheck with local username..
  function getUserRole($ruuid) {
    if (strlen($ruuid) > 0) {
      // Return hash with region=>role for all regions
      if ($ruuid == "_ALL_") {
        $rol = array();
        $q = new Query('');
        $reg = $q->getRegionList('', '');
        if (!empty($reg))
          foreach ($reg as $rid=>$rgn) {
            $res = $this->getUserRoleByRegion($rid, $_SESSION['username']);
            if (!iserror($res))
              $rol[$rid] = $res;
          }
      }
      else {
        $rol = ERR_CONSTRAINT_FAIL;
        if (checkUserSess()) {
          $res = $this->getUserRoleByRegion($ruuid, $_SESSION['username']);
          if (is_array($res) && isset($res[$_SESSION['username']]))
            $rol = $res[$_SESSION['username']];
          else
            $rol = $res; // is error..
        }
      }
    }
    // No region, check adminportal role
    else {
      $rol = ERR_CONSTRAINT_FAIL;
      if (checkUserSess()) {
        foreach ($this->getAllPermbyUser() as $k=>$v)
          if ($k == "REGION/")
            $rol = "ADMINPORTAL";
      }
    }
    return $rol;
  }

  // Return hash user=>role
  function getUserRoleByRegion($ruuid, $user) {
    if (checkUserSess()) {
      $rpcarg = array($_SESSION['sessionid'], $ruuid);
      $res = callRpcDICore('RpcUserOperations.getUserRoleByRegion', $rpcarg);
      if (!iserror($res) && !empty($user))
        if (isset($res[$user]))
          return $res[$user];
      return $res;
    }
    return ERR_CONSTRAINT_FAIL;
  }

  function setUserRole($rua, $rid, $role) {
    // Only ONE ADMINREGION by REGION..
    if ($role == "ADMINREGION") {
      $urol = $this->getUserRoleByRegion($rid, '');
      foreach ($urol as $k=>$v)
        if ($v == $role)
          $this->setUserRole($k, $rid, "NONE");
    }
    $rpcargs = array($_SESSION['sessionid'], $rua, $rid, $role);
    $rrol = callRpcDICore('RpcUserOperations.setUserRole', $rpcargs);
    return $rrol;
  }

  /** FUNCTIONS TO MANAGE USERS **/
  function updateUser ($user, $name, $email, $pass, $cnt, $cty, $active) {
    $data['UserName'] = $user;
    $data['UserEMail'] = $email;
    $data['UserPasswd'] = $pass;
    $data['UserFullName'] = $name;
    $data['UserCountry'] = $cnt;
    $data['UserCity'] = $cty;
    $data['UserActive'] = $active;
    $rpcargs = array($_SESSION['sessionid'], DI_USER, CMD_UPDATE, $data);
    $usr = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    return $usr;
  }
  
  function insertUser ($user, $name, $email, $pass, $cnt, $cty, $active) {
    $data['UserName'] = $user;
    $data['UserEMail'] = $email;
    $data['UserPasswd'] = $pass;
    $data['UserFullName'] = $name;
    $data['UserLangCode'] = 'es';
    $data['UserCountry'] = $cnt;
    $data['UserCity'] = $cty;
    $data['UserCreationDate'] = date("Y-m-d H:i:s");
    $data['UserActive'] = $active;
    $rpcargs = array($_SESSION['sessionid'], DI_USER, CMD_NEW, $data);
    $usr = callRpcDICore('RpcDIServer.saveDIObject', $rpcargs);
    // set role to user. This role let to user change his own basic info
    if (!iserror($usr))
      $this->setUserRole($data['UserName'], '', 'MINIMAL');
    return $usr;
  }
  
  /** USERINFO: functions to get info about user **/
  //Get user fullname info
  function getUsername ($username) {
    $rpcargs = array($_SESSION['sessionid'], $username);
    $usr = callRpcDICore('RpcUserOperations.getUsersList', $rpcargs);
    return $usr;
  }

  // Get basic user info: user=>[email,pass,name,lang,country,city,creadate,iplist,notes,active]
  function getUserInfo ($username) {
    $rpcargs = array($_SESSION['sessionid'], $username);
    // uhmm, need change a new function in dicore..
    $usr = callRpcDICore('RpcUserOperations.getUsersInfo', $rpcargs);
    return $usr;
  }
  
  // Check if login is available..
  function chkLogin ($username) {
    $name = $this->getUsername($username);
    if (empty($name))
		  return true;
    return false;
  }
  
  // Check if password is correct.. uhmm need validate with dicore..
  function chkPasswd ($passwd) {
    if (strlen($passwd) > 0) {
      $rpcargs = array($_SESSION['sessionid'], $passwd);
      $usr = callRpcDICore('RpcUserOperations.validateUserPasswd', $rpcargs);
      if (!iserror($usr))
        return true;
    }
    return false;
  }
  
  function findUserPass($user) {
    $val = array();
    // Only check email..
    foreach ($this->getUserInfo('') as $k=>$i) {
      if ($i[0] == $user) {
        $val[0] = $k;
        $val[1] = $i[1];
      }
    }
    return $val;
  }
  
} // end class

</script>
