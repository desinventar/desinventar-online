<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class UserSession {
	var $q               = null;
	var $sSessionId      = '';
	var $sRegionId       = '';
	var $LangIsoCode = 'eng';
	var $sUserName       = '';
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
			if ($num_args > 1) { 
				$this->sRegionId = func_get_arg(1);
			}
		}
		$this->load($this->sSessionId);
	} //constructor
	
	// Read Session Information from Database
	public function load($prmSessionId) {
		$iReturn = 0;
		$sQuery = "SELECT * FROM UserSession WHERE SessionId='" . $prmSessionId . "'";
		try {
			if ($result = $this->q->core->query($sQuery, PDO::FETCH_OBJ)) {
				while ($row = $result->fetch()) {
					$this->sSessionId  = $row->SessionId;
					$this->sRegionId   = $row->RegionId;
					$this->sUserName   = $row->UserName;
					$this->dStart      = $row->Start;
					$this->dLastUpdate = $row->LastUpdate;
					$iReturn = 1;
				}
			}
		} catch (Exception $e) {
			showErrorMsg($e->getMessage());
		}
		// If session doesn't exist in database, insert record
		if (! $iReturn) {
			$this->insert();
		}
		$this->q = new Query($this->sRegionId);
		return $iReturn;	
	} // function

	// Set LastUpdate field of Session so it will not expire...
	public function awake() {
		$iReturn = 1;
		$this->dLastUpdate = gmdate('c');
		$iReturn = $this->update();
		return $iReturn;
	}

	public function login($prmUserName, $prmUserPasswd) {
		$iReturn = 0;
		if ($this->validateUser($prmUserName, $prmUserPasswd) > 0) {
			$iReturn = $this->setUser($prmUserName);
		}
		return $iReturn;
	}

	public function logout() {
		return $this->setUser("");
	}

	public function setUser($prmUserName) {
		$iReturn = 0;
		$sQuery = 
		  "UPDATE UserSession SET UserName='" . $prmUserName . "' " .
		  "WHERE SessionId='" . $this->sSessionId . "'";
		if ($result = $this->q->core->query($sQuery)) {
			$iReturn = 1;
			$this->sUserName = $prmUserName;
		}
		return $iReturn;
	}

	// Start a Session by creating a record in the database
	// this could be an anonymous or authenticated session
	public function insert() {
		$iReturn = 0;
		$sQuery = "INSERT INTO UserSession VALUES (" . 
				  "'" . $this->sSessionId . "'," .
				  "'" . $this->sRegionId  . "'," .
				  "'" . $this->sUserName  . "'," .
				  "1," .
				  "'" . $this->dStart     . "'," .
				  "'" . $this->dLastUpdate . "'" .
				  ")";
		if ($result = $this->q->core->query($sQuery)) {
			$iReturn = 1;
		}
		return $iReturn;
	} // insert()

	// Update information about this session in database
	public function update() {
		$iReturn = 0;
		// Always update this field...
		$this->dLastUpdate = gmdate('c');
		$sQuery = "UPDATE UserSession SET " . 
				  "RegionId ='"  . $this->sRegionId  . "'," .
				  "UserName='"   . $this->sUserName  . "'," .
				  "Valid=1," .
				  "Start='"      . $this->dStart     . "'," .
				  "LastUpdate='" . $this->dLastUpdate . "'" .
				  "WHERE SessionId ='" . $this->sSessionId . "'";
		if ($result = $this->q->core->query($sQuery)) {
			$sQuery = "UPDATE UserLockList SET LastUpdate='" . $this->dLastUpdate . "' WHERE SessionId='" . $this->sSessionId . "'";
			$this->q->core->query($sQuery);
			$iReturn = 1;
		}
		return $iReturn;
	} // update()

	// Close a session, removing the session information from the
	// database.	
	public function delete() {
		$iReturn = 0;
		$sQuery = "DELETE FROM UserSession WHERE SessionId='" . $this->sSessionId . "'";
		if ($result = $this->q->core->query($sQuery)) {
			$this->sUserName = "";
			$this->sRegionId = "";
			$iReturn = 1;
		}
		return $iReturn;
	} // delete()

	// Associate a RegionId with the session
	public function open($prmRegionId) {
		$iReturn = ERR_NO_ERROR;
		$this->clearLocks();
		$sQuery = "UPDATE UserSession SET RegionId='" . $prmRegionId . "' " . 
		          "WHERE SessionId='" . $this->sSessionId . "'";
		if ($result = $this->q->core->query($sQuery)) {
			$this->sRegionId = $prmRegionId;
		}
		$this->awake();
		$this->q = new Query($this->sRegionId);
		return $iReturn;
	} // open()

	public function close($prmRegionId) {
		return $this->open("");
	} // close()

	// Validate a user/passwd pair against database
	public function validateUser($prmUserName, $prmUserPasswd) {
		$iReturn = 0;
		if ( ($prmUserName == "") && ($prmUserPasswd == "")) {
			// This is an anonymous session
			$iReturn = 1;
		} else {
			$sQuery = "SELECT * FROM User WHERE UserName='" . $prmUserName . "'";
			try {
				$result = $this->q->core->query($sQuery);
				while ($row = $result->fetch(PDO::FETCH_OBJ)) {
					if ($row->UserPasswd == $prmUserPasswd) {
						$iReturn = 1;
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
		if ($result = $this->q->core->query("SELECT * FROM User WHERE UserName='" . $this->sUserName . "'") ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$sUserFullName = $row->UserFullName;
			} // while
		}
		return $sUserFullName;
	}
	
	public function getAllPermsByUser() {
		return $this->getAllPermsGeneric($this->sUserName, "");
	}
	
	public function getAllPermsByRegion() {
		return $this->getAllPermsGeneric($this->sUserName, $this->sRegionId);
	}
	
	private function getAllPermsGeneric($prmUserName, $prmRegionId) {
		$myPerms = array();
		$sQuery = "SELECT * FROM RegionAuth WHERE " .
		  "((UserName='" . $prmUserName . "') OR (UserName='')) ";
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
		  " AND (UserName='" . $this->sUserName . "') " .
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

	// Get basic user info: user=>[email,pass,name,org,country,city,creadate,iplist,notes,active]
	function getUserInfo ($username) {
		$myData = array();
		$sQuery = "SELECT * FROM User " .
		  " WHERE UserName='" . $this->sUserName . "'" .
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
		return array($this->sUserName => $myData);
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
			     "  UserName : $row->UserName\n" .
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
		  " AND (UserName='" . $this->sUserName . "') " .
		  " AND AuthKey='ROLE'" . 
		  " ORDER BY UserName,RegionId";
		if ($result = $this->q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$myAnswer = $row->AuthAuxValue;
			} // while
		}
		return $myAnswer;
	} // function
	
	public function setUserRole($prmUserName, $prmRegionId, $prmRole) {
		$iReturn = 1;
		if ($prmUserName == '') { $iReturn = -1; }
		if ($prmRegionId == '') { $iReturn = -1; }
		
		if ($iReturn > 0) {
			// Remove All Permissions for This User on This Database
			$sQuery = "DELETE FROM RegionAuth WHERE " .
				" UserName='" . $prmUserName . "' AND " . 
				" RegionId='" . $prmRegionId . "'";
			$this->q->core->query($sQuery);
			
			// Create Role Permission
			$sQuery = "INSERT INTO RegionAuth (UserName,RegionId,AuthKey,AuthValue,AuthAuxValue) " .
				" VALUES ('" . $prmUserName . "','" . $prmRegionId . "','ROLE',0,'" . $prmRole . "')";
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
				$this->setPerm($prmUserName, $prmRegionId, $k, $v[0], $v[1]);
			}
		}
		return $iReturn;
	} //function
	
	public function setPerm($prmUserName, $prmRegionId, $prmAuthKey, $prmValue, $prmAuxValue) {
		$sQuery = "INSERT INTO RegionAuth VALUES (" . 
			"'" . $prmUserName . "','" . $prmRegionId  . "'," .
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
	
	public function existUser($username) {
		return true;
	}
	
	public function insertUser($UserName, $UserFullName, $UserEMail, $UserPasswd, $UserCountry, $UserCity, $UserActive) {
		return true;
	}
	
	public function updateUser($UserName, $UserFullName, $UserEMail, $UserPasswd, $UserCountry, $UserCity, $UserActive) {
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
		$sQuery = "SELECT S.UserName FROM UserLockList U,UserSession S WHERE U.SessionId=S.SessionId AND U.RecordId='" . $prmDisasterId . "'";
		foreach ($this->q->core->query($sQuery) as $row) {
			$sReturn = $row['UserName'];
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
	
	public function getUsersList($prmUserName) {
		$list = array();
		$sQuery = "SELECT * FROM User WHERE UserActive > 0 ";
		if ($prmUserName != '') {
			$sQuery .= " AND UserName='" . $prmUserName . "'";
		}
		$sQuery .= " ORDER BY UserFullName";
		foreach($this->q->core->query($sQuery) as $row) {
			$list[$row['UserName']]=$row['UserFullName'];
		}
		return $list;
	}
} //class

</script>
