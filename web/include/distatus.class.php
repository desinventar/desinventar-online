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
	
	public function addError($errCode, $errMsg) {
		$this->error[] = $errMsg;
		$this->status = $errCode;
	}
	
	public function addWarning() {
		$this->warning[] = $errMsg;
		$this->status = $errCode;
	}
} //class
</script>
