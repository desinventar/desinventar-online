#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-10-09 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import EEData from DI6 IND_TAMILNADU
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/didisaster.class.php');
require_once(BASE . '/include/dieedata.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dieefield.class.php');
require_once(BASE . '/include/date.class.php');

$RegionId = 'GAR-ISDR-2011_IND_TAMILNADU';
$us->login('diadmin','di8');
$us->open($RegionId);

//createEEFields($us);
//exit(0);

$line = 1;
$count = 0;
$fh = fopen('/tmp/IND_I2_eedata.csv','r');
$a = fgetcsv($fh, 0, ',');
$a = fgetcsv($fh, 0, ',');
while (! feof($fh) ) {
	$a = fgetcsv($fh, 0, ',');
	if (count($a) > 1) {
		for($i = 0; $i<count($a); $i++) {
			$a[$i] = trim($a[$i]);
		}
		// 0 - DisasterId
		$DisasterId = $a[0];
		if ($DisasterId != '') {
			$count++;
			$e = new DIEEData($us, $DisasterId);
			for($i = 1; $i <= count($a) - 2; $i++) {
				$fname = 'EEF' . padNumber($i, 3);
				$e->set($fname, valueToDIField($a[$i+1]));
			}
			$i = 1;
			$j = 1;
			//if ($bExist < 0) {
			//	$j = $e->insert();
			//} else {
			$j = $e->update();
			//}
			if ( ($i < 0) || ($j < 0) ) {
				print $line . ' ' . $DisasterId . ' ' . $i . ' ' . $j . "\n";
			}			
			if (($line > 0) && (($line % 100) == 0) ) {
				printf('%5d %5d' . "\n", $line, $count);
			}
		} //if
	} //if
	$line++;
} //while
fclose($fh);
print 'Lines   : ' . $line . "\n";
print 'Records : ' . $count . "\n";

$us->close();
$us->logout();
exit();

function valueToDIField($prmValue) {
	$Value = 0;
	$prmValue = preg_replace('/\$/', '', $prmValue);
	$prmValue = preg_replace('/\./', '', $prmValue);
	$prmValue = preg_replace('/,/', '.', $prmValue);
	if (is_numeric($prmValue)) {
		$Value = $prmValue;
	} else {
		if ($prmValue == 'hubo') {
			$Value = -1;
		}
		if ($prmValue == 'no hubo') {
			$Value = 0;
		}
	}
	return $Value;
}

function strToISO8601($prmDate) {
	$v = '';
	if (strlen($prmDate) > 0) {
		$month = substr($prmDate,0,2);
		$day   = substr($prmDate,3,2);
		$year  = substr($prmDate,6,4);
		$v = sprintf('%4d-%2d-%2d', $year, $month, $day);
		$v = str_replace(' ', '0', $v);
	}
	return $v;
}

function getMonth($prmMonthName) {
	$m = array('ene' =>  1, 'feb' =>  2, 'mar' =>  3, 'apr' =>  4, 'may' =>  5, 'jun' =>  6, 
	           'jul' =>  7, 'aug' =>  8, 'sep' =>  9, 'oct' => 10, 'nov' => 11, 'dec' => 12);
	$v = 0;
	$MonthName = strtolower($prmMonthName);
	if (array_key_exists($MonthName, $m)) {
		$v = $m[$MonthName];
	}
	return $v;
}

function createEEField($prmSession, $EEFieldLabel, $EEFieldType, $EEFieldSize='') {
	$f = new DIEEField($prmSession);
	$f->set('EEGroupId', 'SLV');
	$f->set('EEFieldLabel', $EEFieldLabel);
	$f->set('EEFieldType', $EEFieldType);
	if ($EEFieldSize != '') {
		$f->set('EEFieldSize', $EEFieldSize);
	}
	$i = $f->insert();
	return $i;
}

function createEEFields($us) {
	$i = createEEField($us, 'Risk Type ', 'INTEGER' );      //  1
	$i = createEEField($us, 'Event Type', 'INTEGER' );      //  2
}

</script>
