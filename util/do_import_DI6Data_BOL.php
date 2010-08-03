#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-08-03 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from DI6 XLS Files
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/didisaster.class.php');
require_once(BASE . '/include/dieedata.class.php');
require_once(BASE . '/include/dicause.class.php');

$RegionId = 'BOL-1248983224-bolivia_inventario_historico_de_desastres';

$us->login('diadmin','di8');
$us->open($RegionId);

$line = 1;
// Header line... ignore
$a = fgetcsv(STDIN, 1000, ',');
while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 1000, ',');
	if (count($a) > 1) {
		for($i = 0; $i<count($a); $i++) {
			$a[$i] = trim($a[$i]);
		}
		
		// Get DisasterSerial/DisasterId
		$DisasterSerial = $a[0];
		$p = $us->getDisasterIdFromSerial($DisasterSerial);
		$DisasterId = $p['DisasterId'];
		
		$d = new DIDisaster($us, $DisasterId);

		// 0 - DisasterSerial
		$d->set('DisasterSerial', $DisasterSerial);

		// 1,2,3 - GeographyCode -> GeographyId
		$GeographyCode = $a[1];
		if ($a[2] != '') { $GeographyCode = $a[2]; }
		if ($a[3] != '') { $GeographyCode = $a[3]; }
		$GeographyId = DIGeography::getIdByCode($us, $GeographyCode);
		$d->set('GeographyId', $GeographyId);
		
		// 4,5,6 GeographyName (Not needed)

		// 7 - Event
		$EventList = array();
		$EventList['TORMENTA E.'] = 'ELECTRICSTORM';
		$EventList['ESTRUCTURA']  = 'STRUCTURE';
		$EventName = $a[7];
		if (array_key_exists($EventName, $EventList)) {
			$EventId = $EventList[$EventName];
		} else {
			$EventId = DIEvent::getIdByName($us, $EventName);
		}
		$d->set('EventId', $EventId);
		
		// 8 DisasterSiteNotes
		$d->set('DisasterSiteNotes', $a[8]);
		
		// 9,10,11 DisasterBeginTime
		$DisasterBeginTime = sprintf('%04d', $a[9]);
		if ($a[10] != '') {
			$DisasterBeginTime .= sprintf('-%02d' , $a[10]);
		}
		if ($a[11] != '') {
			$DisasterBeginTime .= sprintf('-%02d' , $a[11]);
		}
		$d->set('DisasterBeginTime', $DisasterBeginTime);

		//print $DisasterSerial . ' ' . $a[0] . "\n";

		// 12-15 - Effects (Basic)
		$d->set('EffectPeopleDead'    , valueToDIField($a[12],$a[25]));
		$d->set('EffectPeopleInjured' , valueToDIField($a[13],$a[26]));
		$d->set('EffectPeopleMissing' , valueToDIField($a[14],$a[27]));
		$d->set('EffectPeopleAffected', valueToDIField($a[15],$a[28]));

		// 16,17 Effects (Houses)
		$d->set('EffectHousesDestroyed', valueToDIField($a[16],$a[29]));
		$d->set('EffectHousesAffected' , valueToDIField($a[17],$a[30]));
		
		// 18 EffectOtherLosses
		$d->set('EffectOtherLosses', $a[18]);

		// 19 EffectNotes
		$d->set('EffectNotes', $a[19]);

		// 20 DisasterSource
		$d->set('DisasterSource', $a[20]);
		
		// 21 - EffectLossesValueLocal
		$d->set('EffectLossesValueLocal', $a[21]);
		
		// 22 - EffectLossesValueUSD
		$d->set('EffectLossesValueUSD', $a[22]);
		
		// 23
		$d->set('RecordAuthor', $a[23]);
		
		//24 RecordCreation
		$d->set('RecordCreation', valueToDate($a[24]));
		
		// 25 - 30 Boolean values for Effect Fields...
		
		// 31 - Sector Other
		$d->set('SectorOther', valueToDIField($a[31]));
		// 32 
		$d->set('SectorRelief', valueToDIField($a[32]));
		// 33
		$d->set('SectorHealth', valueToDIField($a[33]));
		// 34
		$d->set('SectorEducation', valueToDIField($a[34]));
		// 35
		$d->set('SectorAgricultural', valueToDIField($a[35]));
		// 36
		$d->set('SectorIndustry', valueToDIField($a[36]));
		// 37
		$d->set('SectorWaterSupply', valueToDIField($a[37]));
		// 38
		$d->set('SectorSewerage', valueToDIField($a[38]));
		// 39
		$d->set('SectorPower', valueToDIField($a[39]));
		// 40
		$d->set('SectorCommunications', valueToDIField($a[40]));
		
		$CauseList = array();
		$CauseList['Cond. Atmosféri'] = 'ATMOSPHCONDITION';
		// 41 - Cause
		$CauseName = $a[41];
		if (array_key_exists($CauseName, $CauseList)) {
			$CauseId = $CauseList[$CauseName];
		} else {
			$CauseId = DICause::getIdByName($us, $CauseName);
		}
		$d->set('CauseId', $CauseId);
		// 42
		$d->set('CauseNotes', $a[42]);
		// 43
		$d->set('SectorTransport', valueToDIField($a[43]));
		// 44
		$d->set('EventMagnitude', $a[44]);
		// 45
		$d->set('EffectMedicalCenters', valueToDIField($a[45]));
		// 46
		$d->set('EffectEducationCenters', valueToDIField($a[46]));
		// 47
		$d->set('EffectFarmingAndForest', valueToDIField($a[47]));
		// 48
		$d->set('EffectLiveStock', valueToDIField($a[48]));
		// 49
		$d->set('EffectRoads', valueToDIField($a[49]));
		// 50
		$d->set('EventDuration', valueToDIField($a[50]));
		// 51
		$d->set('EffectPeopleHarmed', valueToDIField($a[51], $a[53]));
		// 52
		$d->set('EffectPeopleEvacuated', valueToDIField($a[52], $a[54]));
		// 53-54
		// 55-56
		$d->set('EffectPeopleRelocated', valueToDIField($a[56], $a[55]));
		
		$e = new DIEEData($us, $d->get('DisasterId'));
		
		$bExist = $d->exist();
		if ($bExist < 0) {
			$mode = 'INSERT';
			$i = $d->insert();
			$e->set('DisasterId', $d->get('DisasterId'));
			$j = $e->insert();
		} else {
			$mode = 'UPDATE';
			$i = $d->update();
			$j = $e->update();
		}
		printf('%4d %-10s %s %s %s' . "\n", $line, $DisasterSerial, $mode, $d->get('EffectFarmingAndForest'), $a[47]);
		if ( ($i < 0) || ($j < 0) ) {
			print $line . ' ' . $DisasterSerial . ' ' . $i . ' ' . $j . "\n";
		}			
		if (($line > 0) && (($line % 100) == 0) ) {
			print $line . "\n";
		}
	} //if
	$line++;
} //while

$us->close();
$us->logout();
exit();

function valueToDIField($prmValue, $prmValue2='') {
	$Value = 0;
	$prmValue = preg_replace('/\$/', '', $prmValue);
	$prmValue = preg_replace('/\./', '', $prmValue);
	$prmValue = preg_replace('/,/', '.', $prmValue);
	if ($prmValue != '') {
		if (is_numeric($prmValue)) {
			$Value = $prmValue;
		} else {
			if ($prmValue == 'hubo') {
				$Value = -1;
			}
			if ($prmValue == 'no hubo') {
				$Value = 0;
			}
			if ( ($prmValue2 == 'FALSO') || ($prmValue2 == 'FALSE') ) {
				$Value = -2;
			}
			if ( ($prmValue2 == 'TRUE') || ($prmValue2 == 'VERDADERO') ) {
				$Value = -1;
			}
		}
	} else {
		$Value = 0;
		if ( ($prmValue2 == 'FALSO') || ($prmValue2 == 'FALSE') ) {
			$Value = -2;
		}
		if ( ($prmValue2 == 'TRUE') || ($prmValue2 == 'VERDADERO') ) {
			$Value = -1;
		}
	}
	return $Value;
}

function valueToDate($prmDate) {
	$v = '';
	if (strlen($prmDate) > 0) {
		$day   = substr($prmDate,0,2);
		$month = substr($prmDate,3,2);
		$year  = substr($prmDate,6,4);
		$v = sprintf('%4d-%2d-%2d', $year, $month, $day);
		$v = str_replace(' ', '0', $v);
	}
	return $v;
}

</script>
