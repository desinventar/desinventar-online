<script language="php">
/*
  DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

define('ROLE_NONE'       , 0);
define('ROLE_OBSERVER'   , 1);
define('ROLE_USER'       , 2);
define('ROLE_SUPERVISOR' , 3);
define('ROLE_ADMINREGION', 4);
define('ROLE_ADMINPORTAL', 5);

class UserSession {

	public function __construct() {
		$this->sSessionId = session_id();
		$this->UserId            = '';
		$this->LangIsoCode       = '';
		$this->RegionId          = 'core';
		$this->RegionLangIsoCode = 'eng';
		$this->dStart            = gmdate('c');
		$this->dLastUpdate       = $this->dStart;
		$this->UserRole          = '';
		$this->UserRoleValue     = ROLE_NONE;
		$this->q = new Query();
		$num_args = func_num_args();
		if ($num_args > 0) {
			if (func_get_arg(0) != '') {  
				$this->sSessionId = func_get_arg(0);
			}
		}
		$this->load($this->sSessionId);
	} //constructor
	
	// Read Session Information from Database
	public function load($prmSessionId) {
		$iReturn = ERR_UNKNOWN_ERROR;
		$sQuery = 'SELECT * FROM UserSession WHERE SessionId=:SessionId';
		$sth = $this->q->core->prepare($sQuery);
		$this->q->core->beginTransaction();
		try {
			$sth->bindParam(':SessionId', $prmSessionId, PDO::PARAM_STR);
			$sth->execute();
			while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$this->sSessionId  = $row['SessionId'];
				$this->UserId      = $row['UserId'];
				$this->LangIsoCode = $row['LangIsoCode'];
				$this->dStart      = $row['Start'];
				$this->dLastUpdate = $row['LastUpdate'];
				$iReturn = ERR_NO_ERROR;
			} //while
			$this->q->core->commit();
		} catch (Exception $e) {
			showErrorMsg($e->getMessage());
			$this->q->core->rollBack();
		}
		// If session doesn't exist in database, insert record
		if ($iReturn < 0) {
			$this->insert();
		}
		return $iReturn;	
	} // function

	// Set LastUpdate field of Session so it will not expire...
	public function awake() {
		$iReturn = ERR_NO_ERROR;
		$PrevTime = $this->dLastUpdate;
		$CurTime  = gmdate('c');
		$this->dLastUpdate = $CurTime;
		
		// 2009-10-23 (jhcaiced) Logout users when too much time has passed
		// without using this session (Bug # 277)
		$Interval = strtotime($CurTime) - strtotime($PrevTime);
		// 60 * 30 = 1800 (30 minutes)
		if ($Interval > 1800) {
			$this->close();
			$this->logout();
		}
		try {
			$sQuery = 'UPDATE UserSession SET LastUpdate=:LastUpdate WHERE SessionId=:SessionId';
			$sth = $this->q->core->prepare($sQuery);
			$this->q->core->beginTransaction();
			$sth->bindParam(':SessionId', $this->sSessionId, PDO::PARAM_STR);
			$sth->bindParam(':LastUpdate', $this->dLastUpdate, PDO::PARAM_STR);
			$sth->execute();
			$this->q->core->commit();

			$sQuery = 'UPDATE UserLockList SET LastUpdate=:LastUpdate WHERE SessionId=:SessionId';
			$sth = $this->q->core->prepare($sQuery);
			$this->q->core->beginTransaction();			
			$sth->bindParam(':SessionId', $this->sSessionId, PDO::PARAM_STR);
			$sth->bindParam(':LastUpdate', $this->dLastUpdate, PDO::PARAM_STR);
			$sth->execute();
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			showErrorMsg($e->getMessage());
			$this->q->core->rollBack();
		}
		return $iReturn;
	}

	public function login($prmUserId, $prmUserPasswd) {
		$iReturn = ERR_DEFAULT_ERROR;
		$UserId = $this->validateUser($prmUserId, $prmUserPasswd);
		if ($UserId != '') {
			$iReturn = $this->setUser($UserId);
		}
		return $iReturn;
	}

	public function logout() {
		return $this->setUser('');
	}

	public function setUser($prmUserId) {
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = 'UPDATE UserSession SET UserId=:UserId ' . 
		          'WHERE SessionId=:SessionId';
		$sth = $this->q->core->prepare($sQuery);
		if ($result = $sth->execute(array(':UserId'    => $prmUserId,
		                                  ':SessionId' => $this->sSessionId))) {
			$iReturn = ERR_NO_ERROR;
			$this->UserId = $prmUserId;
		}
		return $iReturn;
	}
	
	public function setLangIsoCode($prmLangIsoCode) 
	{
		$this->LangIsoCode = $prmLangIsoCode;
	}

	// Start a Session by creating a record in the database
	// this could be an anonymous or authenticated session
	public function insert() {
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = 'INSERT INTO UserSession (SessionId,RegionId,UserId,Valid,LangIsoCode,Start,LastUpdate) ' .
		          ' VALUES (:SessionId,:RegionId,:UserId,:Valid,:LangIsoCode,:Start,:LastUpdate)';
		$sth = $this->q->core->prepare($sQuery);
		$sth->bindParam(':SessionId'  , $this->sSessionId, PDO::PARAM_STR);
		$sth->bindValue(':RegionId'   , '', PDO::PARAM_STR);
		$sth->bindParam(':UserId'     , $this->UserId, PDO::PARAM_STR);
		$sth->bindValue(':Valid'      , 1, PDO::PARAM_INT);
		$sth->bindParam(':LangIsoCode', $this->LangIsoCode, PDO::PARAM_STR);
		$sth->bindParam(':Start'      , $this->dStart, PDO::PARAM_STR);
		$sth->bindParam(':LastUpdate' , $this->dLastUpdate, PDO::PARAM_STR);
		if ($result = $sth->execute()) {
			$iReturn = ERR_NO_ERROR;
		}
		return $iReturn;
	} // insert()

	// Update information about this session in database
	public function update() {
		$iReturn = ERR_DEFAULT_ERROR;
		// Always update this field...
		$this->dLastUpdate = gmdate('c');
		$sQuery = 'UPDATE UserSession SET ' . 
				  'UserId=:UserId,' . 
				  'Valid=:Valid,' .
				  'LangIsoCode=:LangIsoCode,' . 
				  'Start=:Start,' .
				  'LastUpdate=:LastUpdate ' . 
				  'WHERE SessionId=:SessionId';
		$sth = $this->q->core->prepare($sQuery);
		$sth->bindParam(':SessionId'  , $this->sSessionId , PDO::PARAM_STR);
		$sth->bindValue(':RegionId'   , ''                , PDO::PARAM_STR);
		$sth->bindParam(':UserId'     , $this->UserId     , PDO::PARAM_STR);
		$sth->bindValue(':Valid'      , 1                 , PDO::PARAM_INT);
		$sth->bindParam(':LangIsoCode', $this->LangIsoCode, PDO::PARAM_STR);
		$sth->bindParam(':Start'      , $this->dStart     , PDO::PARAM_STR);
		$sth->bindParam(':LastUpdate' , $this->dLastUpdate, PDO::PARAM_STR);
		$sth->execute();
		$sQuery = 'UPDATE UserSession SET LangIsoCode="' . $this->LangIsoCode . '" WHERE SessionId="' . $this->sSessionId . '"';
		$this->q->core->query($sQuery);
		$sQuery = "UPDATE UserLockList SET LastUpdate='" . $this->dLastUpdate . "' WHERE SessionId='" . $this->sSessionId . "'";
		$this->q->core->query($sQuery);
		$iReturn = ERR_NO_ERROR;
		return $iReturn;
	} // update()

	// Close a session, removing the session information from the
	// database.
	public function delete() {
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = 'DELETE FROM UserSession WHERE SessionId=:SessionId';
		$sth = $this->q->core->prepare($sQuery);
		$sth->bindParam(':SessionId'  , $this->sSessionId, PDO::PARAM_STR);
		if ($result = $sth->execute()) {
			$this->UserId = '';
			$iReturn = ERR_NO_ERROR;
		}
		return $iReturn;
	} // delete()

	// Associate a RegionId with the session
	public function open($prmRegionId, $prmDBFile = '') {
		$iReturn = ERR_NO_ERROR;
		$this->clearLocks();
		
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
			$this->RegionId = $prmRegionId;
			$this->UserRole = $this->getUserRole($prmRegionId);
			$this->UserRoleValue = $this->getUserRoleValue($prmRegionId);
			$this->RegionLangIsoCode = $this->getDBInfoValue('LangIsoCode');
		} 
		return $iReturn;
	} // open()

	public function close($prmRegionId = '') {
		return $this->open('');
	} // close()

	// Validate a user/passwd pair against database
	public function validateUser($prmUserId, $prmUserPasswd, $withCrypt=true) {
		$UserId = '';
		if (! $withCrypt) {
			$prmUserPasswd = md5($prmUserPasswd);
		}
		$sQuery = 'SELECT * FROM User WHERE (UserId=:UserId OR UserNotes LIKE :UserNotes) AND UserPasswd=:UserPasswd';
		$sth = $this->q->core->prepare($sQuery);
		$sth->bindParam(':UserId', $prmUserId, PDO::PARAM_STR);
		$sth->bindValue(':UserNotes', '%(UserName=' . $prmUserId. ')%', PDO::PARAM_STR);
		$sth->bindParam(':UserPasswd', $prmUserPasswd, PDO::PARAM_STR);
		$sth->execute();
		try {
			while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$UserId = $row['UserId'];
			}
		} catch (Exception $e) {
			showErrorMsg($e->getMessage());
		} // catch
		return $UserId;
	} // valiteUser
	
	public function getUserFullName() {
		$sUserFullName = "";
		if ($result = $this->q->core->query("SELECT * FROM User WHERE UserId='" . $this->UserId . "'") ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$sUserFullName = $row->UserFullName;
			} // while
		}
		return $sUserFullName;
	}
	
	public function getAllPermsByUser() {
		return $this->getAllPermsGeneric($this->UserId, "");
	}

	private function getAllPermsGeneric($prmUserId, $prmRegionId) {
		$myPerms = array();
		$sQuery = "SELECT * FROM RegionAuth WHERE " .
		  "((UserId='" . $prmUserId . "') OR (UserId='')) ";
		if ($prmRegionId != "") {
			$sQuery .= " AND " . "((RegionId='" . $prmRegionId . "') OR (RegionId='')) ";
		}
		$sQuery = $sQuery + " ORDER BY AuthKey,AuthValue";
		if ($result = $this->q->core->query($sQuery) ) {
			$i = 0;
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
			 $sAuthKey = $row->AuthKey;
			 $sAuthValue = $row->AuthValue . "/" . $row->AuthAuxValue;
			 $myPerms[$sAuthkey] = $sAuthValue;
			} // while
		}
		return $myPerms;
	}

	// Return hash with all regions where the user has a role
	function getUserRoleList() {
		$myData = array();
		$sQuery = "SELECT RegionAuth.*,Region.RegionLabel FROM RegionAuth,Region WHERE " .
			" (RegionAuth.RegionId = Region.RegionId) " .
			" AND (UserId='" . $this->UserId . "') " .
			" AND AuthKey='ROLE'" . 
			" ORDER BY RegionAuth.RegionId";
		if ($result = $this->q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$sKey = $row->RegionId;
				$sValue = $row->AuthAuxValue;
				$myData[$sKey]['Role']        = $row->AuthAuxValue;
				$myData[$sKey]['RegionLabel'] = $row->RegionLabel;
			} // while
		}
		return $myData;
	} // function

	// Return hash with all  users of a Region with a role
	function getRegionRoleList($myregion) {
		$myData = array();
		$sQuery = "SELECT RegionAuth.*,Region.RegionLabel FROM RegionAuth,Region WHERE " .
		  " (RegionAuth.RegionId = Region.RegionId) " .
		  " AND (Region.RegionId='" . $myregion . "') " .
		  " AND RegionAuth.AuthKey='ROLE' AND RegionAuth.AuthAuxValue != 'NONE'" .
		  " ORDER BY RegionAuth.RegionId";
		if ($result = $this->q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$sKey = $row->UserId;
				$sValue = $row->AuthAuxValue;
				$myData[$sKey] = $sValue;
			} // while
		}
		return $myData;
	} // function

	// Get basic user info: user=>[email,pass,name,org,country,city,creadate,iplist,notes,active]
	function getUserInfo ($prmUserId) {
		$myData = array();
		$sQuery = "SELECT * FROM User";
		if ($prmUserId != '')
			$sQuery .= " WHERE UserId='" . $prmUserId . "'";
		$sQuery .= " ORDER BY UserFullName";
		if ($result = $this->q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$myData[$row->UserId] = array ($row->UserEMail, $row->UserPasswd, $row->UserFullName, $row->Organization, 
					$row->CountryIso, $row->UserCity, $row->UserCreationDate, $row->UserNotes, $row->UserActive);
			}
		}
		return $myData;
	}
	
	// Send a Password Reminder to an E-mail
	function sendPasswdReminder($prmEMail) {
		$myAnswer = '';
		$sQuery = "SELECT * FROM User WHERE (UserEMail='" . $prmEMail . "') ";
		foreach($this->q->core->query($sQuery) as $row) {
			$myAnswer = $row['UserEMail'];
			$myPasswd = generatePasswd();
			$this->updateUserPasswd($row['UserId'], md5($myPasswd));
			// uhmm, must revise if send mail-> offline systems ??
			mail($myAnswer, 
				 "DesInventar - Password Reminder",
				 "Dear User\nYour login information for DesInventar is:\n" .
				 "  UserId : " . $row['UserId'] . "\n" .
				 "  Passwd : " . $myPasswd . "\n" . 
				 "\n\n" .
				 "Sincerely,\n" .
				 "   The DesInventar Team",
				"From: support@desinventar.org"
			);
		} //foreach
		return $myAnswer;
	}

	// Return Role for a Region
	function getUserRole($prmRegionId='') {
		if ($prmRegionId == '') {
			$prmRegionId = $this->RegionId;
		}
		$myAnswer = "";
		$sQuery = "SELECT * FROM RegionAuth WHERE ";
		if ($prmRegionId != '') {
			$sQuery .= "((RegionId='') OR (RegionId='" . $prmRegionId . "'))";
		} else {
			$sQuery .= "(RegionId='')";
		}
		$sQuery .= " AND (UserId='" . $this->UserId . "') " .
		           " AND AuthKey='ROLE'" . 
		           " ORDER BY UserId,RegionId";
		if ($result = $this->q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$myAnswer = $row->AuthAuxValue;
			} // while
		}
		return $myAnswer;
	} // function
	
	// Get User Role as a Numeric Value, easier to compare
	function getUserRoleValue($prmRegionId = '') {
		$Role = $this->getUserRole($prmRegionId);
		$NumRole = ROLE_NONE;
		if ($Role == 'NONE')        { $NumRole = ROLE_NONE;        }
		if ($Role == 'OBSERVER')    { $NumRole = ROLE_OBSERVER;    }
		if ($Role == 'USER')        { $NumRole = ROLE_USER;        }
		if ($Role == 'SUPERVISOR')  { $NumRole = ROLE_SUPERVISOR;  }
		if ($Role == 'ADMINREGION') { $NumRole = ROLE_ADMINREGION; }
		if ($Role == 'ADMINPORTAL') { $NumRole = ROLE_ADMINPORTAL; }
		return $NumRole;
	}
	
	public function setUserRole($prmUserId, $prmRegionId, $prmRole) {
		$iReturn = ERR_NO_ERROR;
		if ($prmUserId   == '') { $iReturn = ERR_DEFAULT_ERROR; }
		if ($prmRegionId == '') { $iReturn = ERR_DEFAULT_ERROR; }
		if ($iReturn > 0) {
			// Remove All Permissions for This User on This Database
			$sQuery = "DELETE FROM RegionAuth WHERE UserId='" . $prmUserId . "' AND RegionId='" . $prmRegionId . "'";
			$this->q->core->query($sQuery);
			// Insert ROLE Permission First		
			$this->setPerm($prmUserId, $prmRegionId, 'ROLE', 0, $prmRole);
			switch($prmRole) {
				case 'ADMINREGION':
					$this->setPerm($prmUserId, $prmRegionId, "DISASTER" , 5, "");
					$this->setPerm($prmUserId, $prmRegionId, "EVENT"    , 5, "");
					$this->setPerm($prmUserId, $prmRegionId, "CAUSE"    , 5, "");
					$this->setPerm($prmUserId, $prmRegionId, "GEOGRAPHY", 5, "");
					$this->setPerm($prmUserId, $prmRegionId, "GEOLEVEL" , 5, "");
					$this->setPerm($prmUserId, $prmRegionId, "EEFIELD"  , 5, "");
					$this->setPerm($prmUserId, $prmRegionId, "DBINFO"   , 2, "");
					$this->setPerm($prmUserId, $prmRegionId, "AUTH"     , 2, "");
					$this->setPerm($prmUserId, $prmRegionId, "DBPUBLIC" , 2, "");
					$this->setPerm($prmUserId, $prmRegionId, "DBACTIVE" , 2, "");
					$this->setPerm($prmUserId, $prmRegionId, "DBLOG"    , 5, "");
					break;
				case 'SUPERVISOR':
					$this->setPerm($prmUserId, $prmRegionId, "DISASTER" , 4, "STATUS=DRAFT,STATUS=READY");
					$this->setPerm($prmUserId, $prmRegionId, "EVENT"    , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "CAUSE"    , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "GEOGRAPHY", 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "GEOLEVEL" , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "EEFIELD"  , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "DBINFO"   , 1, "");
					$this->setPerm($prmUserId, $prmRegionId, "DBLOG"    , 3, "");
					break;
				case 'USER':
					$this->setPerm($prmUserId, $prmRegionId, "DISASTER" , 3, "STATUS=DRAFT,STATUS=READY");
					$this->setPerm($prmUserId, $prmRegionId, "EVENT"    , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "CAUSE"    , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "GEOGRAPHY", 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "GEOLEVEL" , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "EEFIELD"  , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "DBINFO"   , 1, "");
					$this->setPerm($prmUserId, $prmRegionId, "DBLOG"    , 3, "");
					break;
				case 'OBSERVER':
					$this->setPerm($prmUserId, $prmRegionId, "DISASTER" , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "EVENT"    , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "CAUSE"    , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "GEOGRAPHY", 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "GEOLEVEL" , 1, "STATUS=ACTIVE");
					$this->setPerm($prmUserId, $prmRegionId, "DBINFO"   , 1, "");
					$this->setPerm($prmUserId, $prmRegionId, "DBLOG"    , 1, "");
					$this->setPerm($prmUserId, $prmRegionId, "EEFIELD"  , 1, "STATUS=ACTIVE");
					break;
				case 'MINIMAL':
					$this->setPerm($prmUserId, $prmRegionId, "USER"     , 2, "");
					break;
				case 'NONE' :
					// Do not set any permission
					break;
			} //switch
		}
		return $iReturn;
	} //function
	
	public function setPerm($prmUserId, $prmRegionId, $prmAuthKey, $prmValue, $prmAuxValue) {
		$sQuery = "INSERT INTO RegionAuth VALUES (" . 
			"'" . $prmUserId . "','" . $prmRegionId  . "'," .
			"'" . $prmAuthKey . "','" . $prmValue . "','" . $prmAuxValue . "')";
		$this->q->core->query($sQuery);
	}

	public function getRegionList($prmCountryIsoCode, $prmStatus) {
		if (!empty($prmCountryIsoCode))
			$opt = " CountryIsoCode='$prmCountryIsoCode'";
		else
			$opt = " 1=1";
		if ($prmStatus == "ACTIVE")
			$opt .= " AND RegionActive = True";
		$sQuery = "SELECT RegionId, RegionLabel FROM Region " .
		  " WHERE $opt ORDER BY RegionLabel";
		$myData = array();
		if ($result = $this->q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
			 $sKey = $row->RegionId;
			 $sValue = $row->RegionLabel;
			 $myData[$sKey] = $sValue;
			} // while
		}
		return $myData;
	} // function
	
	public function doUserExist($prmUserId) {
		$Answer = ERR_UNKNOWN_ERROR;
		$Query = "SELECT UserId FROM User WHERE UserId='" . $prmUserId . "'";
		foreach ($this->q->core->query($Query) as $row) {
			$Answer = ERR_NO_ERROR;
		}
		return $Answer;
	}
	
	public function insertUser($UserId, $UserFullName, $UserEMail, $UserPasswd, $UserCountry, $UserCity, $UserActive) {
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
		if ($result = $this->q->core->query($sQuery))
			$iReturn = ERR_NO_ERROR;
		return $iReturn;
	}
	
	public function updateUser($UserId, $UserFullName, $UserEMail, $UserPasswd, $UserCountry, $UserCity, $UserActive) {
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = "UPDATE User SET " . 
				  "UserEMail='" . $UserEMail . "',".
				  "UserFullName='" . $UserFullName . "'," .
				  "Organization=''," .
				  "CountryIso='" . $UserCountry . "'," .
				  "UserCity='" . $UserCity . "'," .
				  "UserNotes=''," .
				  "UserActive=" . $UserActive;
		if (!empty($UserPasswd))
			$sQuery .= ", UserPasswd='" . md5($UserPasswd) . "'";
		$sQuery .=  " WHERE UserId='" . $UserId . "'";
		if ($result = $this->q->core->query($sQuery))
			$iReturn = ERR_NO_ERROR;
		return $iReturn;
	}
	
	public function updateUserPasswd($UserId, $UserPasswd) {
		$iReturn = ERR_DEFAULT_ERROR;
		$Query = 'UPDATE User SET UserPasswd="' . $UserPasswd . '" WHERE UserId="' . $UserId . '"';
		if ($result = $this->q->core->query($Query)) {
			$iReturn = ERR_NO_ERROR;
		}		
		return $iReturn;
	}
	
	public function clearOldLocks() {
		$deltime = gmdate('c', time() - 600);
		$sQuery = "DELETE FROM UserLockList WHERE RecordUpdate<='" . $deltime . "'";
		$this->q->core->query($sQuery);
	}
	
	public function isDatacardLocked($prmDisasterId) {
		// First delete old datacard locks...
		$this->clearOldLocks();
		$sQuery = "SELECT * FROM UserLockList WHERE RecordId='" . $prmDisasterId . "'";
		$sReturn = '';
		foreach ($this->q->core->query($sQuery) as $row) {
			$sReturn = $row['SessionId'];
		}
		return $sReturn;
	}
	
	public function lockDatacard($prmDisasterId) {
		// First delete old datacard locks...
		$this->clearOldLocks();
		$now = gmdate('c');
		$sQuery = "INSERT INTO UserLockList VALUES ('" . $this->sSessionId . "','DISASTER','" . $prmDisasterId . "','" . $now . "')";
		$this->q->core->query($sQuery);
	}
	
	public function releaseDatacard($prmDisasterId) {
		$sQuery = "DELETE FROM UserLockList WHERE SessionId='" . $this->sSessionId . "' AND RecordId='" . $prmDisasterId . "'";
		$this->q->core->query($sQuery);
	}

	public function clearLocks() {
		$sQuery = "DELETE FROM UserLockList WHERE SessionId='" . $this->sSessionId . "'";
		$this->q->core->query($sQuery);
	}
	
	public function getUsersList($prmUserId) {
		$list = array();
		$sQuery = "SELECT * FROM User WHERE UserActive > 0 ";
		if ($prmUserId != '') {
			$sQuery .= " AND UserId='" . $prmUserId . "'";
		}
		$sQuery .= " ORDER BY UserFullName";
		foreach($this->q->core->query($sQuery) as $row) {
			$list[$row['UserId']]=$row['UserFullName'];
		}
		return $list;
	}
	
	public function chkPasswd ($passwd) {
		return true;
	}

	public function listDB() {
		$RegionList = array();
		$query = "SELECT R.RegionId AS RegionId, R.RegionLabel AS RegionLabel, R.CountryIso AS CountryIso, R.RegionStatus AS RegionStatus, ".
			"RA.AuthAuxValue AS Role FROM Region AS R, RegionAuth AS RA WHERE R.RegionId = RA.RegionId ";
		/*
		if ($searchByCountry) {
			$query .= " AND R.CountryIso = '" . $prmQuery . "'";
		}
		*/
		if ($this->UserId) {
			$query .= " AND RA.AuthKey = 'ROLE' AND RA.UserId = '". $this->UserId ."'";
		} else {
			$query .= " AND R.RegionStatus = 3 GROUP BY R.RegionId";
		}
		$query .= " ORDER BY R.CountryIso, R.RegionLabel, R.RegionOrder";
		$result = $this->q->core->query($query);
		while ($row = $result->fetch(PDO::FETCH_OBJ)) {
			$RegionList[$row->RegionId] = array($row->RegionLabel, $row->CountryIso, $row->RegionStatus, $row->Role);
		}
		return $RegionList;
	}
	

	public function searchDB($prmQuery, $searchByCountry) {
		$regionlist = array();

		// Search for Public Databases and assign to (ROLE=NONE)
		$query = "SELECT RegionId,CountryIso,RegionLabel FROM Region WHERE RegionStatus=3 AND "; 
		if ($searchByCountry > 0) {
			$query .= "(CountryIso = '" . $prmQuery . "')";
		} else {
			$query .= "(RegionId LIKE '%" . $prmQuery . "%' OR RegionLabel LIKE '%" . $prmQuery . "%')";
		}
		$query .= " ORDER BY CountryIso,RegionLabel,RegionOrder";
		foreach($this->q->core->query($query) as $row) {
			$regionlist[$row['RegionId']] = array('RegionLabel' => $row['RegionLabel'],
			                                      'CountryIso'  => $row['CountryIso'],
			                                      'Role' => 'NONE');
		}
		
		if ($searchByCountry <= 0) {
			// Add Regions with specific Roles
			$query = "select R.RegionId,R.CountryIso,R.RegionLabel,RA.AuthAuxValue from Region R,RegionAuth RA where R.RegionId=RA.RegionId AND RA.AuthKey='ROLE' AND RA.UserId='" . $this->UserId . "' ORDER BY R.CountryIso,R.RegionLabel;";
			foreach($this->q->core->query($query) as $row) {
				$regionlist[$row['RegionId']] = array('RegionLabel' => $row['RegionLabel'],
				                                      'CountryIso'  => $row['CountryIso'],
				                                      'Role' => $row['AuthAuxValue']);
			}
		}
		
		return $regionlist;
	}

	public function getDisasterIdFirst() {
		$Record = 1;
		$RecordCount = $this->getDisasterCount();
		$DisasterId = $this->getDisasterIdFromRecordNumber($Record);
		return array('DisasterId' => $DisasterId, 'RecordNumber' => $Record, 'RecordCount' => $RecordCount, 'Status' => 'OK');
	}

	public function getDisasterIdLast() {
		$RecordCount = $this->getDisasterCount();
		$Record = $RecordCount;
		$DisasterId = $this->getDisasterIdFromRecordNumber($Record);
		return array('DisasterId' => $DisasterId, 'RecordNumber' => $Record, 'RecordCount' => $RecordCount, 'Status' => 'OK');
	}

	public function getDisasterIdPrev($prmRecord) {
		$Record = $prmRecord;
		$RecordCount = $this->getDisasterCount();
		if ($Record > 1) {
			$Record--;
		}
		$DisasterId = $this->getDisasterIdFromRecordNumber($Record);
		return array('DisasterId' => $DisasterId, 'RecordNumber' => $Record, 'RecordCount' => $RecordCount, 'Status' => 'OK');
	}

	public function getDisasterIdNext($prmRecord) {
		$Record = $prmRecord;
		$RecordCount = $this->getDisasterCount();
		if ($Record < $RecordCount) {
			$Record++;
		}
		$DisasterId = $this->getDisasterIdFromRecordNumber($Record);
		return array('DisasterId' => $DisasterId, 'RecordNumber' => $Record, 'RecordCount' => $RecordCount, 'Status' => 'OK');
	}

	public function getDisasterIdFromSerial($prmDisasterSerial)
	{
		$RecordCount = $this->getDisasterCount();
		$DisasterId = '';
		$sQuery = 'SELECT DisasterId,DisasterSerial FROM Disaster ORDER BY DisasterSerial';
		$sth = $this->q->dreg->prepare($sQuery);
		$this->q->dreg->beginTransaction();
		try {
			$sth->execute();
			$reclist = $sth->fetchAll(PDO::FETCH_ASSOC);
			$this->q->dreg->commit();
		}
		catch (Exception $e)
		{
			showErrorMsg($e->getMessage());
			$this->q->dreg->rollBack();
		}
		$bFound = 0;
		$RecordNumber = 0;
		foreach ($reclist as $row)
		{
			$RecordNumber++;
			if ($row['DisasterSerial'] == $prmDisasterSerial)
			{
				$bFound = 1;
				$DisasterId = $row['DisasterId'];
				break;
			}
		} //while
		unset($reclist);
		return array('Status' => 'OK', 'DisasterId' => $DisasterId, 
		             'RecordNumber' => $RecordNumber, 'RecordCount' => $RecordCount);
	}

	public function getDisasterCount() {
		$iCount = 0;
		$sQuery = "SELECT COUNT(DisasterId) AS C FROM Disaster";
		foreach($this->q->dreg->query($sQuery) as $row) {
			$iCount = $row['C'];
		}
		return $iCount;
	}
		
	public function getDisasterIdFromRecordNumber($prmRecord) {
		$DisasterId = '';
		$prmRecord--;
		$sQuery = "SELECT DisasterId FROM Disaster ORDER BY DisasterBeginTime,DisasterSerial LIMIT " . $prmRecord . ",1";
		foreach($this->q->dreg->query($sQuery) as $row) {
			$DisasterId = $row['DisasterId'];
		}
		return $DisasterId;
	}

	public function existDisasterSerial($prmDisasterSerial) {
		$Answer = '';
		$sQuery = "SELECT DisasterSerial FROM Disaster WHERE DisasterSerial = '". $prmDisasterSerial."'";
		foreach($this->q->dreg->query($sQuery) as $row) {
			$Answer = $row['DisasterSerial'];
		}
		return array('Status' => 'OK', 'DisasterSerial' => $Answer);
	}

	// Return a small list with the information about levels in database
	function getGeoLevels() {
		$sQuery = "SELECT GeoLevelId, GeoLevelName FROM GeoLevel WHERE GeoLevelActive=1 ORDER BY GeoLevelId";
		$GeoLevels = array();
		foreach ($this->q->dreg->query($sQuery) as $row) {
			array_push($GeoLevels, $row);
		}
		return $GeoLevels;
	}
	
	// Return an array with the subcomponents of a GeographyId item...
	function getGeographyItemsById($prmGeographyId) {
		$gItems = array();
		for($iLevel = 0; $iLevel < strlen($prmGeographyId)/5; $iLevel++) {
			$gId = substr($prmGeographyId, 0, ($iLevel+1)*5);
			$sQuery = 'SELECT GeographyId, GeographyName FROM Geography WHERE GeographyId="' . $gId . '"';
			foreach($this->q->dreg->query($sQuery) as $row) {
				$gItems[$iLevel] = $row;
			}
		} //for
		return $gItems;
	}
	
	function getGeographyItemsByLevel($prmGeographyLevel, $prmGeographyParentId) {
		$gItems = array();
		$sQuery = 'SELECT GeographyId,GeographyName FROM Geography WHERE GeographyActive=1 AND GeographyLevel=' . $prmGeographyLevel;
		if ($prmGeographyLevel > 0) {
			$sQuery .= ' AND SUBSTR(GeographyId,1,' . ($prmGeographyLevel*5) . ')="' . $prmGeographyParentId . '"';
		}
		$sQuery .= ' ORDER BY GeographyName';
		foreach($this->q->dreg->query($sQuery) as $row) {
			array_push($gItems, $row);
		} //foreach
		return $gItems;
	}

	public function getDBDir() {
		$DBDir = '';
		if ($this->RegionId != '') {
			$DBDir = CONST_DBREGIONDIR . '/' . $this->RegionId;
		}
		return $DBDir;
	}

	// Read an specific InfoKey value from the table
	function getDBInfoValue($prmInfoKey) {
		$sReturn = '';
		if ($this->RegionId != 'core') {
			$r = new DIRegion($this, $this->RegionId);
			$sReturn = $r->getRegionInfoValue($prmInfoKey);
		}
		return $sReturn;
	}

	public function getDateRange() {
		$Role = $this->getUserRole();
		$StatusList = 'PUBLISHED';
		if ($Role == 'ADMINREGION') { $StatusList = 'PUBLISHED READY DRAFT TRASH'; }
		if ($Role == 'SUPERVISOR' ) { $StatusList = 'PUBLISHED READY DRAFT TRASH'; }
		if ($Role == 'USER'       ) { $StatusList = 'PUBLISHED READY DRAFT'; }
		if ($Role == 'OBSERVER'   ) { $StatusList = 'PUBLISHED READY DRAFT'; }

		$res = array();
		$datemin = $this->getDBInfoValue('PeriodBeginDate');
		$datemax = $this->getDBInfoValue('PeriodEndDate');
		if (($datemin == '') || ($datemax == '')) {
			$bFirst = true;
			$statusQuery = '';
			foreach(explode(' ',$StatusList) as $status) {
				if (! $bFirst) {
					$statusQuery .= ',';
				}
				$statusQuery .= '"' . $status . '"';
				$bFirst = false;
			}
			$statusQuery = 'RecordStatus IN (' . $statusQuery . ')';
			$sql = "SELECT MIN(DisasterBeginTime) AS datemin, MAX(DisasterBeginTime) AS datemax FROM Disaster ".
			"WHERE " . $statusQuery;
			$r2 = $this->q->getresult($sql);
			if ($datemin == '' ) { $datemin = $r2['datemin']; }
			if ($datemax == '' ) { $datemax = $r2['datemax']; }
		}
		$res[0] = substr($datemin, 0, 10);
		$res[1] = substr($datemax, 0, 10);
		
		if (! $res[0]) { $res[0] = date('Y-m-d'); }
		if (! $res[1]) { $res[1] = date('Y-m-d'); }
		return $res;
	} //function
} //class


</script>
