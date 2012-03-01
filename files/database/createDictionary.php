#!/usr/bin/php
<script language="php">
	$h = fopen('./doc-db.csv','r');
	$i = 0;
	print "DELETE FROM Dictionary;" . "\n";
	print "DELETE FROM LabelGroup;" . "\n";
	
	while(!feof($h)) {
	//while ($i < 3) {
		$a = fgetcsv($h);
		if (count($a) > 1) {
			if ($i == 0) {
				// Ignore First Line (Headers...)
			} else {
				$now = gmdate('c');
				$query = sprintf('INSERT INTO LabelGroup VALUES ("%s","%s","%s","%s","%s","%s","%s");', 
					$i,$a[0],$a[1],$a[2], $now,$now,$now);
				print $query . "\n";
				$langlist = array('eng','spa','por','fre');
				$Index = 3; // First column with language information
				foreach($langlist as $Lang)
				{
					$query = sprintf('INSERT INTO Dictionary VALUES ("%s","%s","%s","%s","%s","%s","%s","%s","%s");',
						$i, $Lang, $a[$Index],
						escapeQuotes($a[$Index+4]),
						escapeQuotes($a[$Index+8]),
						escapeQuotes($a[$Index+12]),
						$now,$now,$now);
					print $query . "\n";
					$Index++;
				} //foreach
			} //if
		} //if
		$i++;
	}
	fclose($h);
	
	function escapeQuotes($prmValue) {
		$prmValue = str_replace('"','',$prmValue);
		return $prmValue;
	}
</script>
