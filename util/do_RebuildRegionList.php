#!/usr/bin/php
<script language="php">
	require_once('../web/include/loader.php');
	
	if ($handle = opendir(DBDIR)) {
		$dirlist = array();
		while ( ($diritem = readdir($handle)) == true) {
			if ($diritem != '.' && $diritem != '..') {
				$DBDir = DBDIR . '/' . $diritem;
				if (is_dir($DBDir)) {
					if (file_exists($DBDir . '/desinventar.db')) {
						array_push($dirlist, $diritem);
					}
				}
			}
		}
	}
	closedir($handle);
		
	foreach($dirlist as $diritem) {
		echo $diritem . "\n";
		/*
			$results[] = $file;
			$RegionId = $file;

			// Create a new entry using data from Info table
			$r = new DIRegion($us, $RegionId);
			$r->loadInfo();
			$r->insert();
		*/
	}
</script>
