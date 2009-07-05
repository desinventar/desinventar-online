<script language="php">
/*
  DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class DIImport {
	public function __construct($prmRegionId) {
		$this->RegionId = $prmRegionId;
		$this->q = newQuery($prmRegionId);
	}
	
	public function importFromCSV($FileName) {
		print "$FileName<br>\n";	
	}
} //class

</script>

