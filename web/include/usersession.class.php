<?php
/*
  DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

define('ROLE_NONE'       , 0);
define('ROLE_OBSERVER'   , 1);
define('ROLE_USER'       , 2);
define('ROLE_SUPERVISOR' , 3);
define('ROLE_ADMINREGION', 4);
define('ROLE_ADMINPORTAL', 5);

class UserSession
{

	public function __construct()
	{
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
		$this->config = array();
		$this->config['AutoLogin'] = 0;
		$num_args = func_num_args();
		if ($num_args > 0)
		{
			if (func_get_arg(0) != '')
			{  
				$this->sSessionId = func_get_arg(0);
			}
		}
		$this->load($this->sSessionId);
	} //constructor
	
	// Read Session Information from Database
	public function load($prmSessionId)
	{
		$iReturn = ERR_UNKNOWN_ERROR;
		$sQuery = 'SELECT * FROM UserSession WHERE SessionId=:SessionId';
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->bindParam(':SessionId', $prmSessionId, PDO::PARAM_STR);
			$sth->execute();
			$this->q->core->commit();
			while($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$this->sSessionId  = $row['SessionId'];
				$this->UserId      = $row['UserId'];
				$this->LangIsoCode = $row['LangIsoCode'];
				$this->dStart      = $row['Start'];
				$this->dLastUpdate = $row['LastUpdate'];
				$iReturn = ERR_NO_ERROR;
			} //while
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg($e->getMessage());
		}
		// If session doesn't exist in database, insert record
		if ($iReturn < 0)
		{
			$this->insert();
		}
		return $iReturn;	
	} // function

	// Set LastUpdate field of Session so it will not expire...
	public function awake()
	{
		$iReturn = ERR_NO_ERROR;
		$PrevTime = $this->dLastUpdate;
		$CurTime  = gmdate('c');
		$this->dLastUpdate = $CurTime;
		
		// 2009-10-23 (jhcaiced) Logout users when too much time has passed
		// without using this session (Bug # 277)
		$Interval = strtotime($CurTime) - strtotime($PrevTime);
		// 60 * 30 = 1800 (30 minutes)
		if ($Interval > 1800)
		{
			$this->close();
			$this->logout();
		}
		$sQuery1 = 'UPDATE UserSession SET LastUpdate=:LastUpdate WHERE SessionId=:SessionId';
		$sth1 = $this->q->core->prepare($sQuery1);
		$sQuery2 = 'UPDATE UserLockList SET LastUpdate=:LastUpdate WHERE SessionId=:SessionId';
		$sth2 = $this->q->core->prepare($sQuery2);
		try
		{
    		$this->q->core->beginTransaction();
			$sth1->bindParam(':SessionId', $this->sSessionId, PDO::PARAM_STR);
			$sth1->bindParam(':LastUpdate', $this->dLastUpdate, PDO::PARAM_STR);
			$sth1->execute();
			$sth2->bindParam(':SessionId', $this->sSessionId, PDO::PARAM_STR);
			$sth2->bindParam(':LastUpdate', $this->dLastUpdate, PDO::PARAM_STR);
			$sth2->execute();
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg($e->getMessage());
		}
		return $iReturn;
	}

	public function login($prmUserId, $prmUserPasswd)
	{
		$iReturn = ERR_DEFAULT_ERROR;
		$UserId = $this->validateUser($prmUserId, $prmUserPasswd);
		if ($UserId != '')
		{
			$iReturn = $this->setUser($UserId);
		}
		return $iReturn;
	}

	public function logout()
	{
		return $this->setUser('');
	}

	public function setUser($prmUserId)
	{
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = 'UPDATE UserSession SET UserId=:UserId ' . 
		          'WHERE SessionId=:SessionId';
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			if ($sth->execute(array(':UserId'    => $prmUserId,
			                    ':SessionId' => $this->sSessionId)))
			{
				$iReturn = ERR_NO_ERROR;
				$this->UserId = $prmUserId;
			}
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg($e->getMessage());
		}
		return $iReturn;
	} //setUser()

	public function doUserAutoLoginCheck()
	{
		$iReturn = ERR_DEFAULT_ERROR;
		if ($this->UserId == '')
		{
			$clientAddr = trim($_SERVER['REMOTE_ADDR']);
			$addrList = '127.0.0.1';
			$addrList2 = '';
			if (isset($_SERVER['DESINVENTAR_AUTOLOGIN_ADDRLIST']))
			{
				$addrList2 = $_SERVER['DESINVENTAR_AUTOLOGIN_ADDRLIST'];
			}
			$addrList2 = preg_replace('/ +/',',', $addrList2);
			$addrList2 = preg_replace('/,+/',',', $addrList2);
			$addrList = explode(',', $addrList . ',' . $addrList2);
			foreach($addrList as $key => $addr)
			{
				if ($addr == '')
				{
					unset($addrList[$key]);
				}
			}//foreach
			$bAutoLogin = in_array($clientAddr, $addrList);			
			if ($bAutoLogin)
			{
				$iReturn = ERR_NO_ERROR;
			}
		}
		return $iReturn;
	} //doUserAutoLoginCheck()

	public function doUserAutoLogin()
	{
		$iReturn = ERR_NO_ERROR;
		$UserId = 'root';
		$this->setUser($UserId);
		$this->config['AutoLogin'] = 1;
		return $iReturn;
	} //doUserAutoLogin()
	
	public function setLangIsoCode($prmLangIsoCode) 
	{
		$this->LangIsoCode = $prmLangIsoCode;
	}

	// Start a Session by creating a record in the database
	// this could be an anonymous or authenticated session
	public function insert()
	{
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = 'INSERT INTO UserSession (SessionId,RegionId,UserId,Valid,LangIsoCode,Start,LastUpdate) ' .
		          ' VALUES (:SessionId,:RegionId,:UserId,:Valid,:LangIsoCode,:Start,:LastUpdate)';
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->bindParam(':SessionId'  , $this->sSessionId, PDO::PARAM_STR);
			$sth->bindValue(':RegionId'   , '', PDO::PARAM_STR);
			$sth->bindParam(':UserId'     , $this->UserId, PDO::PARAM_STR);
			$sth->bindValue(':Valid'      , 1, PDO::PARAM_INT);
			$sth->bindParam(':LangIsoCode', $this->LangIsoCode, PDO::PARAM_STR);
			$sth->bindParam(':Start'      , $this->dStart, PDO::PARAM_STR);
			$sth->bindParam(':LastUpdate' , $this->dLastUpdate, PDO::PARAM_STR);
			if ($result = $sth->execute())
			{
				$iReturn = ERR_NO_ERROR;
			}
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg($e->getMessage());
		}
		return $iReturn;
	} // insert()

	// Update information about this session in database
	public function update()
	{
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
		$sQuery = 'UPDATE UserSession SET LangIsoCode="' . $this->LangIsoCode . '" WHERE SessionId="' . $this->sSessionId . '"';
		$sth1 = $this->q->core->prepare($sQuery);
		$sQuery = "UPDATE UserLockList SET LastUpdate='" . $this->dLastUpdate . "' WHERE SessionId='" . $this->sSessionId . "'";
		$sth2 = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->bindParam(':SessionId'  , $this->sSessionId , PDO::PARAM_STR);
			$sth->bindValue(':RegionId'   , ''                , PDO::PARAM_STR);
			$sth->bindParam(':UserId'     , $this->UserId     , PDO::PARAM_STR);
			$sth->bindValue(':Valid'      , 1                 , PDO::PARAM_INT);
			$sth->bindParam(':LangIsoCode', $this->LangIsoCode, PDO::PARAM_STR);
			$sth->bindParam(':Start'      , $this->dStart     , PDO::PARAM_STR);
			$sth->bindParam(':LastUpdate' , $this->dLastUpdate, PDO::PARAM_STR);
			$sth->execute();
			$sth1->execute();
			$sth2->execute();
			$this->q->core->commit();
			$iReturn = ERR_NO_ERROR;
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg($e->getMessage());
		}
		return $iReturn;
	} // update()

	// Close a session, removing the session information from the
	// database.
	public function delete()
	{
		$iReturn = ERR_DEFAULT_ERROR;
		$sQuery = 'DELETE FROM UserSession WHERE SessionId=:SessionId';
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->bindParam(':SessionId'  , $this->sSessionId, PDO::PARAM_STR);
			$sth->execute();
			$this->q->core->commit();
			$this->UserId = '';
			$iReturn = ERR_NO_ERROR;
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg($e->getMessage());
		}
		return $iReturn;
	} // delete()

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
		if ($prmDBFile == '')
		{
			$DBFile = $DBDir . '/desinventar.db';
		}
		else
		{
			$DBFile = $DBDir . '/' . $prmDBFile;
		}
		if (! file_exists($DBFile))
		{
			$iReturn = ERR_NO_DATABASE;
		}		
		if ($iReturn > 0)
		{
			$this->awake();
			$this->q->setDBConnection($prmRegionId, $DBFile);
			$this->conn = $this->q->dreg;
			$this->RegionId = $prmRegionId;
			$this->UserRole = $this->getUserRole($prmRegionId);
			$this->UserRoleValue = $this->getUserRoleValue($prmRegionId);
			$this->RegionLangIsoCode = $this->getDBInfoValue('LangIsoCode');
		} 
		return $iReturn;
	} // open()

	public function close($prmRegionId = '')
	{
		return $this->open('');
	} // close()

	// Validate a user/passwd pair against database
	public function validateUser($prmUserId, $prmUserPasswd, $withCrypt=true)
	{
		$UserId = '';
		if (! $withCrypt)
		{
			$prmUserPasswd = md5($prmUserPasswd);
		}
		$sQuery = 'SELECT * FROM User WHERE (UserId=:UserId OR UserNotes LIKE :UserNotes) AND UserPasswd=:UserPasswd';
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->bindParam(':UserId', $prmUserId, PDO::PARAM_STR);
			$sth->bindValue(':UserNotes', '%(UserName=' . $prmUserId. ')%', PDO::PARAM_STR);
			$sth->bindParam(':UserPasswd', $prmUserPasswd, PDO::PARAM_STR);
			$sth->execute();
			$this->q->core->commit();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$UserId = $row['UserId'];
			}
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg($e->getMessage());
		} // catch
		return $UserId;
	} // valiteUser
	
	public function getUserFullName()
	{
		$sUserFullName = '';
		$sQuery = "SELECT * FROM User WHERE UserId='" . $this->UserId . "'";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$sUserFullName = $row['UserFullName'];
			} //while
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('getUserFullName Error : ' . $e->getMessage());
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
		if ($prmRegionId != "")
		{
			$sQuery .= " AND " . "((RegionId='" . $prmRegionId . "') OR (RegionId='')) ";
		}
		$sQuery = $sQuery + " ORDER BY AuthKey,AuthValue";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			$i = 0;
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$sAuthKey   = $row['AuthKey'];
				$sAuthValue = $row['AuthValue'] . "/" . $row['AuthAuxValue'];
				$myPerms[$sAuthkey] = $sAuthValue;
			} // while
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('getAllPermsGeneric Error : ' . $e->getMessage());
		}
		return $myPerms;
	}

	// Return hash with all regions where the user has a role
	function getUserRoleList()
	{
		$myData = array();
		$sQuery = "SELECT RegionAuth.*,Region.RegionLabel FROM RegionAuth,Region WHERE " .
			" (RegionAuth.RegionId = Region.RegionId) " .
			" AND (UserId='" . $this->UserId . "') " .
			" AND AuthKey='ROLE'" . 
			" ORDER BY RegionAuth.RegionId";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$sKey   = $row['RegionId'];
				$sValue = $row['AuthAuxValue'];
				$myData[$sKey]['Role']        = $row['AuthAuxValue'];
				$myData[$sKey]['RegionLabel'] = $row['RegionLabel'];
			} // while
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('getUserRoleList Error : ' . $e->getMessage());
		}
		return $myData;
	} // function

	// Return hash with all users of a Region with a role
	function getRegionRoleList($prmRegionId='', $prmRoleId='')
	{
		if ($prmRegionId == '')
		{
			$prmRegionId = $this->RegionId;
		}
		$myData = array();
		$sQuery = 'SELECT RegionAuth.*,Region.RegionLabel,User.UserFullName AS UserFullName FROM RegionAuth,Region,User WHERE'  .
			' (RegionAuth.RegionId = Region.RegionId)' .
			' AND (RegionAuth.UserId = User.UserId)' .
			' AND (Region.RegionId="' . $prmRegionId . '")' .
			' AND RegionAuth.AuthKey="ROLE"';
		if ($prmRoleId == '')
		{
			$sQuery .= ' AND RegionAuth.AuthAuxValue != "NONE"';
		}
		else
		{
			$sQuery .= ' AND RegionAuth.AuthAuxValue == "' . $prmRoleId . '"';
		}
		$sQuery .= ' ORDER BY RegionAuth.RegionId, User.UserFullName';
		$sth = $this->q->core->prepare($sQuery);
		try
		{
            $this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$sKey = $row['UserId'];
				$sValue = array(
					'UserId'   => $row['UserId'],
					'UserRole' => $row['AuthAuxValue'],
					'UserName' => $row['UserFullName']
				);
				$myData[$sKey] = $sValue;
			} // while
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('getRegionRoleList Error : ' . $e->getMessage());
		}
		/*
		if ($prmRoleId != 'ADMINREGION')
		{
			foreach($myData as $UserId => $RoleInfo)
			{
				if ($RoleInfo['UserRole'] == 'ADMINREGION')
				{
					unset($myData[$UserId]);
				}
			}
		}
		*/
		return $myData;
	} // getRegionRoleList()

	function getRegionUserAdminInfo($prmRegionId)
	{
		$UserInfo = array();
		$RoleList = $this->getRegionRoleList($prmRegionId, 'ADMINREGION');
		foreach($RoleList as $UserId => $UserRole)
		{
			$UserFullInfo = $this->getUserInfo($UserId);
			foreach(array('UserId','UserFullName','UserEMail') as $key)
			{
				$UserInfo[$key] = $UserFullInfo[$UserId][$key];
			}
		}
		return $UserInfo;
	} // getRegionUserAdminInfo()
	
	// Get basic user info: user=>[email,pass,name,org,country,city,creadate,iplist,notes,active]
	function getUserInfo($prmUserId)
	{
		$myData = array();
		$sQuery = "SELECT * FROM User";
		if ($prmUserId != '')
		{
			$sQuery .= " WHERE UserId='" . $prmUserId . "'";
		}
		$sQuery .= " ORDER BY UserFullName";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
			$sth->execute();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
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
		}
		catch (Exception $e)
		{
			showErrorMsg('ERROR getUserInfo : ' . $e->getMessage());
		}
		return $myData;
	}
	
	// Send a Password Reminder to an E-mail
	function sendPasswdReminder($prmEMail)
	{
		$myAnswer = '';
		$sQuery = "SELECT * FROM User WHERE (UserEMail='" . $prmEMail . "') ";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
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
			} // while
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showStatusMsg('ERROR sendPasswdReminder : ' . $e->getMessage());
		}
		return $myAnswer;
	}

	// Return Role for a Region
	function getUserRole($prmRegionId='')
	{
		$UserRole = 'NONE';
		if ($prmRegionId == '')
		{
			$prmRegionId = $this->RegionId;
		}
		if ($this->UserId == 'root')
		{
			$UserRole = 'ADMINPORTAL';
		}
		else
		{
			$UserRole = 'NONE';
			$sQuery = "SELECT * FROM RegionAuth WHERE ";
			if ($prmRegionId != '')
			{
				$sQuery .= "((RegionId='') OR (RegionId='" . $prmRegionId . "'))";
			}
			else
			{
				$sQuery .= "(RegionId='')";
			}
			$sQuery .= " AND (UserId='" . $this->UserId . "') " .
					   " AND AuthKey='ROLE'" . 
					   " ORDER BY UserId,RegionId";
			$sth = $this->q->core->prepare($sQuery);
			try
			{
    			$this->q->core->beginTransaction();
				$sth->execute();
				$this->q->core->commit();
				while ($row = $sth->fetch(PDO::FETCH_OBJ))
				{
					$UserRole = $row->AuthAuxValue;
				} // while
				$sth->closeCursor();
			}
			catch (Exception $e)
			{
				$this->q->core->rollBack();
				showErrorMsg('ERROR getUserRole : ' . $e->getMessage());
			}
			if ($UserRole == 'NONE')
			{
				$RegionStatus = $this->getDBInfoValue('RegionStatus');
				$RegionPublic = $RegionStatus & 2;
				if ($RegionPublic > 0)
				{
					$UserRole = 'OBSERVER';
				}
			}
		}
		return $UserRole;
	} // function

	function convertRoleToRoleValue($prmRole)
	{
		$NumRole = ROLE_NONE;
		if ($prmRole == 'NONE')        { $NumRole = ROLE_NONE;        }
		if ($prmRole == 'OBSERVER')    { $NumRole = ROLE_OBSERVER;    }
		if ($prmRole == 'USER')        { $NumRole = ROLE_USER;        }
		if ($prmRole == 'SUPERVISOR')  { $NumRole = ROLE_SUPERVISOR;  }
		if ($prmRole == 'ADMINREGION') { $NumRole = ROLE_ADMINREGION; }
		if ($prmRole == 'ADMINPORTAL') { $NumRole = ROLE_ADMINPORTAL; }
		return $NumRole;
	}
		
	// Get User Role as a Numeric Value, easier to compare
	function getUserRoleValue($prmRegionId = '')
	{
		$Role = $this->getUserRole($prmRegionId);
		$NumRole = $this->convertRoleToRoleValue($Role);
		return $NumRole;
	}
	
	public function setUserRole($prmUserId, $prmRegionId, $prmRole)
	{
		$iReturn = ERR_NO_ERROR;
		if ($prmUserId   == '')
		{
			$iReturn = ERR_DEFAULT_ERROR;
		}
		if ($prmRegionId == '')
		{
			$iReturn = ERR_DEFAULT_ERROR;
		}
		if ($iReturn > 0)
		{
			// Remove All Permissions for This User on This Database
			$sQuery = "DELETE FROM RegionAuth WHERE UserId='" . $prmUserId . "' AND RegionId='" . $prmRegionId . "'";
			$sth = $this->q->core->prepare($sQuery);
			try
			{
    			$this->q->core->beginTransaction();
				$sth->execute();
				$this->q->core->commit();
			}
			catch (Exception $e)
			{
				$this->q->core->rollBack();
				showErrorMsg('ERROR setUserRole : '. $e->getMessage());
			}
		}

		if ($iReturn > 0)
		{
			// Insert ROLE Permission First
			if ($prmRole <> 'NONE')
			{
				$this->setPerm($prmUserId, $prmRegionId, 'ROLE', 0, $prmRole);
			}
			switch($prmRole)
			{
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
	
	public function setPerm($prmUserId, $prmRegionId, $prmAuthKey, $prmValue, $prmAuxValue)
	{
		$sQuery = "INSERT INTO RegionAuth VALUES (" . 
			"'" . $prmUserId . "','" . $prmRegionId  . "'," .
			"'" . $prmAuthKey . "','" . $prmValue . "','" . $prmAuxValue . "')";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR setPerm : ' . $e->getMessage());
		}
	}

	public function getRegionList($prmCountryIsoCode, $prmStatus)
	{
		if (!empty($prmCountryIsoCode))
		{
			$opt = " CountryIsoCode='$prmCountryIsoCode'";
		}
		else
		{
			$opt = " 1=1";
		}
		if ($prmStatus == "ACTIVE")
		{
			$opt .= " AND RegionActive = True";
		}
		$sQuery = "SELECT RegionId, RegionLabel FROM Region " .
		          " WHERE $opt ORDER BY RegionLabel";
		$myData = array();
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			while ($row = $sth->fetch(PDO::FETCH_OBJ))
			{
				$sKey = $row->RegionId;
				$sValue = $row->RegionLabel;
				$myData[$sKey] = $sValue;
			} // while
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR getRegionList : ' . $e->getMessage());
		}
		return $myData;
	} // function
	
	public function doUserExist($prmUserId)
	{
		$Answer = ERR_UNKNOWN_ERROR;
		$Query = "SELECT UserId FROM User WHERE UserId='" . $prmUserId . "'";
		$sth = $this->q->core->prepare($Query);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$Answer = ERR_NO_ERROR;
			}
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR doUserExist : ' . $e->getMessage());
		}
		return $Answer;
	}
	
	public function insertUser($UserId, $UserFullName, $UserEMail, $UserPasswd, 
	                           $UserCountry, $UserCity, $UserActive)
	{
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
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			$iReturn = ERR_NO_ERROR;
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR insertUser : ' . $e->getMessage());
		}
		return $iReturn;
	}
	
	public function updateUser($UserId, $UserFullName, $UserEMail, $UserPasswd,
	                           $UserCountry, $UserCity, $UserActive)
	{
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
		{
			$sQuery .= ", UserPasswd='" . md5($UserPasswd) . "'";
		}
		$sQuery .=  " WHERE UserId='" . $UserId . "'";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			$iReturn = ERR_NO_ERROR;
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR updateUser : ' . $e->getMessage());
		}
		return $iReturn;
	}
	
	public function updateUserPasswd($UserId, $UserPasswd)
	{
		$iReturn = ERR_DEFAULT_ERROR;
		$Query = 'UPDATE User SET UserPasswd="' . $UserPasswd . '" WHERE UserId="' . $UserId . '"';
		$sth = $this->q->core->prepare($Query);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			$iReturn = ERR_NO_ERROR;
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR updateUserPasswd : ' . $e->getMessage());
		}
		return $iReturn;
	}
	
	public function clearOldLocks()
	{
		$deltime = gmdate('c', time() - 600);
		$sQuery = "DELETE FROM UserLockList WHERE LastUpdate<='" . $deltime . "'";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR clearOldLocks : ' . $e->getMessage());
		}
	}
	
	public function isDatacardLocked($prmDisasterId)
	{
		$sReturn = '';
		$sQuery = 'SELECT * FROM UserLockList WHERE RecordId=:RecordId';
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute(array(':RecordId' => $prmDisasterId));
			$this->q->core->commit();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$sReturn = $row['SessionId'];
			}
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR isDatacardLocked : ' . $e->getMessage());
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
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute(array(
				'SessionId' => $this->sSessionId,
				'LockType' => 'DISASTER',
				'RecordId' => $prmDisasterId,
				'LockTime' => $now
			));
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR lockDatacard : ' . $e->getMessage());
		}
	}
	
	public function releaseDatacard($prmDisasterId)
	{
		$sQuery = 'DELETE FROM UserLockList WHERE SessionId=:SessionId AND RecordId=:RecordId';
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute(array(
				'SessionId' => $this->sSessionId,
				'RecordId' => $prmDisasterId
			));
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR releaseDatacard : ' . $e->getMessage());
		}
	}

	public function clearLocks()
	{
		$sQuery = "DELETE FROM UserLockList WHERE SessionId='" . $this->sSessionId . "'";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR clearLocks : ' . $e->getMessage());
		}
	}
	
	public function getUserList($prmUserId)
	{
		$list = array();
		$sQuery = "SELECT * FROM User WHERE UserActive > 0 ";
		if ($prmUserId != '')
		{
			$sQuery .= " AND UserId='" . $prmUserId . "'";
		}
		$sQuery .= " ORDER BY UserFullName";
		$sth = $this->q->core->prepare($sQuery);
		try
		{
    		$this->q->core->beginTransaction();
			$sth->execute();
			$this->q->core->commit();
			while($row = $sth->fetch(PDO::FETCH_ASSOC))
			{
				$list[$row['UserId']]=$row['UserFullName'];
			}
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->core->rollBack();
			showErrorMsg('ERROR getUserList : ' . $e->getMessage());
		}
		return $list;
	}
	
	public function chkPasswd ($passwd)
	{
		return true;
	}

	public function listDB()
	{
		$RegionList = array();
		$query = "SELECT R.RegionId AS RegionId, R.RegionLabel AS RegionLabel, R.CountryIso AS CountryIso, R.RegionStatus AS RegionStatus, ".
			"RA.AuthAuxValue AS Role FROM Region AS R, RegionAuth AS RA WHERE R.RegionId = RA.RegionId ";
		if ($this->UserId)
		{
			$query .= " AND RA.AuthKey = 'ROLE' AND RA.UserId = '". $this->UserId ."'";
		}
		else
		{
			$query .= " AND R.RegionStatus = 3 GROUP BY R.RegionId";
		}
		$query .= " ORDER BY R.CountryIso, R.RegionLabel, R.RegionOrder";
		$result = $this->q->core->query($query);
		while ($row = $result->fetch(PDO::FETCH_OBJ))
		{
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
		foreach($this->q->core->query($sQuery) as $row)
		{
			if ($Index > 0)
			{
				$List .= ',';
			}
			$List .= '"' . $row['CountryIso'] . '"';
			$Index++;
		}
		$sQuery = 'SELECT CountryIso,CountryName FROM Country WHERE CountryIso IN (' . $List . ')';
		$Index = 0;
		foreach($this->q->base->query($sQuery) as $row)
		{
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
		if ($this->UserId != 'root')
		{
			$query .= 'RegionStatus=3';
			$Role = 'NONE';
		}
		else
		{
			$query .= 'RegionStatus>0';
			$Role = 'ADMINREGION';
		}

		if ($prmQuery != '')
		{
			if ($searchByCountry > 0)
			{
				$query .= ' AND (CountryIso = "' . $prmQuery . '")';
			}
			else
			{
				$query .= "(RegionId LIKE '%" . $prmQuery . "%' OR RegionLabel LIKE '%" . $prmQuery . "%')";
			}			
		}
		$query .= ' ORDER BY CountryIso,RegionLabel,RegionOrder';
		foreach($this->q->core->query($query) as $row)
		{
			$regionlist[$row['RegionId']] = array('RegionLabel' => $row['RegionLabel'],
			                                      'CountryIso'  => $row['CountryIso'],
			                                      'Role' => $Role);
		}
		if ($searchByCountry <= 0)
		{
			// Add Regions with specific Roles
			$query = "select R.RegionId,R.CountryIso,R.RegionLabel,RA.AuthAuxValue from Region R,RegionAuth RA where R.RegionId=RA.RegionId AND RA.AuthKey='ROLE' AND RA.UserId='" . $this->UserId . "' ORDER BY R.CountryIso,R.RegionLabel;";
			foreach($this->q->core->query($query) as $row)
			{
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
		return array('DisasterId' => $DisasterId, 'RecordNumber' => $Record, 'RecordCount' => $RecordCount, 'Status' => 'OK');
	}

	public function getDisasterIdLast()
	{
		$RecordCount = $this->getDisasterCount();
		$Record = $RecordCount;
		$DisasterId = $this->getDisasterIdFromRecordNumber($Record);
		return array('DisasterId' => $DisasterId, 'RecordNumber' => $Record, 'RecordCount' => $RecordCount, 'Status' => 'OK');
	}

	public function getDisasterIdPrev($prmRecord)
	{
		$Record = $prmRecord;
		$RecordCount = $this->getDisasterCount();
		if ($Record > 1)
		{
			$Record--;
		}
		$DisasterId = $this->getDisasterIdFromRecordNumber($Record);
		return array('DisasterId' => $DisasterId, 'RecordNumber' => $Record, 'RecordCount' => $RecordCount, 'Status' => 'OK');
	}

	public function getDisasterIdNext($prmRecord)
	{
		$Record = $prmRecord;
		$RecordCount = $this->getDisasterCount();
		if ($Record < $RecordCount)
		{
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
		try
		{
    		$this->q->dreg->beginTransaction();
			$sth->execute();
			$this->q->dreg->commit();
			$reclist = $sth->fetchAll(PDO::FETCH_ASSOC);
			$sth->closeCursor();
		}
		catch (Exception $e)
		{
			$this->q->dreg->rollBack();
			showErrorMsg($e->getMessage());
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

	// Get number of datacards by status: PUBLISHED, DRAFT, ..
	public function getNumDisasterByStatus($prm_record_status)
	{
		$query = 'SELECT COUNT(DisasterId) AS counter FROM Disaster';
		if ($prm_record_status != '')
		{
			$query_status = '';
			foreach(explode(' ', $prm_record_status) as $status)
			{
				if ($query_status != '') { $query_status .= ','; }
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
		foreach($this->q->dreg->query($sQuery) as $row)
		{
			$iCount = $row['C'];
		}
		return $iCount;
	}
		
	public function getDisasterIdFromRecordNumber($prmRecord)
	{
		$DisasterId = '';
		$prmRecord--;
		$sQuery = 'SELECT DisasterId FROM Disaster WHERE DisasterSerial<>"" ORDER BY DisasterBeginTime,DisasterSerial LIMIT ' . $prmRecord . ',1';
		foreach($this->q->dreg->query($sQuery) as $row)
		{
			$DisasterId = $row['DisasterId'];
		}
		return $DisasterId;
	}

	public function existDisasterSerial($prmDisasterSerial)
	{
		$Answer = '';
		$sQuery = "SELECT DisasterSerial FROM Disaster WHERE DisasterSerial = '". $prmDisasterSerial."'";
		foreach($this->q->dreg->query($sQuery) as $row)
		{
			$Answer = $row['DisasterSerial'];
		}
		return array('Status' => 'OK', 'DisasterSerial' => $Answer);
	}

	// Return a small list with the information about levels in database
	function getGeoLevels()
	{
		$sQuery = "SELECT GeoLevelId, GeoLevelName FROM GeoLevel WHERE GeoLevelActive=1 ORDER BY GeoLevelId";
		$GeoLevels = array();
		foreach ($this->q->dreg->query($sQuery) as $row)
		{
			array_push($GeoLevels, $row);
		}
		return $GeoLevels;
	}
	
	// Return an array with the subcomponents of a GeographyId item...
	function getGeographyItemsById($prmGeographyId)
	{
		$gItems = array();
		for($iLevel = 0; $iLevel < strlen($prmGeographyId)/5; $iLevel++)
		{
			$gId = substr($prmGeographyId, 0, ($iLevel+1)*5);
			$sQuery = 'SELECT GeographyId, GeographyName FROM Geography WHERE GeographyId="' . $gId . '"';
			foreach($this->q->dreg->query($sQuery) as $row)
			{
				$gItems[$iLevel] = $row;
			}
		} //for
		return $gItems;
	} //getGeographyItemsById()
	
	function getGeographyItemsByLevel($prmGeographyLevel, $prmGeographyParentId)
	{
		$gItems = array();
		$sQuery = 'SELECT GeographyId,GeographyName FROM Geography WHERE GeographyActive>0 AND GeographyLevel=' . $prmGeographyLevel;
		if ($prmGeographyLevel > 0)
		{
			$sQuery .= ' AND SUBSTR(GeographyId,1,' . ($prmGeographyLevel*5) . ')="' . $prmGeographyParentId . '"';
		}
		$sQuery .= ' ORDER BY GeographyName';
		foreach($this->q->dreg->query($sQuery, PDO::FETCH_ASSOC) as $row)
		{
			$gItems[$row['GeographyId']] = $row;
		} //foreach
		return $gItems;
	} //getGeographyItemsByLevel()

	public function getDBDir()
	{
		$DBDir = '';
		if ($this->RegionId != '')
		{
			$DBDir = CONST_DBREGIONDIR . '/' . $this->RegionId;
		}
		return $DBDir;
	}

	// Read an specific InfoKey value from the table
	function getDBInfoValue($prmInfoKey)
	{
		$sReturn = '';
		if ($this->RegionId != 'core')
		{
			$r = new DIRegion($this, $this->RegionId);
			$sReturn = $r->getRegionInfoValue($prmInfoKey);
		}
		return $sReturn;
	}

	public function getDateRange()
	{
		$StatusList = 'PUBLISHED';
		if ($this->UserRoleValue >= 2)
		{
			$StatusList = 'PUBLISHED READY DRAFT';
		}
		if ($this->UserRoleValue >= 4)
		{
			$StatusList = 'PUBLISHED READY DRAFT TRASH';
		}
		
		$res = array();
		$datemin = $this->getDBInfoValue('PeriodBeginDate');
		$datemax = $this->getDBInfoValue('PeriodEndDate');
		if (($datemin == '') || ($datemax == ''))
		{
			$bFirst = true;
			$statusQuery = '';
			foreach(explode(' ',$StatusList) as $status)
			{
				if (! $bFirst)
				{
					$statusQuery .= ',';
				}
				$statusQuery .= '"' . $status . '"';
				$bFirst = false;
			}
			$statusQuery = 'RecordStatus IN (' . $statusQuery . ')';
			$sQuery = 'SELECT MIN(DisasterBeginTime) AS datemin, MAX(DisasterBeginTime) AS datemax FROM Disaster '.
				'WHERE (DisasterBeginTime<>"") AND ' . $statusQuery;
			foreach($this->q->dreg->query($sQuery, PDO::FETCH_ASSOC) as $row)
			{
				if ($datemin == '' ) { $datemin = $row['datemin']; }
				if ($datemax == '' ) { $datemax = $row['datemax']; }
			}
			// 2010-01-21 (jhcaiced) Fix some weird cases in MinDate/MaxDate
			if (substr($datemin, 5, 2) == '00')
			{
				$datemin = substr($datemin, 0, 4) . '-01-01';
			}
			if (substr($datemax, 5, 2) > '12')
			{
				$datemax = substr($datemax, 0, 4) . '-12-31';
			}
		}
		if ($datemin == '') { $datemin = date('Y-m-d'); }
		if ($datemax == '') { $datemax = date('Y-m-d'); }
		if ($datemin > $datemax) {  $datemin = $datemax; }
		$res[0] = substr($datemin, 0, 10);
		$res[1] = substr($datemax, 0, 10);
		return $res;
	} //function
} //class UserSession
