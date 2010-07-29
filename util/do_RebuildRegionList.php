#!/usr/bin/php
<script language="php">
	require_once('../web/include/loader.php');
	
	if ($handle = opendir(DBDIR)) {
		while ( ($file = readdir($handle)) == true) {
			$DBDir = DBDIR . '/' . $file;
			if (is_dir($DBDir)) {
				echo $file . "\n";
			}
		}
	}
	closedir($handle);
</script>
