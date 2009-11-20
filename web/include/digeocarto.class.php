<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
*/

class DIGeoCarto extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "GeoCarto";
		$this->sPermPrefix  = "GEOLEVEL";
		$this->sFieldKeyDef = "GeoLevelId/INTEGER," .
		                      "GeographyId/STRING," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "RegionId/STRING," .
		                      "GeoLevelLayerFile/STRING," .
		                      "GeoLevelLayerName/STRING," .
		                      "GeoLevelLayerCode/STRING," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," .
		                      "RecordUpdate/DATETIME";
		parent::__construct($prmSession);

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmGeoLevelId = func_get_arg(1);
			$this->set('GeoLevelId', $prmGeoLevelId);
			if ($num_args >= 3) {
				$prmLangIsoCode = func_get_arg(2);
				$this->set('LangIsoCode', $prmLangIsoCode);
			}
			$this->load();
		}
	} // __construct

} //class

</script>
