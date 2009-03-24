<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIGeography extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Geography";
		$this->sPermPrefix  = "GEOGRAPHY";
		$this->sFieldKeyDef = "GeographyId/STRING," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "SyncRecord/DATETIME," .
		                      "GeographyCode/STRING," .
		                      "GeographyName/STRING," .  
		                      "GeoographyLevel/INTEGER," .
		                      "GeographyActive/INTEGER";
		parent::__construct($prmSession);
		$this->set("LangIsoCode", $this->q->getDBInfoValue('I18NFirstLang'));

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$this->set('GeographyId', func_get_arg(1));
			if ($num_args >= 3) {
				$this->set('LangIsoCode', func_get_arg(2));
			}
			$this->load();
		}
	} // __construct

	public String buildGeographyId($sMySessionUUID, $sMyCode, 
	                               $sMyParentCode, $iMyLevel) {
		$sQuery = '';
		$bError = 0;
	}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           	
	public function padNumber($iNumber, $iLen) {
		$sNumber = "" . $iNumber;
		while (strlen($sNumber) < $iLen) {
			$sNumber = "0" . $sNumber;
		}
		return $sNumber;
	} // function

} //class
