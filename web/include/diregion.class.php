<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIRegion extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Region";
		$this->sPermPrefix  = "INFO";
		$this->sFieldKeyDef = "RegionId/STRING";
		$this->sFieldDef    = "RegionLabel/STRING," .
		                      "LangIsoCode/STRING," . 
		                      "CountryIso/STRING," .
		                      "RegionStatus/INTEGER";
		$this->sInfoDef     = "RegionDesc/STRING," .
		                      "RegionDescEN/STRING," .
		                      "PeriodBeginDate/DATE," .
		                      "PeriodEndDate/DATE";
		parent::__construct($prmSession);
		$num_args = func_num_args();
		$this->set('RegionLangCode', 'spa');
		$this->setConnection("core");
		if ($num_args >= 2) {
			$prmRegionId = func_get_arg(1);
			if ($prmRegionId != '') {
				$this->set('RegionId', $prmRegionId);
			}
			$this->load();
		}
	} // __construct
} //class

</script>
