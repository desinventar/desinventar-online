<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIGeoLevel extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "GeoLevel";
		$this->sPermPrefix  = "GEOLEVEL";
		$this->sFieldKeyDef = "GeoLevelId/INTEGER," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "SyncRecord/DATETIME," .
		                      "GeoLevelName/STRING," .
		                      "GeoLevelDesc/STRING," .  
		                      "GeoLevelActive/INTEGER," .
		                      "GeoLevelLayerFile/STRING," .
		                      "GeoLevelLayerName/STRING," .
		                      "GeoLevelLayerCode/STRING";
		parent::__construct($prmSession);
		$this->set("GeoLevelActive", 1);

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$this->GeoLevelId = func_get_arg(1);
			$this->LangIsoCode = 'spa';
			if ($num_args >= 3) {
				$this->LangIsoCode = func_get_arg(2);
			}
			$this->load();
		}
	}
	
}
