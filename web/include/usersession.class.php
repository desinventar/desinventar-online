<?php
/*
  DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
namespace DesInventar\Legacy;

use DesInventar\Legacy\Query;
use DesInventar\Database\Session;
use DesInventar\Legacy\DIRegion;
use \PDO;

define('ROLE_NONE', 0);
define('ROLE_OBSERVER', 1);
define('ROLE_USER', 2);
define('ROLE_SUPERVISOR', 3);
define('ROLE_ADMINREGION', 4);
define('ROLE_ADMINPORTAL', 5);


class UserSession
{
    const PASSWORD_IS_HASHED = true;
    const PASSWORD_IS_CLEAR = false;

    private $session = null;

    public function __construct($sSessionId = null, $config = array())
    {
        if (!empty($sSessionId)) {
            $this->sSessionId = $sSessionId;
        } else {
            $this->sSessionId = session_id();
        }
        $this->UserId            = '';
        $this->LangIsoCode       = '';
        $this->RegionId          = 'core';
        $this->RegionLangIsoCode = 'eng';
        $this->dStart            = gmdate('c');
        $this->dLastUpdate       = $this->dStart;
        $this->UserRole          = '';
        $this->UserRoleValue     = ROLE_NONE;
        $this->config = $config;
        $this->q = new Query(null, $config->database);
        $this->session = new Session($this->q->core);
        if (!empty($this->q->core)) {
            $this->load($this->sSessionId);
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function isConnected()
    {
        return (! empty($this->q->core));
    }

    // Read Session Information from Database
    public function load($prmSessionId)
    {
        if (empty($this->q->core)) {
            return ERR_UNKNOWN_ERROR;
        }
        $row = null;
        try {
            $row = $this->session->read($prmSessionId);
            if (empty($row)) {
            } else {
                $this->sSessionId  = $row['id'];
                $this->UserId      = $row['user_id'];
                $this->LangIsoCode = $row['iso_code'];
                $this->dStart      = $row['created'];
                $this->dLastUpdate = $row['modified'];
            }
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
            return ERR_UNKNOWN_ERROR;
        }
        if (empty($row)) {
            $this->insert();
        }
        return ERR_NO_ERROR;
    }

    // Set LastUpdate field of Session so it will not expire...
    public function awake()
    {
        if (! $this->isConnected()) {
            return false;
        }

        $iReturn = ERR_NO_ERROR;
        $PrevTime = $this->dLastUpdate;
        $CurTime  = gmdate('c');
        $this->dLastUpdate = $CurTime;

        // Logout users when too much time has passed without using this session
        $Interval = strtotime($CurTime) - strtotime($PrevTime);
        // 60 * 30 = 1800 (30 minutes)
        if ($Interval > 1800) {
            $this->close();
            $this->logout();
        }
        try {
            $this->session->awake($this->sSessionId, $this->dLastUpdate);
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $iReturn;
    }

    public function login($prmUserId, $prmUserPasswd, $withCrypt)
    {
        $userId = $this->validateUser($prmUserId, $prmUserPasswd, $withCrypt);
        if (empty($userId)) {
            return ERR_UNKNOWN_ERROR;
        }
        return $this->setUser($userId);
    }

    public function logout()
    {
        return $this->setUser('');
    }

    public function setUser($prmUserId)
    {
        try {
            return $this->session->setUser($this->sSessionId, $prmUserId);
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
        }
    }

    public function doUserAutoLoginCheck()
    {
        $iReturn = ERR_DEFAULT_ERROR;
        if ($this->UserId == '') {
            $clientAddr = trim($_SERVER['REMOTE_ADDR']);
            $addrList = '127.0.0.1';
            $addrList2 = '';
            if (isset($_SERVER['DESINVENTAR_AUTOLOGIN_ADDRLIST'])) {
                $addrList2 = $_SERVER['DESINVENTAR_AUTOLOGIN_ADDRLIST'];
            }
            $addrList2 = preg_replace('/ +/', ',', $addrList2);
            $addrList2 = preg_replace('/,+/', ',', $addrList2);
            $addrList = explode(',', $addrList . ',' . $addrList2);
            foreach ($addrList as $key => $addr) {
                if ($addr == '') {
                    unset($addrList[$key]);
                }
            }//foreach
            $bAutoLogin = in_array($clientAddr, $addrList);
            if ($bAutoLogin) {
                $iReturn = ERR_NO_ERROR;
            }
        }
        return $iReturn;
    }

    public function doUserAutoLogin()
    {
        $iReturn = ERR_NO_ERROR;
        $UserId = 'root';
        $this->setUser($UserId);
        $this->config->flags['auto_login'] = 1;
        return $iReturn;
    }

    public function changeLanguage($langIsoCode)
    {
        return $this->session->changeLanguage($this->sSessionId, $langIsoCode);
    }

    protected function getSessionValues()
    {
        $this->dLastUpdate = gmdate('c');
        return array(
            'SessionId' => $this->sSessionId,
            'RegionId' => '',
            'UserId' => $this->UserId,
            'Valid' => 1,
            'LangIsoCode' => $this->LangIsoCode,
            'Start' => $this->dStart,
            'LastUpdate' => $this->dLastUpdate
        );
    }

    // Start a Session by creating a record in the database
    // this could be an anonymous or authenticated session
    public function insert()
    {
        try {
            $this->session->create($this->getSessionValues());
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
            return ERR_DEFAULT_ERROR;
        }
        return ERR_NO_ERROR;
    }

    // Update information about this session in database
    public function update()
    {
        if (! $this->isConnected()) {
            return false;
        }
        try {
            $this->session->update($this->sSessionId, $this->getSessionValues());
            $this->awake();
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
            return ERR_UNKNOWN_ERROR;
        }
        return ERR_NO_ERROR;
    }

    public function delete()
    {
        try {
            $this->session->delete($this->sSessionId);
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
            return ERR_UNKNOWN_ERROR;
        }
        return ERR_NO_ERROR;
    }

    public function getRegionDir($prmRegionId)
    {
        $DBDir = VAR_DIR . '/database/' . $prmRegionId;
        return $DBDir;
    }

    // Associate a RegionId with the session
    public function open($prmRegionId, $prmDBFile = '')
    {
        $iReturn = ERR_NO_ERROR;
        $this->RegionId = '';
        $this->UserRole = '';
        $this->UserRoleValue = ROLE_NONE;

        $DBDir = VAR_DIR . '/database/' . $prmRegionId;
        if ($prmDBFile == '') {
            $DBFile = $DBDir . '/desinventar.db';
        } else {
            $DBFile = $DBDir . '/' . $prmDBFile;
        }
        if (! file_exists($DBFile)) {
            $iReturn = ERR_NO_DATABASE;
        }
        if ($iReturn > 0) {
            $this->awake();
            $this->q->setDBConnection($prmRegionId, $DBFile);
            $this->conn = $this->q->dreg;
            $this->RegionId = $prmRegionId;
            $this->UserRole = $this->getUserRole($prmRegionId);
            $this->UserRoleValue = $this->getUserRoleValue($prmRegionId);
            $this->RegionLangIsoCode = $this->getDBInfoValue('LangIsoCode');
        }
        return $iReturn;
    }

    public function close($prmRegionId = '')
    {
        return $this->open('');
    }

    // Validate a user/passwd pair against database
    public function validateUser($prmUserId, $prmUserPasswd, $withCrypt)
    {
        if (! $withCrypt) {
            $prmUserPasswd = md5($prmUserPasswd);
        }
        try {
            $row = $this->session->login($prmUserId, $prmUserPasswd);
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
            return false;
        }
        return $row['UserId'];
    }

    public function getUserFullName()
    {
        if (! $this->isConnected()) {
            return false;
        }
        $sUserFullName = '';
        $sQuery = "SELECT * FROM User WHERE UserId='" . $this->UserId . "'";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $sUserFullName = $row['UserFullName'];
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $sUserFullName;
    }

    public function getAllPermsByUser()
    {
        return $this->getAllPermsGeneric($this->UserId, "");
    }

    private function getAllPermsGeneric($prmUserId, $prmRegionId)
    {
        $myPerms = array();
        $sQuery = "SELECT * FROM RegionAuth WHERE " .
          "((UserId='" . $prmUserId . "') OR (UserId='')) ";
        if ($prmRegionId != "") {
            $sQuery .= " AND " . "((RegionId='" . $prmRegionId . "') OR (RegionId='')) ";
        }
        $sQuery = $sQuery + " ORDER BY AuthKey,AuthValue";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            $i = 0;
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $sAuthKey   = $row['AuthKey'];
                $sAuthValue = $row['AuthValue'] . "/" . $row['AuthAuxValue'];
                $myPerms[$sAuthkey] = $sAuthValue;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $myPerms;
    }

    // Return hash with all regions where the user has a role
    protected function getUserRoleList()
    {
        $myData = array();
        $sQuery = "SELECT RegionAuth.*,Region.RegionLabel FROM RegionAuth,Region WHERE " .
            " (RegionAuth.RegionId = Region.RegionId) " .
            " AND (UserId='" . $this->UserId . "') " .
            " AND AuthKey='ROLE'" .
            " ORDER BY RegionAuth.RegionId";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $sKey   = $row['RegionId'];
                $sValue = $row['AuthAuxValue'];
                $myData[$sKey]['Role']        = $row['AuthAuxValue'];
                $myData[$sKey]['RegionLabel'] = $row['RegionLabel'];
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $myData;
    }

    // Return hash with all users of a Region with a role
    public function getRegionRoleList($prmRegionId)
    {
        $myData = array();
        $sQuery = '
			SELECT
				RegionAuth.UserId,
				RegionAuth.AuthAuxValue AS UserRole,
				User.UserFullName AS UserName,
				User.UserEMail
			FROM RegionAuth
			INNER JOIN User ON RegionAuth.UserId=User.UserId AND User.UserActive>0
			WHERE
				RegionAuth.AuthKey="ROLE" AND
				RegionAuth.AuthAuxValue != "NONE" AND
				RegionAuth.RegionId=:RegionId
			ORDER BY User.UserFullName
		';
        $sth = $this->q->core->prepare($sQuery);
        $sth->bindParam(':RegionId', $prmRegionId, PDO::PARAM_STR);
        try {
            $sth->execute();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $sKey = $row['UserId'];
                $myData[$sKey] = $row;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $myData;
    }

    public function getRegionUserAdminInfo($prmRegionId)
    {
        $UserInfo = array();
        $RoleList = $this->getRegionRoleList($prmRegionId);
        foreach ($RoleList as $UserId => $UserRole) {
            if ($UserRole['UserRole'] != 'ADMINREGION') {
                continue;
            }
            $UserFullInfo = $this->getUserInfo($UserId);
            foreach (array('UserId','UserFullName','UserEMail') as $key) {
                $UserInfo[$key] = $UserFullInfo[$UserId][$key];
            }
        }
        return $UserInfo;
    }

    // Get basic user info: user=>[email,pass,name,org,country,city,creadate,iplist,notes,active]
    public function getUserInfo($prmUserId)
    {
        $myData = array();
        $sQuery = "SELECT * FROM User";
        if ($prmUserId != '') {
            $sQuery .= " WHERE UserId='" . $prmUserId . "'";
        }
        $sQuery .= " ORDER BY UserFullName";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $sth->execute();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $myData[$row['UserId']] = array_merge(
                    array(
                        $row['UserEMail'],
                        $row['UserPasswd'],
                        $row['UserFullName'],
                        $row['Organization'],
                        $row['CountryIso'],
                        $row['UserCity'],
                        $row['UserCreationDate'],
                        $row['UserNotes'],
                        $row['UserActive']
                    ),
                    $row
                );
            }
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $myData;
    }

    // Send a Password Reminder to an E-mail
    public function sendPasswdReminder($prmEMail)
    {
        $myAnswer = '';
        $sQuery = "SELECT * FROM User WHERE (UserEMail='" . $prmEMail . "') ";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $myAnswer = $row['UserEMail'];
                $myPasswd = generatePasswd();
                $this->updateUserPasswd($row['UserId'], md5($myPasswd));
                // uhmm, must revise if send mail-> offline systems ??
                mail(
                    $myAnswer,
                    "DesInventar - Password Reminder",
                    "Dear User\nYour login information for DesInventar is:\n" .
                     "  UserId : " . $row['UserId'] . "\n" .
                     "  Passwd : " . $myPasswd . "\n" .
                     "\n\n" .
                     "Sincerely,\n" .
                     "   The DesInventar Team",
                    "From: support@desinventar.org"
                );
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showStatusMsg('ERROR sendPasswdReminder : ' . $e->getMessage());
        }
        return $myAnswer;
    }

    // Return Role for a Region
    public function getUserRole($prmRegionId = '')
    {
        $UserRole = 'NONE';
        if ($prmRegionId == '') {
            $prmRegionId = $this->RegionId;
        }
        if ($this->UserId == 'root') {
            $UserRole = 'ADMINPORTAL';
        } else {
            $UserRole = 'NONE';
            $sQuery = "SELECT * FROM RegionAuth WHERE ";
            if ($prmRegionId != '') {
                $sQuery .= "((RegionId='') OR (RegionId='" . $prmRegionId . "'))";
            } else {
                $sQuery .= "(RegionId='')";
            }
            $sQuery .= " AND (UserId='" . $this->UserId . "') " .
                       " AND AuthKey='ROLE'" .
                       " ORDER BY UserId,RegionId";
            $sth = $this->q->core->prepare($sQuery);
            try {
                $this->q->core->beginTransaction();
                $sth->execute();
                $this->q->core->commit();
                while ($row = $sth->fetch(PDO::FETCH_OBJ)) {
                    $UserRole = $row->AuthAuxValue;
                }
                $sth->closeCursor();
            } catch (Exception $e) {
                $this->q->core->rollBack();
                showErrorMsg(debug_backtrace(), $e, '');
            }
            if ($UserRole == 'NONE') {
                $RegionStatus = $this->getDBInfoValue('RegionStatus');
                $RegionPublic = $RegionStatus & 2;
                if ($RegionPublic > 0) {
                    $UserRole = 'OBSERVER';
                }
            }
        }
        return $UserRole;
    }

    protected function convertRoleToRoleValue($prmRole)
    {
        $NumRole = ROLE_NONE;
        if ($prmRole == 'NONE') {
            $NumRole = ROLE_NONE;
        }
        if ($prmRole == 'OBSERVER') {
            $NumRole = ROLE_OBSERVER;
        }
        if ($prmRole == 'USER') {
            $NumRole = ROLE_USER;
        }
        if ($prmRole == 'SUPERVISOR') {
            $NumRole = ROLE_SUPERVISOR;
        }
        if ($prmRole == 'ADMINREGION') {
            $NumRole = ROLE_ADMINREGION;
        }
        if ($prmRole == 'ADMINPORTAL') {
            $NumRole = ROLE_ADMINPORTAL;
        }
        return $NumRole;
    }

    // Get User Role as a Numeric Value, easier to compare
    public function getUserRoleValue($prmRegionId = '')
    {
        $Role = $this->getUserRole($prmRegionId);
        $NumRole = $this->convertRoleToRoleValue($Role);
        return $NumRole;
    }

    public function setUserRole($prmUserId, $prmRegionId, $prmRole)
    {
        $iReturn = ERR_NO_ERROR;
        if ($prmUserId   == '') {
            $iReturn = ERR_DEFAULT_ERROR;
        }
        if ($prmRegionId == '') {
            $iReturn = ERR_DEFAULT_ERROR;
        }
        if ($iReturn > 0) {
            // Remove All Permissions for This User on This Database
            $sQuery = "DELETE FROM RegionAuth WHERE UserId='" . $prmUserId . "' AND RegionId='" . $prmRegionId . "'";
            $sth = $this->q->core->prepare($sQuery);
            try {
                $this->q->core->beginTransaction();
                $sth->execute();
                $this->q->core->commit();
            } catch (Exception $e) {
                $this->q->core->rollBack();
                showErrorMsg(debug_backtrace(), $e, '');
            }
        }

        if ($iReturn > 0) {
            // Insert ROLE Permission First
            if ($prmRole <> 'NONE') {
                $this->setPerm($prmUserId, $prmRegionId, 'ROLE', 0, $prmRole);
            }
            switch ($prmRole) {
                case 'ADMINREGION':
                    $this->setPerm($prmUserId, $prmRegionId, "DISASTER", 5, "");
                    $this->setPerm($prmUserId, $prmRegionId, "EVENT", 5, "");
                    $this->setPerm($prmUserId, $prmRegionId, "CAUSE", 5, "");
                    $this->setPerm($prmUserId, $prmRegionId, "GEOGRAPHY", 5, "");
                    $this->setPerm($prmUserId, $prmRegionId, "GEOLEVEL", 5, "");
                    $this->setPerm($prmUserId, $prmRegionId, "EEFIELD", 5, "");
                    $this->setPerm($prmUserId, $prmRegionId, "DBINFO", 2, "");
                    $this->setPerm($prmUserId, $prmRegionId, "AUTH", 2, "");
                    $this->setPerm($prmUserId, $prmRegionId, "DBPUBLIC", 2, "");
                    $this->setPerm($prmUserId, $prmRegionId, "DBACTIVE", 2, "");
                    $this->setPerm($prmUserId, $prmRegionId, "DBLOG", 5, "");
                    break;
                case 'SUPERVISOR':
                    $this->setPerm($prmUserId, $prmRegionId, "DISASTER", 4, "STATUS=DRAFT,STATUS=READY");
                    $this->setPerm($prmUserId, $prmRegionId, "EVENT", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "CAUSE", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "GEOGRAPHY", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "GEOLEVEL", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "EEFIELD", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "DBINFO", 1, "");
                    $this->setPerm($prmUserId, $prmRegionId, "DBLOG", 3, "");
                    break;
                case 'USER':
                    $this->setPerm($prmUserId, $prmRegionId, "DISASTER", 3, "STATUS=DRAFT,STATUS=READY");
                    $this->setPerm($prmUserId, $prmRegionId, "EVENT", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "CAUSE", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "GEOGRAPHY", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "GEOLEVEL", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "EEFIELD", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "DBINFO", 1, "");
                    $this->setPerm($prmUserId, $prmRegionId, "DBLOG", 3, "");
                    break;
                case 'OBSERVER':
                    $this->setPerm($prmUserId, $prmRegionId, "DISASTER", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "EVENT", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "CAUSE", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "GEOGRAPHY", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "GEOLEVEL", 1, "STATUS=ACTIVE");
                    $this->setPerm($prmUserId, $prmRegionId, "DBINFO", 1, "");
                    $this->setPerm($prmUserId, $prmRegionId, "DBLOG", 1, "");
                    $this->setPerm($prmUserId, $prmRegionId, "EEFIELD", 1, "STATUS=ACTIVE");
                    break;
                case 'MINIMAL':
                    $this->setPerm($prmUserId, $prmRegionId, "USER", 2, "");
                    break;
                case 'NONE':
                    // Do not set any permission
                    break;
            }
        }
        return $iReturn;
    }

    public function setPerm($prmUserId, $prmRegionId, $prmAuthKey, $prmValue, $prmAuxValue)
    {
        $sQuery = "INSERT INTO RegionAuth VALUES (" .
            "'" . $prmUserId . "','" . $prmRegionId  . "'," .
            "'" . $prmAuthKey . "','" . $prmValue . "','" . $prmAuxValue . "')";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
    }

    public function getRegionList($prmCountryIsoCode, $prmStatus)
    {
        if (!empty($prmCountryIsoCode)) {
            $opt = " CountryIsoCode='$prmCountryIsoCode'";
        } else {
            $opt = " 1=1";
        }
        if ($prmStatus == "ACTIVE") {
            $opt .= " AND RegionActive = True";
        }
        $sQuery = "SELECT RegionId, RegionLabel FROM Region " .
                  " WHERE $opt ORDER BY RegionLabel";
        $myData = array();
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            while ($row = $sth->fetch(PDO::FETCH_OBJ)) {
                $sKey = $row->RegionId;
                $sValue = $row->RegionLabel;
                $myData[$sKey] = $sValue;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $myData;
    }

    public function doUserExist($prmUserId)
    {
        $Answer = ERR_UNKNOWN_ERROR;
        $Query = "SELECT UserId FROM User WHERE UserId='" . $prmUserId . "'";
        $sth = $this->q->core->prepare($Query);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $Answer = ERR_NO_ERROR;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $Answer;
    }

    public function insertUser(
        $UserId,
        $UserFullName,
        $UserEMail,
        $UserPasswd,
        $UserCountry,
        $UserCity,
        $UserActive
    ) {
        $iReturn = ERR_DEFAULT_ERROR;
        $sQuery = "INSERT INTO User VALUES (" .
                  "'" . $UserId . "'," .
                  "'" . $UserEMail . "'," .
                  "'" . md5($UserPasswd) . "'," .
                  "'" . $UserFullName . "'," .
                  "''," .
                  "'" . $UserCountry . "'," .
                  "'" . $UserCity . "'," .
                  "'" . gmdate('c') . "'," .
                  "''," .
                  $UserActive . ")";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            $iReturn = ERR_NO_ERROR;
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $iReturn;
    }

    public function updateUser(
        $UserId,
        $UserFullName,
        $UserEMail,
        $UserPasswd,
        $UserCountry,
        $UserCity,
        $UserActive
    ) {
        $iReturn = ERR_DEFAULT_ERROR;
        $sQuery = "UPDATE User SET " .
                  "UserEMail='" . $UserEMail . "',".
                  "UserFullName='" . $UserFullName . "'," .
                  "Organization=''," .
                  "CountryIso='" . $UserCountry . "'," .
                  "UserCity='" . $UserCity . "'," .
                  "UserNotes=''," .
                  "UserActive=" . $UserActive;
        if (!empty($UserPasswd)) {
            $sQuery .= ", UserPasswd='" . md5($UserPasswd) . "'";
        }
        $sQuery .=  " WHERE UserId='" . $UserId . "'";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            $iReturn = ERR_NO_ERROR;
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $iReturn;
    }

    public function updateUserPasswd($UserId, $UserPasswd)
    {
        $iReturn = ERR_DEFAULT_ERROR;
        $Query = 'UPDATE User SET UserPasswd="' . $UserPasswd . '" WHERE UserId="' . $UserId . '"';
        $sth = $this->q->core->prepare($Query);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            $iReturn = ERR_NO_ERROR;
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $iReturn;
    }

    public function clearOldLocks()
    {
        $deltime = gmdate('c', time() - 600);
        $sQuery = "DELETE FROM UserLockList WHERE LastUpdate<='" . $deltime . "'";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
    }

    public function isDatacardLocked($prmDisasterId)
    {
        $sReturn = '';
        $sQuery = 'SELECT * FROM UserLockList WHERE RecordId=:RecordId';
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute(array(':RecordId' => $prmDisasterId));
            $this->q->core->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $sReturn = $row['SessionId'];
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $sReturn;
    }

    public function lockDatacard($prmDisasterId)
    {
        // First delete old datacard locks...
        $this->clearOldLocks();
        $now = gmdate('c');
        $sQuery = 'INSERT INTO UserLockList VALUES (:SessionId, :LockType, :RecordId, :LockTime)';
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute(array(
                'SessionId' => $this->sSessionId,
                'LockType' => 'DISASTER',
                'RecordId' => $prmDisasterId,
                'LockTime' => $now
            ));
            $this->q->core->commit();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
    }

    public function releaseDatacard($prmDisasterId)
    {
        $sQuery = 'DELETE FROM UserLockList WHERE SessionId=:SessionId AND RecordId=:RecordId';
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute(array(
                'SessionId' => $this->sSessionId,
                'RecordId' => $prmDisasterId
            ));
            $this->q->core->commit();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
    }

    public function clearLocks()
    {
        $sQuery = "DELETE FROM UserLockList WHERE SessionId='" . $this->sSessionId . "'";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
    }

    public function getUserList($prmUserId)
    {
        $list = array();
        $sQuery = "SELECT * FROM User WHERE UserActive > 0 ";
        if ($prmUserId != '') {
            $sQuery .= " AND UserId='" . $prmUserId . "'";
        }
        $sQuery .= " ORDER BY UserFullName";
        $sth = $this->q->core->prepare($sQuery);
        try {
            $this->q->core->beginTransaction();
            $sth->execute();
            $this->q->core->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list[$row['UserId']]=$row['UserFullName'];
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->core->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $list;
    }

    public function chkPasswd($passwd)
    {
        return true;
    }

    public function listDB()
    {
        $RegionList = array();
        $query = "
        SELECT
           R.RegionId AS RegionId,
           R.RegionLabel AS RegionLabel,
           R.CountryIso AS CountryIso,
           R.RegionStatus AS RegionStatus,
           RA.AuthAuxValue AS Role
        FROM Region AS R, RegionAuth AS RA
        WHERE R.RegionId = RA.RegionId
        ";
        if ($this->UserId) {
            $query .= " AND RA.AuthKey = 'ROLE' AND RA.UserId = '". $this->UserId ."'";
        } else {
            $query .= " AND R.RegionStatus = 3 GROUP BY R.RegionId";
        }
        $query .= " ORDER BY R.RegionOrder DESC, R.CountryIso, R.RegionLabel";
        $result = $this->q->core->query($query);
        while ($row = $result->fetch(PDO::FETCH_OBJ)) {
            $RegionList[$row->RegionId] = array($row->RegionLabel, $row->CountryIso, $row->RegionStatus, $row->Role);
        }
        return $RegionList;
    }

    public function searchCountryList()
    {
        $sQuery = 'SELECT DISTINCT CountryIso FROM Region WHERE RegionStatus=3 AND CountryIso<>""';
        $CountryList = array();
        $Index = 0;
        $List = '';
        foreach ($this->q->core->query($sQuery) as $row) {
            if ($Index > 0) {
                $List .= ',';
            }
            $List .= '"' . $row['CountryIso'] . '"';
            $Index++;
        }
        $sQuery = 'SELECT CountryIso,CountryName FROM Country WHERE CountryIso IN (' . $List . ')';
        $Index = 0;
        foreach ($this->q->base->query($sQuery) as $row) {
            $CountryList[$Index]['CountryIso']  = $row['CountryIso'];
            $CountryList[$Index]['CountryName'] = $row['CountryName'];
            $Index++;
        }
        return $CountryList;
    }

    public function searchDB($prmQuery, $searchByCountry)
    {
        $regionlist = array();

        // Search for Public Databases and assign to (ROLE=NONE)
        $query = 'SELECT RegionId,CountryIso,RegionLabel FROM Region WHERE ';
        if ($this->UserId != 'root') {
            $query .= 'RegionStatus=3';
            $Role = 'NONE';
        } else {
            $query .= 'RegionStatus>=0';
            $Role = 'ADMINREGION';
        }

        if ($prmQuery != '') {
            if ($searchByCountry > 0) {
                $query .= ' AND (CountryIso = "' . $prmQuery . '")';
            } else {
                $query .= "(RegionId LIKE '%" . $prmQuery . "%' OR RegionLabel LIKE '%" . $prmQuery . "%')";
            }
        }
        $query .= ' ORDER BY RegionOrder DESC, CountryIso ASC,RegionLabel ASC';
        foreach ($this->q->core->query($query) as $row) {
            $regionlist[$row['RegionId']] = array('RegionLabel' => $row['RegionLabel'],
                                                  'CountryIso'  => $row['CountryIso'],
                                                  'Role' => $Role);
        }
        if ($searchByCountry <= 0) {
            // Add Regions with specific Roles
            $query = "
            SELECT
                R.RegionId,
                R.CountryIso,
                R.RegionLabel,
                RA.AuthAuxValue
            FROM Region R,RegionAuth RA
            WHERE R.RegionId=RA.RegionId AND RA.AuthKey='ROLE' AND RA.UserId='" . $this->UserId . "'
            ORDER BY R.RegionOrder, R.CountryIso, R.RegionLabel;
            ";
            foreach ($this->q->core->query($query) as $row) {
                $regionlist[$row['RegionId']] = array(
                    'RegionLabel' => $row['RegionLabel'],
                    'CountryIso'  => $row['CountryIso'],
                    'Role' => $row['AuthAuxValue'],
                    'RoleValue' => $this->convertRoleToRoleValue($row['AuthAuxValue'])
                );
            }
        }
        return $regionlist;
    }

    public function getDisasterIdFirst()
    {
        $Record = 1;
        $RecordCount = $this->getDisasterCount();
        $DisasterId = $this->getDisasterIdFromRecordNumber($Record);
        return [
            'DisasterId' => $DisasterId,
            'RecordNumber' => $Record,
            'RecordCount' => $RecordCount,
            'Status' => 'OK',
        ];
    }

    public function getDisasterIdLast()
    {
        $RecordCount = $this->getDisasterCount();
        $Record = $RecordCount;
        $DisasterId = $this->getDisasterIdFromRecordNumber($Record);
        return [
            'DisasterId' => $DisasterId,
            'RecordNumber' => $Record,
            'RecordCount' => $RecordCount,
            'Status' => 'OK'
        ];
    }

    public function getDisasterIdPrev($prmRecord)
    {
        $Record = $prmRecord;
        $RecordCount = $this->getDisasterCount();
        if ($Record > 1) {
            $Record--;
        }
        $DisasterId = $this->getDisasterIdFromRecordNumber($Record);
        return [
            'DisasterId' => $DisasterId,
            'RecordNumber' => $Record,
            'RecordCount' => $RecordCount,
            'Status' => 'OK'
        ];
    }

    public function getDisasterIdNext($prmRecord)
    {
        $Record = $prmRecord;
        $RecordCount = $this->getDisasterCount();
        if ($Record < $RecordCount) {
            $Record++;
        }
        $DisasterId = $this->getDisasterIdFromRecordNumber($Record);
        return [
            'DisasterId' => $DisasterId,
            'RecordNumber' => $Record,
            'RecordCount' => $RecordCount,
            'Status' => 'OK'
        ];
    }

    public function getDisasterIdFromSerial($prmDisasterSerial)
    {
        $RecordCount = $this->getDisasterCount();
        $DisasterId = '';
        $sQuery = 'SELECT DisasterId,DisasterSerial FROM Disaster ORDER BY DisasterSerial';
        $sth = $this->q->dreg->prepare($sQuery);
        try {
            $this->q->dreg->beginTransaction();
            $sth->execute();
            $this->q->dreg->commit();
            $reclist = $sth->fetchAll(PDO::FETCH_ASSOC);
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->q->dreg->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        $bFound = 0;
        $RecordNumber = 0;
        foreach ($reclist as $row) {
            $RecordNumber++;
            if ($row['DisasterSerial'] == $prmDisasterSerial) {
                $bFound = 1;
                $DisasterId = $row['DisasterId'];
                break;
            }
        }
        unset($reclist);
        return array('Status' => 'OK', 'DisasterId' => $DisasterId,
                     'RecordNumber' => $RecordNumber, 'RecordCount' => $RecordCount);
    }

    // Get number of datacards by status: PUBLISHED, DRAFT, ..
    public function getNumDisasterByStatus($prm_record_status)
    {
        $query = 'SELECT COUNT(DisasterId) AS counter FROM Disaster';
        if ($prm_record_status != '') {
            $query_status = '';
            foreach (explode(' ', $prm_record_status) as $status) {
                if ($query_status != '') {
                    $query_status .= ',';
                }
                $query_status .= '"' . $status . '"';
            }
            $query_status = 'RecordStatus IN (' . $query_status . ')';
            $query .= ' WHERE ' . $query_status;
        }
        $dat = $this->q->getresult($query);
        return $dat['counter'];
    }

    public function getDisasterCount()
    {
        $iCount = 0;
        $sQuery = 'SELECT COUNT(DisasterId) AS C FROM Disaster WHERE DisasterSerial<>"" AND RecordStatus<>"DELETED"';
        foreach ($this->q->dreg->query($sQuery) as $row) {
            $iCount = $row['C'];
        }
        return $iCount;
    }

    public function getDisasterIdFromRecordNumber($prmRecord)
    {
        $DisasterId = '';
        $prmRecord--;
        $sQuery = '
            SELECT DisasterId FROM Disaster
            WHERE DisasterSerial<>"" ORDER BY DisasterBeginTime,DisasterSerial LIMIT ' . $prmRecord . ',1';
        foreach ($this->q->dreg->query($sQuery) as $row) {
            $DisasterId = $row['DisasterId'];
        }
        return $DisasterId;
    }

    public function existDisasterSerial($prmDisasterSerial)
    {
        $Answer = '';
        $sQuery = "SELECT DisasterSerial FROM Disaster WHERE DisasterSerial = '". $prmDisasterSerial."'";
        foreach ($this->q->dreg->query($sQuery) as $row) {
            $Answer = $row['DisasterSerial'];
        }
        return array('Status' => 'OK', 'DisasterSerial' => $Answer);
    }

    // Return a small list with the information about levels in database
    protected function getGeoLevels()
    {
        $sQuery = "SELECT GeoLevelId, GeoLevelName FROM GeoLevel WHERE GeoLevelActive=1 ORDER BY GeoLevelId";
        $GeoLevels = array();
        foreach ($this->q->dreg->query($sQuery) as $row) {
            array_push($GeoLevels, $row);
        }
        return $GeoLevels;
    }

    // Return an array with the subcomponents of a GeographyId item...
    public function getGeographyItemsById($prmGeographyId)
    {
        $gItems = array();
        for ($iLevel = 0; $iLevel < strlen($prmGeographyId)/5; $iLevel++) {
            $gId = substr($prmGeographyId, 0, ($iLevel+1)*5);
            $sQuery = 'SELECT GeographyId, GeographyName FROM Geography WHERE GeographyId="' . $gId . '"';
            foreach ($this->q->dreg->query($sQuery) as $row) {
                $gItems[$iLevel] = $row;
            }
        }
        return $gItems;
    }

    public function getGeographyItemsByLevel($prmGeographyLevel, $prmGeographyParentId)
    {
        $gItems = array();
        $sQuery = '
            SELECT GeographyId,GeographyName
            FROM Geography
            WHERE GeographyActive>0 AND GeographyLevel=' . $prmGeographyLevel;
        if ($prmGeographyLevel > 0) {
            $sQuery .= ' AND SUBSTR(GeographyId,1,' . ($prmGeographyLevel*5) . ')="' . $prmGeographyParentId . '"';
        }
        $sQuery .= ' ORDER BY GeographyName';
        foreach ($this->q->dreg->query($sQuery, PDO::FETCH_ASSOC) as $row) {
            $gItems[$row['GeographyId']] = $row;
        }
        return $gItems;
    }

    public function getDBDir()
    {
        $DBDir = '';
        if ($this->RegionId != '') {
            $DBDir = CONST_DBREGIONDIR . '/' . $this->RegionId;
        }
        return $DBDir;
    }

    // Read an specific InfoKey value from the table
    public function getDBInfoValue($prmInfoKey)
    {
        $sReturn = '';
        if ($this->RegionId != 'core') {
            $r = new DIRegion($this, $this->RegionId);
            $sReturn = $r->getRegionInfoValue($prmInfoKey);
        }
        return $sReturn;
    }

    public function getDateRange()
    {
        $StatusList = 'PUBLISHED';
        if ($this->UserRoleValue >= 2) {
            $StatusList = 'PUBLISHED READY DRAFT';
        }
        if ($this->UserRoleValue >= 4) {
            $StatusList = 'PUBLISHED READY DRAFT TRASH';
        }

        $res = array();
        $datemin = $this->getDBInfoValue('PeriodBeginDate');
        $datemax = $this->getDBInfoValue('PeriodEndDate');
        if (($datemin == '') || ($datemax == '')) {
            $bFirst = true;
            $statusQuery = '';
            foreach (explode(' ', $StatusList) as $status) {
                if (! $bFirst) {
                    $statusQuery .= ',';
                }
                $statusQuery .= '"' . $status . '"';
                $bFirst = false;
            }
            $statusQuery = 'RecordStatus IN (' . $statusQuery . ')';
            $sQuery = 'SELECT MIN(DisasterBeginTime) AS datemin, MAX(DisasterBeginTime) AS datemax FROM Disaster '.
                'WHERE (DisasterBeginTime<>"") AND ' . $statusQuery;
            foreach ($this->q->dreg->query($sQuery, PDO::FETCH_ASSOC) as $row) {
                if ($datemin == '') {
                    $datemin = $row['datemin'];
                }
                if ($datemax == '') {
                    $datemax = $row['datemax'];
                }
            }
            // 2010-01-21 (jhcaiced) Fix some weird cases in MinDate/MaxDate
            if (substr($datemin, 5, 2) == '00') {
                $datemin = substr($datemin, 0, 4) . '-01-01';
            }
            if (substr($datemax, 5, 2) > '12') {
                $datemax = substr($datemax, 0, 4) . '-12-31';
            }
        }
        if ($datemin == '') {
            $datemin = date('Y-m-d');
        }
        if ($datemax == '') {
            $datemax = date('Y-m-d');
        }
        if ($datemin > $datemax) {
            $datemin = $datemax;
        }
        $res[0] = substr($datemin, 0, 10);
        $res[1] = substr($datemax, 0, 10);
        return $res;
    }
}
