<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIUser extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "User";
		$this->sPermPrefix  = "ADMIN";
		$this->sFieldKeyDef = "UserId/STRING";
		$this->sFieldDef    = "UserEMail/STRING," .
		                      "UserPasswd/STRING," .
		                      "UserFullName/STRING," .
		                      "Organization/STRING," .
		                      "CountryIso/STRING," .
		                      "UserCity/STRING," .
		                      "UserCreationDate/DATETIME," .
		                      "UserNotes/STRING," .
		                      "UserActive/INTEGER";
		parent::__construct($prmSession);
		$num_args = func_num_args();
		$this->setConnection("core");
		if ($num_args >= 2) {
			$prmUserId = func_get_arg(1);
			if ($prmUserId != '') {
				$this->set('UserId', $prmUserId);
				$this->load();
			}
		}
	} // __construct

	public function set($prmKey, $prmValue) {
		$iReturn = ERR_DEFAULT_ERROR;
		if ($prmKey == 'UserPasswd') {
			$prmValue = md5($prmValue);
		}
		$iReturn = parent::set($prmKey, $prmValue);
		return $iReturn;
	}
} //class

</script>
