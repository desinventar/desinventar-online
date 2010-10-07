#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-09-27 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from Filemaker Database
  COL - Risaralda - Pereira - CARDER
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


$RegionId = 'COL-1260541809-colombia_risaralda';
$us->login('diadmin','di8');
$us->open($RegionId);

//createEEFields();

$r = new DIRegion($us, $RegionId);

$line = 1;
// skip first line, contains column names
$a = fgetcsv(STDIN, 0, ',');

while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 0, ',');
	if (count($a) > 1) {
		for($i = 0; $i<count($a); $i++) {
			$a[$i] = trim($a[$i]);
		}
		// 1 - DisasterBeginTime
		$DisasterBeginTime = strToISO8601($a[1]);
		// 0 - DisasterSerial
		$DisasterSerial = substr($DisasterBeginTime,0,4) . '-' . padNumber($a[0],5);
		$p = $us->getDisasterIdFromSerial($DisasterSerial);
		$DisasterId = $p['DisasterId'];
		if ($DisasterBeginTime != '') {
			$d = new DIDisaster($us, $DisasterId);
			$d->set('DisasterSerial', $DisasterSerial);			
			
			// 1 - DisasterBeginTime
			$d->set('DisasterBeginTime', $DisasterBeginTime);

			// 2 - Event
			$EventName = $a[2];
			$EventId = DIEvent::getIdByName($us, $EventName);
			$d->set('EventId', $EventId);
			
			// 3,4,5,6 - DisasterSiteNotes
			$d->set('DisasterSiteNotes', $a[3] . '/' . $a[4] . '/' . $a[5] . '/' . $a[6]);

			// 7 - Cause
			$CauseName = $a[7];
			if ($CauseName == 'No especificado') { $CauseName = 'Otra causa'; }
			$CauseId = DICause::getIdByName($us, $CauseName);
			$d->set('CauseId', $CauseId);

			// 8-9 - Effects (Basic)
			$d->set('EffectPeopleDead'    , valueToDIField($a[8]));
			$d->set('EffectPeopleInjured' , valueToDIField($a[9]));
			$d->set('EffectPeopleAffected', valueToDIField($a[12]));

			// 10-11 Effects (Houses)
			$d->set('EffectHousesDestroyed', valueToDIField($a[10]));
			$d->set('EffectHousesAffected' , valueToDIField($a[11]));
			
			$d->set('EffectLossesValueLocal', valueToDIField($a[13]));
			$d->set('EffectLossesValueUSD'  , valueToDIField($a[14]));
			
			// 15 - GeographyCode (Municipo/Urbano-Rural
			$Mpio = substr($a[15],0,7);
			$GeographyId = DIGeography::getIdByCode($us, $Mpio, '');
			$d->set('GeographyId', $GeographyId);

			// 17,18 EffectNotes
			$d->set('EffectNotes', $a[17] . ' ' . $a[18]);

			// 19
			$d->set('SectorWaterSupply', valueToDIField($a[19]));
			// 23
			$d->set('SectorAgricultural', valueToDIField($a[23]));
			// 24
			$d->set('SectorSewerage', valueToDIField($a[24]));
			// 25
			$d->set('EffectEducationCenters', valueToDIField($a[25]));
			// 27
			$d->set('EffectMedicalCenters', valueToDIField($a[27]));
			// 29
			$d->set('SectorCommunications', valueToDIField($a[29]));
			// 30 - EffectPeopleMissing
			$d->set('EffectPeopleMissing' , valueToDIField($a[30]));
			// 32
			$d->set('SectorEducation', valueToDIField($a[32]));
			// 33 - RecordAuthor
			$d->set('RecordAuthor', valueToDIField($a[33]));
			// 34
			$d->set('SectorPower', valueToDIField($a[34]));
			// 35
			$d->set('EffectPeopleEvacuated', valueToDIField($a[35]));
			// 38
			$d->set('RecordCreation', strToISO8601($a[38]));
			// 39
			$d->set('DisasterSource', valueToDIField($a[39]));
			// 41
			$d->set('SectorIndustry', valueToDIField($a[41]));
			// 42
			$d->set('EventMagnitude', valueToDIField($a[42]));
			// 43
			$d->set('EffectRoads', valueToDIField($a[43]));
			// 46,47
			$d->set('EffectOtherLosses', $a[46] . ' ' . $a[47]);
			// 48
			$d->set('EffectPeopleRelocated', valueToDIField($a[48]));
			// 50
			$d->set('SectorHealth', valueToDIField($a[50]));
			// 51
			$d->set('SectorRelief', valueToDIField($a[51]));
			// 52
			$d->set('SectorTransport', valueToDIField($a[52]));
			
			$bExist = $d->exist();
			if ($bExist < 0) {
				$i = $d->insert();
			} else {
				$i = $d->update();
			}
			$j = 1;
			if ( ($i < 0) || ($j < 0) ) {
				print $DisasterSerial . ' ' . $i . ' ' . $j . "\n";
			}			
			if (($line > 0) && (($line % 100) == 0) ) {
				//print $line . "\n";
			}
		} //if
	} //if
	$line++;
} //while

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

</script>
