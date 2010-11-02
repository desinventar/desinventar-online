#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-10-09 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import EEData from DI6 LKA
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

$RegionId = 'GAR-ISDR-2011_LKA';
$us->login('diadmin','di8');
$us->open($RegionId);

//createEEFields($us);
//exit(0);

$line = 1;
$count = 0;
$a = fgetcsv(STDIN, 0, ',');
$a = fgetcsv(STDIN, 0, ',');
while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 0, ',');
	if (count($a) > 1) {
		for($i = 0; $i<count($a); $i++) {
			$a[$i] = trim($a[$i]);
		}
		// 0 - DisasterSerial
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
	$fielname  = array();
	$fielddesc = array();
	$fieldtype = array();
	$fieldsize = array();
	$fh = fopen('/tmp/LKA_eefield.csv', 'r');
	// Header Line
	$a = fgetcsv($fh, 0, ',');
	while (! feof($fh) ) {
		$a = fgetcsv($fh, 0, ',');
		if (count($a) > 1) {
			//print $a[1] . ' ' . $a[3] . ' ' . $a[2] . "\n";
			$fieldname[$a[1]] = $a[3];
			$fielddesc[$a[1]] = $a[2];
			$fieldtype[$a[1]] = $a[11];
			$fieldsize[$a[1]] = $a[7];
		}
	}
	fclose($fh);

	$fh = fopen('/tmp/LKA_eedata.csv', 'r');
	// Header Line
	$a = fgetcsv($fh, 0, ',');
	$a = fgetcsv($fh, 0, ',');
	foreach($a as $key) {
		if (array_key_exists($key, $fieldname) ) {
			$ftype = 'INTEGER';
			if ($fieldtype[$key] == 2) { $ftype = 'DOUBLE';   }
			if ($fieldtype[$key] == 0) { $ftype = 'STRING';   }
			if ($fieldtype[$key] == 3) { $ftype = 'CURRENCY'; }
			if ($fieldtype[$key] == 7) { $ftype = 'BOOLEAN';  }
			print "$key  $fieldtype[$key] $ftype \n";
			if ($ftype == 'STRING') {
				$i = createEEField($us, $fieldname[$key], 'STRING', 50);
			} else {
				$i = createEEField($us, $fieldname[$key], $ftype);
			}
		} //if
	} //foreach
	fclose($fh);
	/*
	$i = createEEField($us, 'Comercio'                , 'INTEGER' );      //  1
	$i = createEEField($us, 'Duracion Evento en Horas', 'INTEGER' );      //  2
	$i = createEEField($us, 'Iglesias'                , 'INTEGER' );      //  3
	$i = createEEField($us, 'Otras Edificaciones'     , 'INTEGER' );      //  4
	$i = createEEField($us, 'Puentes'                 , 'INTEGER' );      //  5
	$i = createEEField($us, 'Turismo'                 , 'STRING', 200);   //  6
	*/
}

</script>
