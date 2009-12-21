#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2009 Corporacion OSSO
  
  2009-12-21 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from DGR (Direccion de Gestion del Riesgo) 
  SIGPAD - Colombia
*/

$_SERVER["DI8_WEB"] = '../web';
require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/didisaster.class.php');
require_once(BASE . '/include/dieedata.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/dievent.class.php');

$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
$us->login('diadmin','di8');
$us->open($RegionId);

$r = new DIRegion($us, $RegionId);
$r->copyEvents('spa');
$r->copyCauses('spa');

$line = 1;
while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 1000, ',');
	if (count($a) > 1) {
		for($i = 0; $i<count($a); $i++) {
			$a[$i] = trim($a[$i]);
		}
		$DisasterBeginTime = strToISO8601($a[0]);
		if ($DisasterBeginTime != '') {
			$d = new DIDisaster($us);
			
			$DisasterSerial = substr($DisasterBeginTime, 0, 4);
			$DisasterSerial .= '-0001';
			$d->set('DisasterSerial', $DisasterSerial);
			
			$d->set('DisasterSource', 'DGR');
			
			// 0 - DisasterBeginTime
			$d->set('DisasterBeginTime', $DisasterBeginTime);
			
			// 1 - GeographyName (Departamento)
			$Dpto = $a[1];
			$Mpio = $a[2];
			// 2 - GeographyName (Municipio)
			$GeographyId = DIGeography::getIdByName($us, $Dpto, '');
			if ($GeographyId != '') {
				$GeographyId = DIGeography::getIdByName($us, $Mpio, $GeographyId);
			}
			$d->set('GeographyId', $GeographyId);
			
			// 3 - Cause
			$CauseName = $a[3];
			if ($CauseName == 'No especificado') { $CauseName = 'Otra causa'; }
			$CauseId = DICause::getIdByName($us, $CauseName);
			$d->set('CauseId', $CauseId);
			
			// 4 - Event
			$EventName = $a[4];
			$EventId = DIEvent::getIdByName($us, $EventName);
			$d->set('EventId', $EventId);
			
			// 5 - EffectPeopleDead
			$d->set('EffectPeopleDead', valueToDIField($a[5]));
			// 6 - EffectPeopleInjured
			$d->set('EffectPeopleInjured', valueToDIField($a[6]));
			// 7 - EffectPeopleMissing
			$d->set('EffectPeopleMissing', valueToDIField($a[7]));
			// 8
			$d->set('EffectPeopleAffected', valueToDIField($a[8]));
			// 10
			$d->set('EffectHousesDestroyed', valueToDIField($a[10]));
			// 11 - 28
			$d->set('EffectHousesAffected', valueToDIField($a[11]));
			// 12
			$d->set('EffectRoads', valueToDIField($a[12]));
			// 15
			$d->set('SectorWaterSupply', valueToDIField($a[15]));
			// 16
			$d->set('SectorSewerage', valueToDIField($a[16]));
			// 17
			$d->set('EffectMedicalCenters', valueToDIField($a[17]));
			// 18
			$d->set('EffectEducationCenters', valueToDIField($a[18]));
			// 20
			$d->set('EffectFarmingAndForest', valueToDIField($a[20]));
			// 21
			$d->set('SectorAgricultural', valueToDIField($a[21]));
			// 22
			$d->set('SectorIndustry', valueToDIField($a[22]));
			// 23
			$d->set('SectorPower', valueToDIField($a[23]));
			// 24
			$d->set('SectorEducation', valueToDIField($a[24]));
			// 26
			$d->set('SectorTransport', valueToDIField($a[26]));
			// 27
			$d->set('SectorCommunications', valueToDIField($a[27]));
			// 30
			$d->set('EffectPeopleEvacuated', valueToDIField($a[30]));
			// 31
			$d->set('EffectOtherLosses', $a[31]);
			
			// Extended Fields
			//  9 -
			// 13 -
			// 14 -
			// 19 -
			$v = $d->validateUpdate();
			if ($v < 0) {
				//fb('Line : ' . $line . ' ' . $v);
				if ($v == -58) {
					print $line . ',' . $a[1] . ',' . $a[2] . "\n";
				}
			}
		}
	}
	$line++;
} //while

$us->close();
$us->logout();
exit();

function valueToDIField($prmValue) {
	$Value = 0;
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
	$a = array();
	preg_match('/([0-9]+) de (.*) de ([0-9]+)/', $prmDate, $a);
	if ( (count($a) > 2) && (is_numeric($a[3])) ) {
		$year = $a[3] + 2000;
		$month = getMonth($a[2]);
		$day = $a[1];
		$v = sprintf('%4d-%2d-%2d', $year, $month, $day);
		$v = str_replace(' ', '0', $v);
	}
	return $v;
}

function getMonth($prmMonthName) {
	$m = array('Jan' =>  1, 'Feb' =>  2, 'Mar' =>  3, 'Apr' =>  4, 'May' =>  5, 'Jun' =>  6, 
	           'Jul' =>  7, 'Aug' =>  8, 'Sep' =>  9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12);
	$v = 0;
	if (array_key_exists($prmMonthName, $m)) {
		$v = $m[$prmMonthName];
	}
	return $v;
}

</script>
