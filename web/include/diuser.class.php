<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

class DIUser extends DIRecord
{
	public function __construct($prmSession)
	{
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
		                      "UserActive/BOOLEAN";
		parent::__construct($prmSession);
		$num_args = func_num_args();
		$this->setConnection("core");
		if ($num_args >= 2)
		{
			$prmUserId = func_get_arg(1);
			if ($prmUserId != '')
			{
				$this->set('UserId', $prmUserId);
				$this->load();
			}
		}
	} // __construct
	
	public function getSelectQuery($prmTableName = '')
	{
		$query = parent::getSelectQuery($prmTableName);
		$query .= " OR (UserNotes LIKE '%(UserName=" . $this->get('UserId') . ")%')";
		return $query;
	}

} #class

</script>
