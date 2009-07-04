<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIGeoCarto extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "GeoCarto";
		$this->sPermPrefix  = "GEOLEVEL";
		$this->sFieldKeyDef = "GeoLevelId/INTEGER," .
		                      "GeographyId/STRING";
		$this->sFieldDef    = "SyncRecord/DATETIME," .
		                      "RegionId/STRING," .
		                      "GeoLevelLayerFile/STRING," .
		                      "GeoLevelLayerName/STRING," .
		                      "GeoLevelLayerCode/STRING";
		parent::__construct($prmSession);
		$this->set("GeoLevelActive", 1);
		$this->set("LangIsoCode", $this->q->getDBInfoValue('I18NFirstLang'));

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

	public function getMaxGeoLevel() {
		$iMaxVal = 0;
		$sQuery = "SELECT MAX(GeoLevelId) AS MAXVAL FROM GeoLevel";
		if ($result = $this->q->dreg->query($sQuery)) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$iMaxVal = $row->MAXVAL;
			}
		}
		return $iMaxVal;
	} // function
} //class
