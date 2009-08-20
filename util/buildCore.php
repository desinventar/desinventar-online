#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	/*
	Use this script to rebuild the core.db database, it 
	scans the data directory for databases, and using
	the data in the Info tables, rebuilds the core.Region
	table for the list of databases.
	2009-08-20 Jhon H. Caicedo <jhcaiced@desinventar.org>
	*/
	
	$_SERVER["DI8_WEB"] = '../web';
	require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
	require_once(BASE . '/include/diregion.class.php');
	
	print DATADIR . "\n";
	rebuildCoreRegionList($us, DATADIR);
	
	function rebuildCoreRegionList($us, $directory) {
		// Remove Previous Entries
		$query = "DELETE FROM Region";
		$us->q->core->query($query);
		
		$results = array();
		$handler = opendir($directory);
		while ($file = readdir($handler)) {
			// if $file isn't this directory or its parent, 
			// add it to the results array
			if ($file != '.' && $file != '..') {
				if (file_exists($directory . '/' . $file . '/desinventar.db')) {
					$results[] = $file;
					$RegionId = $file;

					// Create a new entry using data from Info table
					$r = new DIRegion($us, $RegionId);
					$r->loadInfo();
					$r->insert();
				}
			}
		} //while
		// tidy up: close the handler
		closedir($handler);
	
		//foreach($results as $dir) {
			//print $dir . "\n";
		//}
	} //function
	
</script>
