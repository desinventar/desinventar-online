<script language="php">	

class DIRegionDB {
	public function __construct($prmSession) {
	}

	public static function createRegionDBFromZip($mode, $prmRegionId, $prmRegionLabel, $prmZipFile) {
		$iReturn = ERR_NO_ERROR;
		
		// Open zip file and extract files
		$zip = new ZipArchive();
		$res = $zip->open($prmZipFile);
		if ($res != TRUE) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($iReturn > 0) {
			$OutDir = DBDIR . '/' . $prmRegionId;
			fb($OutDir);
			//$zip->extractTo($OutDir);
			$zip->close();
		}	
		fb($iReturn);
		return $iReturn;	
	} //function
} //class

</script>
