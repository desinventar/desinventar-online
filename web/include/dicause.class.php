<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DICause extends DIObject {
	public function __construct($prmSession) {
		$this->sTableName   = "Cause";
		$this->sPermPrefix  = "EVENT";
		$this->sFieldKeyDef = "CauseId/STRING," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "SyncRecord/DATETIME," .
		                      "CauseName/STRING," .
		                      "CauseDesc/STRING," .
		                      "CauseActive/BOOLEAN," .  
		                      "CausePredefined/BOOLEAN," .
		                      "CauseRGBColor/STRING," .
		                      "CauseKeyWords/STRING," .
		                      "CauseCreationDate/DATETIME," .
		                      "CauseLastUpdate/DATETIME";
		parent::__construct($prmSession);
		$this->set("CausePredefined", 0);
		$this->set("CauseActive", 1);
		$this->set("LangIsoCode", $this->q->getDBInfoValue('I18NFirstLang'));
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
				$this->setIdByName($this->get('CauseName'));
			}
		}
	}
	
	public function setIdByName($prmCauseName) {
		$iReturn = 0;
		$sQuery = "SELECT * FROM " . $this->getTableName() .
		  " WHERE CauseName='" . $prmCauseName . "'";
		if ($result = $this->q->dreg->query($sQuery)) {
			if ($result->num_rows>0) {
				// Local Cause Found
				while ($row = $result->fetch_object()) {
					$this->set('CauseId', $row->CauseId);
					$this->set('CausePreDefined', $row->CausePreDefined);
					$this->set('CauseCreationDate', $row->CauseCreationDate);
				} // while
			} else {
				// Search PreDefined Cause
				$sQuery = "SELECT * FROM DI_Cause " . 
				  " WHERE CauseLangCode='" . $this->oSession->sRegionLangCode . "'" .
				  "   AND (CauseLocalName='" . $this->get('CauseName') . "'" .
				  "        OR CauseDI6Name='" . $this->get('CauseName') . "')";
				if ($result = $this->q->base->query($sQuery)) {
					while ($row = $result->fetch_object()) {
						$this->set('CauseId', $row->CauseId);
						$this->set('CauseName', $row->CauseName);
						$this->set('CauseDesc', $row->CauseDesc);
						$this->set('CausePreDefined', 1);
						$this->set('CauseCreationDate', $row->CauseCreationDate);
					}
				}
			}
		}
		return $iReturn;
	} // function
	
	public function getDeleteQuery() {
		$sQuery = "UPDATE " . $this->getTableName() . " SET CauseActive=0" .
		  " WHERE " . $this->getWhereSubQuery();
		return $sQuery;
	}
}
