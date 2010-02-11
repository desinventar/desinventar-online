<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
*/
class DIStatus {

	public function __construct() {
		$this->status  = ERR_NO_ERROR;
		$this->error   = array();
		$this->warning = array();
	}
	
	public function addMsg($errCode, $errMsg, $isWarning=false) {
		if (! $isWarning) {
			$this->addError($errCode, $errMsg);
		} else {
			$this->addWarning($errCode, $errMsg);
		}
	}
	
	public function addError($errCode, $errMsg) {
		$this->error[$errCode] = $errMsg;
		$this->status = $errCode;
	}
	
	public function addWarning($errCode, $errMsg) {
		$this->warning[$errCode] = $errMsg;
		$this->status = 0;
	}
	
	public function hasError($errCode=0) {
		$bAnswer = false;
		if (count($this->error) > 0) {
			$bAnswer = true;
		}
		if ($errCode != 0) {
			$bAnswer = array_key_exists($errCode, $this->error);
		}
		return $bAnswer;
	}

	public function hasWarning($errCode=0) {
		$bAnswer = false;
		if (count($this->warning) > 0) {
			$bAnswer = true;
		}
		if ($errCode != 0) {
			$bAnswer = array_key_exists($errCode, $this->warning);
		}
		return $bAnswer;
	}
} //class
</script>
