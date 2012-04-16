<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

class DIRegionItem extends DIRecord
{
	public function __construct($prmSession)
	{
		$this->sTableName   = "RegionItem";
		$this->sPermPrefix  = "INFO";
		$this->sFieldKeyDef = "RegionId/STRING," .
		                      "RegionItem/STRING";
		$this->sFieldDef    = "RegionQuery/STRING";

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
				$prmRegionItem = func_get_arg(2);
				$this->set('RegionItem', $prmRegionItem);
			}
			$this->load();
		}
	} // __construct
} #class

</script>
