<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class UserSession {
	var $sSessionId      = '';
	var $sRegionId       = '';
	var $sRegionLangCode = 'eng';
	var $sUserName       = '';
	var $dStart          = '';
	var $dLastUpdate     = '';
	
	public function __construct() {
		$this->sSessionId = session_id();
		$this->dStart = gmdate('c');
		$this->dLastUpdate = $this->dStart;
		$num_args = func_num_args();
		if ($num_args > 0) {
			if (func_get_arg(0) != "") {  $this->sSessionId = func_get_arg(0); }
			if ($num_args > 1) { $this->sRegionId = func_get_arg(1); }
		}
		$this->load($this->sSessionId);
	} //constructor
	
	// Read Session Information from Database
	public function load($prmSessionId) {
		$iReturn = 0;
		$sQuery = "SELECT * FROM UserSession WHERE SessionId='" . $prmSessionId . "'";
		$q = new Query();
		try {
		if ($result = $q->core->query($sQuery, PDO::FETCH_OBJ)) {
			while ($row = $result->fetch()) {
				$this->sSessionId  = $row->SessionId;
				$this->sRegionId   = $row->RegionId;
				$this->sUserName   = $row->UserName;
				$this->dStart      = $row->Start;
				$this->dLastUpdate = $row->LastUpdate;
				$iReturn = 1;
			}
		}
		} catch (PDOException $e) {
			$e->getMessage() . "<br>\n";
		}
		// If session doesn't exist in database, insert record
		if (! $iReturn) {
			$this->insert();
		}
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
		$q = new Query();
		if ($result = $q->core->query($sQuery)) {
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
		$q = new Query();
		if ($result = $q->core->query($sQuery)) {
			$iReturn = 1;
		}
		return $iReturn;
	} // function

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
		$q = new Query();
		if ($result = $q->core->query($sQuery)) {
			$iReturn = 1;
		}
		return $iReturn;
	} // function

	// Close a session, removing the session information from the
	// database.	
	public function delete() {
		$iReturn = 0;
		$sQuery = "DELETE FROM UserSession WHERE SessionId='" . $this->sSessionId . "'";
		$q = new Query();
		if ($result = $q->core->query($sQuery)) {
			$this->sUserName = "";
			$this->sRegionId = "";
			$iReturn = 1;
		}
		return $iReturn;
	} // function

	// Associate a RegionId with the session
	public function open($prmRegionId) {
		$iReturn = 0;
		$sQuery = 
		  "UPDATE UserSession SET RegionId='" . $prmRegionId . "' " . 
		  "WHERE SessionId='" . $this->sSessionId . "'";
		$q = new Query();
		if ($result = $q->core->query($sQuery)) {
			$this->sRegionId = $prmRegionId;
			$sQuery = "SELECT * FROM Region WHERE RegionId='" . $this->sRegionId . "'";
			if ($result = $q->core->query($sQuery)) {
				while ($row = $result->fetch(PDO::FETCH_OBJ)) {
					$sRegionLangCode = $row->LangIsoCode;
				}
			}
			$iReturn = 1;
		}
		$this->awake();
		return $iReturn;
	} // function

	public function close($prmRegionId) {
		return $this->open("");
	} // function

	// Validate a user/passwd pair against database
	public function validateUser($prmUserName, $prmUserPasswd) {
		$iReturn = 0;
		if ( ($prmUserName == "") && ($prmUserPasswd == "")) {
			// This is an anonymous session
			$iReturn = 1;
		} else {
			$q = new Query();
			$sQuery = "SELECT * FROM User WHERE UserName='" . $prmUserName . "'";
			try {
				$result = $q->core->query($sQuery);
				while ($row = $result->fetch(PDO::FETCH_OBJ)) {
					if ($row->UserPasswd == $prmUserPasswd) {
						$iReturn = 1;
					}
				} // while
			} catch (PDOException $e) {
				print $e->getMessage();
			} // catch
		}
		return $iReturn;
	} // function
	
	public function getUserFullName() {
		$sUserFullName = "";
		$q = new Query();
		if ($result = $q->core->query("SELECT * FROM User WHERE UserName='" . $this->sUserName . "'") ) {
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
		$q = new Query();
		if ($result = $q->core->query($sQuery) ) {
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
		
		$q = new Query();
		if ($result = $q->core->query($sQuery) ) {
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
		$q = new Query();
		if ($result = $q->core->query($sQuery) ) {
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
		$q = new Query();
		if ($result = $q->core->query($sQuery) ) {
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
		$q = new Query();
		if ($result = $q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$myAnswer = $row->AuthAuxValue;
			} // while
		}
		return $myAnswer;
	} // function
	
	public function setUserRole($prmUserName, $prmRegionId, $prmRole) {
		$myAnswer = '';
		$sWhereQuery = " WHERE (RegionId='" . $prmRegionId . "'" .
		              "   AND (UserName='" . $prmUserName . "'" .
		              "   AND (Authkey='ROLE') ";
		$sQuery = "SELECT * FROM RegionAuth " . $sWhereQuery;
		$q = new Query();
		if ($result = $q->core->query($sQuery) ) {
			if ($result->num_rows == 0) {
				$sQuery = "INSERT INTO RegionAuth VALUES (" .
					"'" . $prmUserName . "', " .
					"'" . $prmRegionId . "', " .
					"'ROLE', 0, " .
					"'" . $prmRole . "'" .
				  ") ";
				  $myAnswer = $prmRole;
			} else {
				$sQuery = "UPDATE RegionAuth SET " .
				  " AuthAuxValue = '" . $prmRole . "' " .  $sWhereQuery;
				if ($result = $q->core->query($sQuery) ) {
					$myAnswer = $prmRole;
				}
			} // else
		}
		return $myAnswer;
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
		$q = new Query();
		if ($result = $q->core->query($sQuery) ) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
			 $sKey = $row->RegionId;
			 $sValue = $row->RegionLabel;
			 $myData[$sKey] = $sValue;
			} // while
		}
		return $myData;
	} // function
} //class
