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

$RegionId = 'ECU-1250695659-ecuador_sist_de_inf_de_desastres_y_emergencias';

$us->login('diadmin','di8');
$us->open($RegionId);

$line = 1;
// Header line... ignore
$a = fgetcsv(STDIN, 1000, ',');
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

		//print $DisasterSerial . ' ' . count($a) . "\n";

		// 1,2,3 - GeographyCode -> GeographyId
		$GeographyCode = $a[1];
		if ($a[2] != '') { $GeographyCode = $a[2]; }
		if ($a[3] != '') { $GeographyCode = $a[3]; }
		$GeographyId = DIGeography::getIdByCode($us, $GeographyCode);
		$d->set('GeographyId', $GeographyId);
		
		// 4,5,6 GeographyName (Not needed)

		// 7,8,9 DisasterBeginTime
		$DisasterBeginTime = sprintf('%04d', $a[7]);
		if ($a[8] != '') {
			$DisasterBeginTime .= sprintf('-%02d' , $a[8]);
		}
		if ($a[9] != '') {
			$DisasterBeginTime .= sprintf('-%02d' , $a[9]);
		}
		$d->set('DisasterBeginTime', $DisasterBeginTime);

		// 10
		$d->set('EventDuration', valueToDIField($a[10]));

		// 11 DisasterSource
		$d->set('DisasterSource', $a[11]);

		// 12 - Event
		$EventList = array();
		$EventList['TORMENTA E.'] = 'ELECTRICSTORM';
		$EventList['ESTRUCTURA']  = 'STRUCTURE';
		$EventList['OTROS']       = 'OTHER';
		$EventName = $a[12];
		if (array_key_exists($EventName, $EventList)) {
			$EventId = $EventList[$EventName];
		} else {
			$EventId = DIEvent::getIdByName($us, $EventName);
		}
		$d->set('EventId', $EventId);
		//60
		$d->set('EventNotes', $a[60]);
		
		// 13 DisasterSiteNotes
		$d->set('DisasterSiteNotes', $a[13]);

		$CauseList = array();
		$CauseList['Cond. Atmosféri'] = 'ATMOSPHCONDITION';
		// 14 - Cause
		$CauseName = $a[14];
		if (array_key_exists($CauseName, $CauseList)) {
			$CauseId = $CauseList[$CauseName];
		} else {
			$CauseId = DICause::getIdByName($us, $CauseName);
		}
		$d->set('CauseId', $CauseId);
		// 15
		$d->set('CauseNotes', $a[15]);


		// 16-25 - Effects (Basic)
		$d->set('EffectPeopleDead'    , valueToDIField($a[16],$a[17]));
		$d->set('EffectPeopleMissing' , valueToDIField($a[18],$a[19]));
		$d->set('EffectPeopleInjured' , valueToDIField($a[20],$a[21]));
		$d->set('EffectPeopleHarmed'  , valueToDIField($a[22],$a[23]));
		$d->set('EffectPeopleAffected', valueToDIField($a[24],$a[25]));

		// 26-29 Effects (Houses)
		$d->set('EffectHousesDestroyed', valueToDIField($a[26],$a[27]));
		$d->set('EffectHousesAffected' , valueToDIField($a[28],$a[29]));

		$d->set('EffectPeopleEvacuated', valueToDIField($a[30],$a[31]));

		// 32
		$d->set('EffectRoads', valueToDIField($a[32]));
		// 33
		$d->set('EffectFarmingAndForest', valueToDIField($a[33]));
		// 34
		$d->set('EffectLiveStock', valueToDIField($a[34]));
		// 35
		$d->set('EffectEducationCenters', valueToDIField($a[35]));
		// 36-37
		$d->set('EffectPeopleRelocated', valueToDIField($a[36], $a[37]));
		// 38
		$d->set('SectorTransport', valueToDIField($a[38]));
		// 39
		$d->set('SectorAgricultural', valueToDIField($a[39]));
		// 40
		$d->set('SectorCommunications', valueToDIField($a[40]));
		// 41
		$d->set('SectorPower', valueToDIField($a[41]));
		// 42
		$d->set('SectorEducation', valueToDIField($a[42]));
		// 43
		$d->set('EffectMedicalCenters', valueToDIField($a[43]));
		// 44
		$d->set('SectorRelief', valueToDIField($a[44]));
		// 45
		$d->set('SectorWaterSupply', valueToDIField($a[45]));
		// 46
		$d->set('SectorSewerage', valueToDIField($a[46]));
		// 47
		$d->set('SectorIndustry', valueToDIField($a[47]));
		// 48
		$d->set('SectorHealth', valueToDIField($a[48]));
		// 49 - Sector Other
		$d->set('SectorOther', valueToDIField($a[49]));
		// 50 - EffectLossesValueLocal
		$d->set('EffectLossesValueLocal', $a[50]);
		// 51 - EffectLossesValueUSD
		$d->set('EffectLossesValueUSD', $a[51]);
		// 52
		$d->set('EventMagnitude', $a[52]);
		// 53 EffectOtherLosses
		$d->set('EffectOtherLosses', $a[53]);
		// 54-56 EffectNotes
		$d->set('EffectNotes', $a[54] . $a[55] . $a[56]);
		//57 RecordCreation
		$d->set('RecordCreation', valueToDate($a[57]));
		// 58
		$d->set('RecordAuthor', $a[58]);

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
		printf('%4d %-10s %s' . "\n", $line, $DisasterSerial, $mode);
		if ( ($i < 0) || ($j < 0) ) {
			printf('%4d %-10s %d %d' . "\n", $line, $DisasterSerial, $i, $j);
		}
		if (($line > 0) && (($line % 100) == 0) ) {
			//print $line . "\n";
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
	if (strlen($prmDate) == 10) {
		$day   = substr($prmDate,8,2);
		$month = substr($prmDate,5,2);
		$year  = substr($prmDate,0,4);
		$v = sprintf('%4d-%2d-%2d', $year, $month, $day);
		$v = str_replace(' ', '0', $v);
	}
	return $v;
}

</script>
