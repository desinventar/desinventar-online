<script language="php">	

class DIRegionDB {
	public function __construct($prmSession) {
	}

	public static function createRegionDBFromZip($us, $mode, $prmRegionId, $prmRegionLabel, $prmZipFile) {
		$iReturn = ERR_NO_ERROR;
		
		// Open zip file and extract files
		$zip = new ZipArchive();
		$res = $zip->open($prmZipFile);
		if ($res != TRUE) {
			$iReturn = ERR_UNKNOWN_ERROR;
		}
		
		if ($iReturn > 0) {
			$OutDir = DBDIR . '/' . $prmRegionId;
			if ($mode == 'NEW') {
				// Create directory for new database
				if (! mkdir(DBDIR . '/' . $prmRegionId, 0755)) {
					$iReturn = ERR_UNKNOWN_ERROR;
				}
			}
		}
		
		if ($iReturn > 0) {
			// Extract contents of zipfile
			$zip->extractTo($OutDir);
			$zip->close();
		}
		
		if ($iReturn > 0) {
			//Create/update info.xml and core.Region data...
			if ($mode == 'NEW') {
				$dbexist = DIRegion::existRegion($us, $prmRegionId);
				if ($dbexist > 0) {
					// RegionId already exists, cannot create db
					$iReturn = ERR_UNKNOWN_ERROR;
				} else {
					$r = new DIRegion($us, $prmRegionId, $OutDir . '/info.xml');
					$r->set('RegionId', $prmRegionId);
					$r->set('RegionLabel', $prmRegionLabel);
					$us->open($prmRegionId);
					$iReturn = $r->insert();
				}
			}
		}
		
		if ($iReturn < 0) {
			// In case of error, cleanup by removing the new directory
			if ($mode == 'NEW') {
				DIRegion::deleteRegion($us, $prmRegionId);
				rrmdir($OutDir);
			}
		}	
		return $iReturn;	
	} //function
} //class

</script>
