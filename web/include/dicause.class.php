<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO
*/

class DICause extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Cause";
		$this->sPermPrefix  = "CAUSE";
		$this->sFieldKeyDef = "CauseId/STRING," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "RegionId/STRING," . 
		                      "CauseName/STRING," .
		                      "CauseDesc/STRING," .
		                      "CauseActive/BOOLEAN," .  
		                      "CausePredefined/BOOLEAN," .
		                      "CauseRGBColor/STRING," .
		                      "CauseKeyWords/STRING," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," .
		                      "RecordUpdate/DATETIME";
		parent::__construct($prmSession);
		$this->set("CausePredefined", 0);
		$this->set("CauseActive", 1);
		$this->set("CauseId", uuid());

		$num_args = func_num_args();
		if ($num_args >= 2) {
			$prmCauseId = func_get_arg(1);
			$this->set('CauseId', $prmCauseId);
			if ($num_args >= 3) {
				$prmCauseName = func_get_arg(1);
				$prmCauseDesc = func_get_arg(2);				
				$this->set('CauseName', $prmCauseName);
				$this->set('CauseDesc', $prmCauseDesc);
				$this->getIdByName($this->get('CauseName'));
			}
			$this->load();
		}
	}
	
	public function getIdByName($prmCauseName) {
		$CauseId = '';
		$sQuery = "SELECT * FROM " . $this->getTableName() .
		  " WHERE LangIsoCode='" . $this->get('LangIsoCode') . "'" . 
		  " AND (CauseName LIKE '" . $prmCauseName . "'" . 
		  "      OR CauseKeyWords LIKE '" . $prmCauseName . ";')";
		foreach($this->q->dreg->query($sQuery) as $row) {
			$CauseId = $row['CauseId'];
			$this->set('CauseId', $CauseId);
		} //foreach
		
		if ($CauseId != '') {
			$this->load();
		} else {
			$CauseId = $prmCauseName;
		}
		return $CauseId;
	} // function
	
	public function getDeleteQuery() {
		$sQuery = "UPDATE " . $this->getTableName() . " SET CauseActive=0" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}

	public function validateCreate() {
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNotNull(-21, 'CauseId');
		if ($iReturn > 0) {
			$iReturn = $this->validatePrimaryKey(-22);
		}
		return $iReturn;
	}

	public function validateNoDatacards($ErrCode) {
		$iReturn = ERR_NO_ERROR;
		$Count = 0;
		$Query = "SELECT COUNT(DisasterId) AS COUNT FROM Disaster WHERE CauseId='" . $this->get('CauseId') . "'";
		foreach($this->q->dreg->query($Query) as $row) {
			$Count = $row['COUNT'];
		}
		if ($Count > 0) {
			$iReturn = $ErrCode;
		}
		return $iReturn;
	}

	public function validateUpdate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull(-23, 'CauseName');
		if ($iReturn > 0) {
			$iReturn = $this->validateUnique(-24, 'CauseName', true);
			if ($iReturn > 0) {
				if ($this->get('CauseActive') == 0) {
					$iReturn = $this->validateNoDatacards(-25);
				}
			}
		}
		return $iReturn;
	}
	
	public function validateDelete() {
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNoDatacards(-25);
		return $iReturn;
	}

	public function importFromCSV($cols, $values) {
		$oReturn = parent::importFromCSV($cols, $values);
		$iReturn = $oReturn['Status'];
		if ($iReturn > 0) {
			$this->set('CauseName',  $values[1]);
			$this->getIdByName($this->get('CauseName'));
			if ( (count($oReturn['Error']) > 0) || (count($oReturn['Warning']) > 0) ) {
				$iReturn = ERR_UNKNOWN_ERROR;
			}
			$oReturn['Status'] = $iReturn;
		}
		return $oReturn;
	} //function
	
}

</script>
