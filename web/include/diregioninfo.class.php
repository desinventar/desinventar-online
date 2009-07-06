<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIRegionInfo extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Info";
		$this->sPermPrefix  = "INFO";
		$this->sFieldKeyDef = "RegionId/STRING";
		$this->sFieldDef    = "RegionLabel/STRING," .
		                      "RegionDesc/STRING," .
		                      "RegionDescEN/STRING," .
		                      "RegionLangCode/STRING," .
		                      "PeriodBeginDate/DATE," .
		                      "PeriodEndDate/DATE," .
		                      "EEFieldOrder/INTEGER," .
		                      "EEFieldStatus/INTEGER";
	$ifo = $r->updateDBInfo($reg, $get['RegionLabel'], $get['RegionDesc'], $get['RegionDescEN'], 
							$get['RegionLangCode'], $get['PeriodBeginDate'], $get['PeriodEndDate'], $optout, 
							$get['GeoLimitMinX'], $get['GeoLimitMinY'], $get['GeoLimitMaxX'], $get['GeoLimitMaxY']);
		parent::__construct($prmSession);
		$num_args = func_num_args();
		$this->set('EEFieldId', $this->getNextEEFieldId());
		if ($num_args >= 2) {
			$prmEEFieldId = func_get_arg(1);
			if ($prmEEFieldId != '') {
				$this->set('EEFieldId', $prmEEFieldId);
			}
			$this->load();
		}
	} // __construct

	public function getMaxEEFieldId() {
		$iMaxVal = 0;
		$sQuery = "SELECT COUNT(EEFieldId) AS MAXVAL FROM " . $this->getTableName();
		if ($result = $this->q->dreg->query($sQuery)) {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$iMaxVal = $row->MAXVAL;
			}
		}
		return $iMaxVal;
	} // function
	
	public function getNextEEFieldId() {
		$v = $this->getMaxEEFieldId();
		$FieldName = "EEF" . $this->padNumber($v + 1, 3);
		return $FieldName;
	}
} //class

</script>
