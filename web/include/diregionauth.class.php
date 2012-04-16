<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

class DIRegionAuth extends DIRecord
{
	public function __construct($prmSession)
	{
		$this->sTableName   = "RegionAuth";
		$this->sPermPrefix  = "ADMIN";
		$this->sFieldKeyDef = "UserId/STRING," . 
		                      "RegionId/STRING," .
		                      "AuthKey/STRING";
		$this->sFieldDef    = "AuthValue/STRING," .
		                      "AuthAuxValue/STRING";
		parent::__construct($prmSession);
		$num_args = func_num_args();
		$this->setConnection("core");
		if ($num_args >= 2)
		{
			$prmRegionId = func_get_arg(1);
			if ($prmRegionId != '')
			{
				$this->set('RegionId', $prmRegionId);
			}
			if ($num_args >= 3)
			{
				$prmUserId = func_get_arg(2);
				$this->set('UserId', $prmUserId);
			}
			if ($num_args >= 6)
			{
				$prmAuthKey = func_get_arg(3);
				$this->set('AuthKey', $prmAuthKey);
				$prmAuthValue= func_get_arg(4);
				$this->set('AuthValue', $prmAuthValue);
				$prmAuthAuxValue = func_get_arg(5);
				$this->set('AuthAuxValue', $prmAuthAuxValue);
			}
			$this->load();
		}
	} #__construct
	
} #class

</script>
