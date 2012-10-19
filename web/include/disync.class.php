<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

class DISync extends DIRecord
{
	public function __construct($prmSession)
	{
		$this->sTableName   = "Sync";
		$this->sPermPrefix  = "ADMIN";
		$this->sFieldKeyDef = "SyncId/STRING";
		$this->sFieldDef    = "RegionId/STRING," .
		                      "SyncTable/STRING," .
		                      "SyncUpload/DATETIME," .
		                      "SyncDownload/DATETIME," .  
		                      "SyncURL/STRING," .
		                      "SyncSpec/STRING";
		parent::__construct($prmSession);
		$this->set('SyncId', (string)UUID::mint(4));
		$num_args = func_num_args();
		if ($num_args >= 2)
		{
			$prmSyncId = func_get_arg(1);
			if ($prmSyncId != '')
			{
				$this->set('SyncId', $prmSyncId);
				$this->load();
			}
		}
	} #__construct
} #class

</script>
