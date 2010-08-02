#!/usr/bin/php
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . '/include/diregion.class.php');
	
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
	
	//$dirlist = array('PAR-1250695038-paraguay_gran_chaco');
	$dirlist = array('GAR-ISDR-2011_COL');
		
	// Remove Previous Entries
	$query = "DELETE FROM Region";
	$us->q->core->query($query);
	
	// Process list to add new Items
	foreach($dirlist as $diritem) {
		echo $diritem . "\n";
		$RegionId = $diritem;
		// Create a new entry using data from Info table
		$r = new DIRegion($us, $RegionId);
		$r->loadInfo();
		$r->insert();
	}
</script>
