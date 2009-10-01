#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	$GeographyFile = 'g.csv';
	$DisasterFile  = 'd.csv';
	$codes = array();
	$fh = fopen($GeographyFile, 'r');
	while (! feof($fh) ) {
		$a = fgetcsv($fh, 1000, ',');
		if (count($a) > 1) {
			$Level     = $a[0];
			$OldCode   = $a[1];
			$Name      = $a[2];
			$OldParent = $a[3];
			$NewCode = str_replace(".","",$OldCode);
			$NewCode = padCode($NewCode, 2);
			$NewParent = str_replace(".","",$OldParent);
			$l = strlen($NewParent);
			$NewCode = substr($NewCode, $l);
			$NewParent = '';
			if (isset($codes[$OldParent])) {
				$NewParent = $codes[$OldParent];
			}
			$NewCode = $NewParent . $NewCode;
			$codes[$OldCode] = $NewCode;
			//printf('%d,"%s","%s","%s"' . "\n", $Level, $NewCode, $Name, $NewParent);
		}
	}
	fclose($fh);

	// Fix Geography Code in Disaster file
	$fh = fopen($DisasterFile, 'r');
	$line = fgets($fh); print $line;
	$line = fgets($fh); print $line;
	while (! feof($fh) ) {
		$line = fgets($fh);
		$i = strpos($line, ',', 0);
		$j = strpos($line, ',', $i+1);
		$j = strpos($line, ',', $j+1); $i=$j+2;
		$j = strpos($line, ',', $j+1);
		$OldCode = substr($line, $i, $j - $i - 1);
		$l1 = substr($line, 0, $i);
		$l2 = substr($line, $j-1);

		$NewCode = '';
		if (isset($codes[$OldCode])) {
			$NewCode = $codes[$OldCode];
		} else {
			print "ERROR : " . $OldCode . "\n";
		}
		print $l1 . $NewCode . $l2;
	} //while
	fclose($fh);
		
	function padCode($value, $len) {
		while (strlen($value) < $len) {
			$value = '0' . $value;
		}
		return $value;
	}
</script>
