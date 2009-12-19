#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2009 Corporacion OSSO
  
  2009-12-18 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from DGR (Direccion de Gestion del Riesgo) 
  SIGPAD - Colombia
*/

$_SERVER["DI8_WEB"] = '../web';
require_once('../web/include/loader.php');
require_once(BASE . '/include/didisaster.class.php');
require_once(BASE . '/include/dieedata.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/dicause.class.php');

$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
$us->login('diadmin','di8');
$us->open($RegionId);

while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 1000, ',');
	if (count($a) > 1) {
		$DisasterBeginTime = strToISO8601($a[0]);
		if ($DisasterBeginTime != '') {
			$d = new DIDisaster($us);
			
			// 0 - DisasterBeginTime
			$d->set('DisasterBeginTime', $DisasterBeginTime);
			
			// 1 - GeographyName (Departamento)
			// 2 - GeographyName (Municipio)
			$g = DIGeography::loadByName($us, $a[1], '');
			$GeographyId = $g->get('GeographyId');
			if ($GeographyId != '') {
				$g = DIGeography::loadByName($us, $a[2], $GeographyId);
				if ($g->get('GeographyId') != '') {
					$GeographyId = $g->get('GeographyId');
				}
			}
			$d->set('GeographyId', $GeographyId);
			
			// 3 - Cause
			$CauseName = $a[3];
			if ($CauseName == 'No especificado') { $CauseName = 'Otra causa'; }
			$CauseId = DICause::getIdByName($us, $CauseName);
			$d->set('CauseId', $CauseId);
			//print $d->getInsertQuery() . "\n";
		}
	}
} //while

$us->close();
$us->logout();
exit();


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
