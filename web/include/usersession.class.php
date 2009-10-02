<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class UserSession {
	var $q               = null;
	var $sSessionId      = '';
	var $sRegionId       = 'core';
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
				$this->UserId   = $row['UserId'];
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
		$this->dLastUpdate = gmdate('c');
		$iReturn = $this->update();
		return $iReturn;
	}

	public function login($prmUserId, $prmUserPasswd) {
		$iReturn = ERR_DEFAULT_ERROR;
		$iReturn = $this->validateUser($prmUserId, $prmUserPasswd);
		if ($iReturn > 0) {
			$iReturn = $this->setUser($prmUserId);
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
			$this->sRegionId = $prmRegionId;
		}
		return $iReturn;
	} // open()

	public function close($prmRegionId = '') {
		return $this->open("");
	} // close()

	// Validate a user/passwd pair against database
	public function validateUser($prmUserId, $prmUserPasswd) {
		$iReturn = ERR_DEFAULT_ERROR;
		if ( ($prmUserId == "") && ($prmUserPasswd == "")) {
			// This is an anonymous session
			$iReturn = ERR_NO_ERROR;
		} else {
			$sQuery = "SELECT * FROM User WHERE UserId='" . $prmUserId . "' OR UserNotes LIKE '%(UserName=" . $prmUserId . ")'" ;
			try {
				$result = $this->q->core->query($sQuery);
				$iReturn = ERR_DEFAULT_ERROR;
				while (($iReturn < 0) && ($row = $result->fetch(PDO::FETCH_OBJ))) {
					if ($row->UserPasswd == md5($prmUserPasswd)) {
						$iReturn = ERR_NO_ERROR;
					}
				} // while
			} catch (Exception $e) {
				showErrorMsg($e->getMessage());
			} // catch
		}
		return $iReturn;
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
		$sQuery = "SELECT * FROM User " .
		  " WHERE UserId='" . $this->UserId . "'" .
		  " ORDER BY UserFullName";
		if ($result = $this->q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$myData[0] = $row->UserEMail;
				$myData[1] = $row->UserPasswd;
				$myData[2] = $row->UserFullName;
				$myData[3] = $row->Organization;
				$myData[4] = $row->CountryIso;
				$myData[5] = $row->UserCity;
				$myData[6] = $row->UserCreationDate;
				$myData[7] = $row->UserNotes;
				$myData[8] = $row->UserActive;
			} // while
		}
		return array($this->UserId => $myData);
	}
	
	// Send a Password Reminder to an E-mail
	function sendPasswdReminder($prmEMail) {
		$myAnswer = '';
		$sQuery = "SELECT * FROM User " .
		  " WHERE (UserEMail='" . $prmEMail . "') ";
		if ($result = $this->q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$myAnswer = $row->UserEMail;
				// uhmm, must revise if send mail-> offline systems ??
				mail($myAnswer, 
			     "DesInventar - Password Reminder",
			     "Dear User\nYour login information for DesInventar is:\n" .
			     "  UserId : $row->UserId\n" .
			     "  Login    : $row->UserPasswd\n" . 
			     "\n\n" .
			     "Sincerely,\n" .
			     "   The DesInventar Team",
				"From: desinventar@desinventar.org");
			} // while
		}
		return $myAnswer;
	}

	// Return Role for a Region
	function getUserRole($prmRegionId) {
		$myAnswer = "";
		$sQuery = "SELECT * FROM RegionAuth WHERE " .
		  "     ((RegionId='') OR (RegionId='" . $prmRegionId . "')) " .
		  " AND (UserId='" . $this->UserId . "') " .
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
			$sQuery = "DELETE FROM RegionAuth WHERE " .
				" UserId='" . $prmUserId . "' AND " . 
				" RegionId='" . $prmRegionId . "'";
			$this->q->core->query($sQuery);
			
			// Create Role Permission
			$sQuery = "INSERT INTO RegionAuth (UserId,RegionId,AuthKey,AuthValue,AuthAuxValue) " .
				" VALUES ('" . $prmUserId . "','" . $prmRegionId . "','ROLE',0,'" . $prmRole . "')";
			$this->q->core->query($sQuery);
			
			// Add permissions according to each role
			$permObserver    = array("DISASTER"  => array(1, "STATUS=ACTIVE"),
			                         "EVENT"     => array(1, "STATUS=ACTIVE"),
			                         "CAUSE"     => array(1, "STATUS=ACTIVE"),
			                         "GEOLEVEL"  => array(1, "STATUS=ACTIVE"),
			                         "GEOGRAPHY" => array(1, "STATUS=ACTIVE"),
			                         "EEFIELD"   => array(1, "STATUS=ACTIVE"),
			                         "DBINFO"    => array(1, ""),
			                         "DBLOG"     => array(1, "")
			                        );
			$permUser        = array("DISASTER"  => array(3, "STATUS=DRAFT,STATUS=READY"),
			                         "EVENT"     => array(1, "STATUS=ACTIVE"),
			                         "CAUSE"     => array(1, "STATUS=ACTIVE"),
			                         "GEOLEVEL"  => array(1, "STATUS=ACTIVE"),
			                         "GEOGRAPHY" => array(1, "STATUS=ACTIVE"),
			                         "EEFIELD"   => array(1, "STATUS=ACTIVE"),
			                         "DBINFO"    => array(1, ""),
			                         "DBLOG"     => array(3, ""),
			                        );
			$permSupervisor  = array("DISASTER"  => array(4, "STATUS=DRAFT,STATUS=READY"),
			                         "EVENT"     => array(1, "STATUS=ACTIVE"),
			                         "CAUSE"     => array(1, "STATUS=ACTIVE"),
			                         "GEOLEVEL"  => array(1, "STATUS=ACTIVE"),
			                         "GEOGRAPHY" => array(1, "STATUS=ACTIVE"),
			                         "EEFIELD"   => array(1, "STATUS=ACTIVE"),
			                         "DBINFO"    => array(1, ""),
			                         "DBLOG"     => array(3, ""),
			                        );
			$permAdminRegion = array("DISASTER"  => array(5, ""),
			                         "EVENT"     => array(5, ""),
			                         "CAUSE"     => array(5, ""),
			                         "GEOLEVEL"  => array(5, ""),
			                         "GEOGRAPHY" => array(5, ""),
			                         "EEFIELD"   => array(5, ""),
			                         "DBINFO"    => array(5, ""),
			                         "DBLOG"     => array(5, ""),
			                        );
			$perm = array('OBSERVER'    => $permObserver,
			              'USER'        => $permUser,
			              'SUPERVISOR'  => $permSupervisor,
			              'ADMINREGION' => $permAdminRegion);
			foreach($perm[$prmRole] as $k => $v) {
				$this->setPerm($prmUserId, $prmRegionId, $k, $v[0], $v[1]);
			}
		}
		return $iReturn;
	} //function
	
	public function setPerm($prmUserId, $prmRegionId, $prmAuthKey, $prmValue, $prmAuxValue) {
		$sQuery = "INSERT INTO RegionAuth VALUES (" . 
			"'" . $prmUserId . "','" . $prmRegionId  . "'," .
			"'" . $prmAuthKey . "','" . $prmValue . ",'" . $prmAuxValue . "')";
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
		return true;
	}
	
	public function insertUser($UserId, $UserFullName, $UserEMail, $UserPasswd, $UserCountry, $UserCity, $UserActive) {
		return true;
	}
	
	public function updateUser($UserId, $UserFullName, $UserEMail, $UserPasswd, $UserCountry, $UserCity, $UserActive) {
		return true;
	}
	
	public function clearOldLocks() {
		$deltime = gmdate('c', time() - 300);
		$sQuery = "DELETE FROM UserLockList WHERE SessionId='" . $this->sSessionId . "' AND LastUpdate<='" . $deltime . "'";
		$this->q->core->query($sQuery);
	}
	
	public function isDatacardLocked($prmDisasterId) {
		// First delete old datacard locks...
		$this->clearOldLocks();
		$sReturn = '';
		$sQuery = "SELECT S.UserId FROM UserLockList U,UserSession S WHERE U.SessionId=S.SessionId AND U.RecordId='" . $prmDisasterId . "'";
		foreach ($this->q->core->query($sQuery) as $row) {
			$sReturn = $row['UserId'];
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
} //class

</script>
