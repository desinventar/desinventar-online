<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

class UserSession {
	var $q               = null;
	var $sSessionId      = '';
	var $RegionId       = 'core';
	var $LangIsoCode     = 'eng';
	var $UserId          = '';
	var $dStart          = '';
	var $dLastUpdate     = '';

	public function __construct() {
		$this->sSessionId = session_id();
		$this->dStart = gmdate('c');
		$this->dLastUpdate = $this->dStart;
		$this->q = new Query();
		$num_args = func_num_args();
		if ($num_args > 0) {
			if (func_get_arg(0) != "") {  
				$this->sSessionId = func_get_arg(0);
			}
		}
		$this->UserId = '';
		$this->LangIsoCode = 'eng';
		$this->load($this->sSessionId);
	} //constructor
	
	// Read Session Information from Database
	public function load($prmSessionId) {
		$iReturn = ERR_UNKNOWN_ERROR;
		$sQuery = "SELECT * FROM UserSession WHERE SessionId='" . $prmSessionId . "'";
		try {
			foreach($this->q->core->query($sQuery) as $row) {
				$this->sSessionId  = $row['SessionId'];
				$this->UserId      = $row['UserId'];
				$this->dStart      = $row['Start'];
				$this->dLastUpdate = $row['LastUpdate'];
				$iReturn = ERR_NO_ERROR;
			} //foreach
		} catch (Exception $e) {
			showErrorMsg($e->getMessage());
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
		$iReturn = $this->update();
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
		return $this->setUser("");
	}

	public function setUser($prmUserId) {
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = "UPDATE UserSession SET UserId='" . $prmUserId . "' " .
		          "WHERE SessionId='" . $this->sSessionId . "'";
		if ($result = $this->q->core->query($sQuery)) {
			$iReturn = ERR_NO_ERROR;
			$this->UserId = $prmUserId;
		}
		return $iReturn;
	}

	// Start a Session by creating a record in the database
	// this could be an anonymous or authenticated session
	public function insert() {
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = "INSERT INTO UserSession VALUES (" . 
				  "'" . $this->sSessionId . "'," .
				  "''," .
				  "'" . $this->UserId  . "'," .
				  "1," .
				  "'" . $this->dStart     . "'," .
				  "'" . $this->dLastUpdate . "'" .
				  ")";
		if ($result = $this->q->core->query($sQuery)) {
			$iReturn = ERR_NO_ERROR;
		}
		return $iReturn;
	} // insert()

	// Update information about this session in database
	public function update() {
		$iReturn = ERR_DEFAULT_ERROR;
		// Always update this field...
		$this->dLastUpdate = gmdate('c');
		$sQuery = "UPDATE UserSession SET " . 
				  "UserId='"   . $this->UserId  . "'," .
				  "Valid=1," .
				  "Start='"      . $this->dStart     . "'," .
				  "LastUpdate='" . $this->dLastUpdate . "'" .
				  "WHERE SessionId ='" . $this->sSessionId . "'";
		if ($result = $this->q->core->query($sQuery)) {
			$sQuery = "UPDATE UserLockList SET LastUpdate='" . $this->dLastUpdate . "' WHERE SessionId='" . $this->sSessionId . "'";
			$this->q->core->query($sQuery);
			$iReturn = ERR_NO_ERROR;
		}
		return $iReturn;
	} // update()

	// Close a session, removing the session information from the
	// database.
	public function delete() {
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = "DELETE FROM UserSession WHERE SessionId='" . $this->sSessionId . "'";
		if ($result = $this->q->core->query($sQuery)) {
			$this->UserId = "";
			$iReturn = ERR_NO_ERROR;
		}
		return $iReturn;
	} // delete()

	// Associate a RegionId with the session
	public function open($prmRegionId) {
		$iReturn = ERR_NO_ERROR;
		$this->clearLocks();
		
		$DBDir = VAR_DIR . '/database/' . $prmRegionId;
		$DBFile = $DBDir . '/desinventar.db';
		if (! file_exists($DBFile)) {
			$iReturn = ERR_NO_DATABASE;
		}
		
		if ($iReturn > 0) {
			$this->awake();
			$this->q->setDBConnection($prmRegionId);
			$this->RegionId = $prmRegionId;
		}
		return $iReturn;
	} // open()

	public function close($prmRegionId = '') {
		return $this->open("");
	} // close()

	// Validate a user/passwd pair against database
	public function validateUser($prmUserId, $prmUserPasswd, $withCrypt=false) {
		$UserId = '';
		if (! $withCrypt) {
			$prmUserPasswd = md5($prmUserPasswd);
		}
		$sQuery = "SELECT * FROM User WHERE (UserId='" . $prmUserId . "' OR UserNotes LIKE '%(UserName=" . $prmUserId . ")%') AND (UserPasswd='" . $prmUserPasswd . "')" ;
		try {
			foreach($this->q->core->query($sQuery) as $row) {
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
		  " AND AuthKey='ROLE'" . 
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
	
	public function setUserRole($prmUserId, $prmRegionId, $prmRole) {
		$iReturn = ERR_NO_ERROR;
		if ($prmUserId == '') { $iReturn = ERR_DEFAULT_ERROR; }
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
	
	public function existUser($prmUserId) {
		$Answer = false;
		$Query = "SELECT UserId FROM User WHERE UserId='" . $prmUserId . "'";
		foreach ($this->q->core->query($Query) as $row) {
			$Answer = true;
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
	
	public function getDateRange() {
		$Role = $this->getUserRole();
		$StatusList = 'PUBLISHED';
		if ($Role == 'ADMINREGION') { $StatusList = 'PUBLISHED READY DRAFT DELETED'; }
		if ($Role == 'SUPERVISOR' ) { $StatusList = 'PUBLISHED READY DRAFT DELETED'; }
		if ($Role == 'USER'       ) { $StatusList = 'PUBLISHED READY DRAFT DELETED'; }
		if ($Role == 'OBSERVER'   ) { $StatusList = 'PUBLISHED READY DRAFT DELETED'; }
		return $this->q->getDateRange(explode(' ', $StatusList));
	}
} //class

</script>
