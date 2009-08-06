<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DISync extends DIObject {
	public function __construct($prmSession) {
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
		$this->set('SyncId', uuid());
		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmSyncId = func_get_arg(1);
			if ($prmSyncId != '') {
				$this->set('SyncId', $prmSyncId);
				$this->load();
			}
		}
	} // __construct
}

</script>
