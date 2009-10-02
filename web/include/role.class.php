<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2009 Corporacion OSSO
*/

class DIRole {
	var $session  = null;
	var $q        = null;
	var $UserId   = '';
	var $RegionId = '';
	public function __construct($prmSession) {
		$this->session = $prmSession;
		$this->q = $prmSession->q;
	}

	public function setPerm($prmAuthKey, $prmAuthValue, $prmAuthAuxValue) {
		$query = "INSERT INTO RegionAuth VALUES ('" . $this->UserId . "','" . $this->RegionId . "','" . $prmAuthKey . "'," . $prmAuthValue . ",'" . $prmAuthAuxValue . "')";
		fb($query);
		$this->q->core->query($query);
	}
	
	public function getUserRole($prmUserId,$prmRegionId) {
		$this->UserId   = $prmUserId;
		$this->RegionId = $prmRegionId;
		$query = "SELECT * FROM RegionAuth WHERE " .
		            "((UserId='" . $prmUserId . "') OR (UserId=''))" . " AND " .
		            "((RegionId='" . $prmRegionId . "') OR (RegionId=''))" . " AND " .
		            "AuthKey='ROLE' " .
		            "ORDER BY UserId,RegionId";
		$RoleId = '';
		foreach($this->q->core->query($query) as $row) {
			$RoleId = $row['AuthAuxValue'];
		}
		return $RoleId;		
	} //function

	public function setUserRole($prmUserId,$prmRegionId,$prmRoleId) {
		$this->UserId   = $prmUserId;
		$this->RegionId = $prmRegionId;
		
		//Delete Current Permissions
		$query = "DELETE FROM RegionAuth WHERE " .
		            "UserId='" . $prmUserId . "'" . " AND " .
		            "RegionId='" . $prmRegionId . "'";
		$this->q->core->query($query);		         

		// Insert ROLE Permission First		
		$this->setPerm('ROLE', 0, $prmRoleId);
		switch($prmRoleId) {
			case 'ADMINREGION':
				$this->setPerm("DISASTER" , 5, "");
				$this->setPerm("EVENT"    , 5, "");
				$this->setPerm("CAUSE"    , 5, "");
				$this->setPerm("GEOGRAPHY", 5, "");
				$this->setPerm("GEOLEVEL" , 5, "");
				$this->setPerm("EEFIELD"  , 5, "");
				$this->setPerm("DBINFO"   , 2, "");
				$this->setPerm("AUTH"     , 2, "");
				$this->setPerm("DBPUBLIC" , 2, "");
				$this->setPerm("DBACTIVE" , 2, "");
				$this->setPerm("DBLOG"    , 5, "");
				break;
			case 'SUPERVISOR':
				$this->setPerm("DISASTER" , 4, "STATUS=DRAFT,STATUS=READY");
				$this->setPerm("EVENT"    , 1, "STATUS=ACTIVE");
				$this->setPerm("CAUSE"    , 1, "STATUS=ACTIVE");
				$this->setPerm("GEOGRAPHY", 1, "STATUS=ACTIVE");
				$this->setPerm("GEOLEVEL" , 1, "STATUS=ACTIVE");
				$this->setPerm("EEFIELD"  , 1, "STATUS=ACTIVE");
				$this->setPerm("DBINFO"   , 1, "");
				$this->setPerm("DBLOG"    , 3, "");
				break;
			case 'USER':
				$this->setPerm("DISASTER" , 3, "STATUS=DRAFT,STATUS=READY");
				$this->setPerm("EVENT"    , 1, "STATUS=ACTIVE");
				$this->setPerm("CAUSE"    , 1, "STATUS=ACTIVE");
				$this->setPerm("GEOGRAPHY", 1, "STATUS=ACTIVE");
				$this->setPerm("GEOLEVEL" , 1, "STATUS=ACTIVE");
				$this->setPerm("EEFIELD"  , 1, "STATUS=ACTIVE");
				$this->setPerm("DBINFO"   , 1, "");
				$this->setPerm("DBLOG"    , 3, "");
				break;
			case 'OBSERVER':
				$this->setPerm("DISASTER" , 1, "STATUS=ACTIVE");
				$this->setPerm("EVENT"    , 1, "STATUS=ACTIVE");
				$this->setPerm("CAUSE"    , 1, "STATUS=ACTIVE");
				$this->setPerm("GEOGRAPHY", 1, "STATUS=ACTIVE");
				$this->setPerm("GEOLEVEL" , 1, "STATUS=ACTIVE");
				$this->setPerm("DBINFO"   , 1, "");
				$this->setPerm("DBLOG"    , 1, "");
				$this->setPerm("EEFIELD"  , 1, "STATUS=ACTIVE");
				break;
			case 'MINIMAL':
				$this->setPerm("USER"     , 2, "");
				break;
		} //switch
	} //function
	
} //class
</script>
