<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
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
		  " WHERE CauseName LIKE '" . $prmCauseName . "'";
		foreach($this->q->dreg->query($sQuery) as $row) {
			// Local Cause Found
			$CauseId = $row['CauseId'];
			$this->set('CauseId', $CauseId);
			$this->set('CausePredefined'  , $row['CausePredefined']);
			$this->set('CauseCreationDate', $row['CauseCreationDate']);
		} //foreach
		
		if ($CauseId == '') {
			// Search PreDefined Cause
			$sQuery = "SELECT * FROM DI_Cause WHERE " . 
			  "  (CauseName LIKE '%" . $this->get('CauseName') . "%'" .
			  "   OR CauseKeywords LIKE '%" . $this->get('CauseName') . "%')";
			foreach($this->q->base->query($sQuery) as $row) {
				$CauseId = $row['CauseId'];
				$this->set('CauseId'          , $CauseId);
				$this->set('CauseName'        , $row['CauseName']);
				$this->set('CauseDesc'        , $row['CauseDesc']);
				$this->set('CausePredefined'  , 1);
				$this->set('CauseCreationDate', $row['CauseCreationDate']);
			} //foreach
		} //if
		if ($CauseId == '') {
			$CauseId = $prmCauseId;
		}
		return $CauseId;
	} // function
	
	public function getDeleteQuery() {
		$sQuery = "UPDATE " . $this->getTableName() . " SET CauseActive=0" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}

	public function validateCreate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -21, 'CauseId');
		$iReturn = $this->validatePrimaryKey($iReturn,  -22);
		return $iReturn;
	}

	public function validateNoDatacards($curReturn, $ErrCode) {
		$iReturn = $curReturn;
		if ($iReturn > 0) {
			$Count = 0;
			$Query = "SELECT COUNT(DisasterId) AS COUNT FROM Disaster WHERE CauseId='" . $this->get('CauseId') . "'";
			fb($Query);
			foreach($this->q->dreg->query($Query) as $row) {
				$Count = $row['COUNT'];
			}
			fb('Count : ' . $Count);
			if ($Count > 0) {
				$iReturn = $ErrCode;
			}
		}
		fb($iReturn);
		return $iReturn;
	}

	public function validateUpdate() {
		$iReturn = 1;
		$iReturn = $this->validateNotNull($iReturn, -23, 'CauseName');
		$iReturn = $this->validateUnique($iReturn,  -24, 'CauseName', true);
		fb('X' . $this->get('CauseActive') . 'X');
		if ($this->get('CauseActive') == 0) {
			$iReturn = $this->validateNoDatacards($iReturn, -25);
		}
		return $iReturn;
	}
	
	public function validateDelete() {
		$iReturn = ERR_NO_ERROR;
		$iReturn = $this->validateNoDatacards($iReturn, -25);
		return $iReturn;
	}
	
}

</script>
