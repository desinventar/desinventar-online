#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-01-21 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from DGR (Direccion de Gestion del Riesgo) 
  SIGPAD - Colombia
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


$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
//$RegionId = 'GAR-ISDR-2011_COL';
$us->login('diadmin','di8');
$us->open($RegionId);

//createEEFields();

$r = new DIRegion($us, $RegionId);
//$r->copyEvents('spa');
//$r->copyCauses('spa');

$line = 1;

$a = fgetcsv(STDIN, 1000, ',');
while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 1000, ',');
	if (count($a) > 1) {
		for($i = 0; $i<count($a); $i++) {
			$a[$i] = trim($a[$i]);
		}
		// 0 - DisasterSerial
		$DisasterSerial = $a[0];
		$p = $us->getDisasterIdFromSerial($DisasterSerial);
		$DisasterId = $p['DisasterId'];		
		$DisasterBeginTime = strToISO8601($a[1]);
		if ($DisasterBeginTime != '') {
			$d = new DIDisaster($us, $DisasterId);
			
			$d->set('DisasterSerial', $DisasterSerial);
			
			$d->set('DisasterSource', 'DGR');
			
			// 1 - DisasterBeginTime
			$d->set('DisasterBeginTime', $DisasterBeginTime);
			
			// 2-3 - GeographyName (Departamento/Municipio)
			$Dpto = $a[2];
			$Mpio = $a[3];
			$GeographyId = DIGeography::getIdByName($us, $Dpto, '');
			if ($GeographyId != '') {
				if ($Mpio != '') {
					$GeographyId = DIGeography::getIdByName($us, $Mpio, $GeographyId);
				}
			}
			$d->set('GeographyId', $GeographyId);
			
			// 4 - Cause
			$CauseName = $a[4];
			if ($CauseName == 'No especificado') { $CauseName = 'Otra causa'; }
			$CauseId = DICause::getIdByName($us, $CauseName);
			$d->set('CauseId', $CauseId);
			
			// 5 - Event
			$EventName = $a[5];
			$EventId = DIEvent::getIdByName($us, $EventName);
			$d->set('EventId', $EventId);
			
			// 6-9 - Effects (Basic)
			$d->set('EffectPeopleDead', valueToDIField($a[6]));
			$d->set('EffectPeopleInjured', valueToDIField($a[7]));
			$d->set('EffectPeopleMissing', valueToDIField($a[8]));
			$d->set('EffectPeopleAffected', valueToDIField($a[9]));

			// 11-12 Effects (Houses)
			$d->set('EffectHousesDestroyed', valueToDIField($a[11]));
			$d->set('EffectHousesAffected', valueToDIField($a[12]));
			// 13 - 
			$d->set('EffectRoads', valueToDIField($a[13]));
			// 16
			$d->set('SectorSewerage', valueToDIField($a[17]));
			// 17
			$d->set('EffectMedicalCenters', valueToDIField($a[18]));
			// 18
			$d->set('EffectEducationCenters', valueToDIField($a[19]));
			// 20
			$d->set('EffectFarmingAndForest', valueToDIField($a[21]));
			// 21
			$d->set('SectorAgricultural', valueToDIField($a[22]));
			// 22
			$d->set('SectorIndustry', valueToDIField($a[23]));
			// 23
			$d->set('SectorPower', valueToDIField($a[24]));
			// 24
			$d->set('SectorEducation', valueToDIField($a[25]));
			// 25
			$d->set('SectorWaterSupply', valueToDIField($a[25]));
			// 26
			$d->set('SectorTransport', valueToDIField($a[27]));
			// 27
			$d->set('SectorCommunications', valueToDIField($a[28]));
			// 30
			$d->set('EffectPeopleEvacuated', valueToDIField($a[31]));
			// 31
			$d->set('EffectOtherLosses', $a[32]);
			
			// 32 - 46 EffectNotes
			$d->set('EffectNotes', $a[46]);

			//$bExist = $d->exist();
			//if ($bExist < 0) {
			//	$i = $d->insert();
			//} else {
				$i = $d->update();
			//}
			$DisasterId = $d->get('DisasterId');
			$e = new DIEEData($us, $DisasterId);
			//  9 - Familias Afectadas
			$e->set('EEF004', valueToDIField($a[10]));
			// 13 - Puentes Vehiculares
			$e->set('EEF005', valueToDIField($a[14]));
			// 14 - Puentes Peatonales
			$e->set('EEF006', valueToDIField($a[15]));
			// 15 - Acueductos Afectados
			$e->set('EEF007', valueToDIField($a[16]));
			// 19 - Centros Comunitarios
			$e->set('EEF008', valueToDIField($a[20]));
			// 32 - Fecha Tramite Administrativo
			$e->set('EEF009', strToISO8601($a[33]));
			// 33 - Menajes
			$e->set('EEF010', valueToDIField($a[34]));
			// 34 - Ap. Aliment
			$e->set('EEF011', valueToDIField($a[35]));
			$e->set('EEF012', valueToDIField($a[36]));
			$e->set('EEF013', valueToDIField($a[37]));
			$e->set('EEF014', valueToDIField($a[38]));
			$e->set('EEF015', valueToDIField($a[39]));
			$e->set('EEF016', valueToDIField($a[40]));
			$e->set('EEF017', valueToDIField($a[41]));
			$e->set('EEF018', valueToDIField($a[42]));
			$e->set('EEF019', strToISO8601($a[43]));
			$e->set('EEF020', strToISO8601($a[44]));
			$e->set('EEF021', valueToDIField($a[45]));
			$e->set('EEF022', $a[46]);
			$e->set('EEF023', valueToDIField($a[47]));
			$e->set('EEF024', valueToDIField($a[48]));
			$e->set('EEF025', valueToDIField($a[49]));
			$e->set('EEF026', valueToDIField($a[50]));
			$e->set('EEF027', valueToDIField($a[51]));
			$e->set('EEF028', valueToDIField($a[52]));
			$e->set('EEF029', valueToDIField($a[53]));
			$e->set('EEF030', valueToDIField($a[54]));
			$e->set('EEF031', valueToDIField($a[55]));
			$e->set('EEF032', valueToDIField($a[56]));
			$e->set('EEF033', valueToDIField($a[57]));
			$e->set('EEF034', valueToDIField($a[58]));
			$e->set('EEF035', valueToDIField($a[59]));
			$e->set('EEF036', valueToDIField($a[60]));
			$e->set('EEF037', valueToDIField($a[61]));
			$e->set('EEF038', valueToDIField($a[62]));
			$e->set('EEF039', valueToDIField($a[63]));
			$e->set('EEF040', valueToDIField($a[64]));
			$e->set('EEF041', valueToDIField($a[65]));
			$e->set('EEF042', valueToDIField($a[66]));
			$e->set('EEF043', valueToDIField($a[67]));
			$e->set('EEF044', valueToDIField($a[68]));
			$e->set('EEF045', valueToDIField($a[69]));
			$e->set('EEF046', valueToDIField($a[70]));
			$e->set('EEF047', valueToDIField($a[71]));
			$e->set('EEF048', valueToDIField($a[72]));
			$e->set('EEF049', valueToDIField($a[73]));
			$e->set('EEF050', valueToDIField($a[74]));
			$e->set('EEF051', valueToDIField($a[75]));
			$e->set('EEF052', valueToDIField($a[76]));
			$e->set('EEF053', valueToDIField($a[77]));
			$e->set('EEF054', valueToDIField($a[78]));
			$e->set('EEF055', valueToDIField($a[79]));
			$e->set('EEF056', valueToDIField($a[80]));
			$e->set('EEF057', valueToDIField($a[81]));
			$e->set('EEF058', valueToDIField($a[82]));
			$e->set('EEF059', valueToDIField($a[83]));
			$e->set('EEF060', valueToDIField($a[84]));
			$e->set('EEF061', valueToDIField($a[85]));
			$e->set('EEF062', valueToDIField($a[86]));
			$e->set('EEF063', valueToDIField($a[87]));
			$e->set('EEF064', valueToDIField($a[88]));
			$e->set('EEF065', valueToDIField($a[89]));
			$e->set('EEF066', valueToDIField($a[90]));
			$e->set('EEF067', valueToDIField($a[91]));
			$e->set('EEF068', valueToDIField($a[92]));
			$e->set('EEF069', valueToDIField($a[93]));
			$e->set('EEF070', valueToDIField($a[94]));
			$e->set('EEF071', valueToDIField($a[95]));
			$e->set('EEF072', valueToDIField($a[96]));
			$e->set('EEF073', valueToDIField($a[97]));
			$e->set('EEF074', valueToDIField($a[98]));
			$e->set('EEF075', valueToDIField($a[99]));
			$e->set('EEF076', valueToDIField($a[100]));
			$e->set('EEF077', valueToDIField($a[101]));
			$e->set('EEF078', valueToDIField($a[102]));
			$e->set('EEF079', valueToDIField($a[103]));
			$e->set('EEF080', valueToDIField($a[104]));
			$e->set('EEF081', valueToDIField($a[105]));
			$e->set('EEF082', valueToDIField($a[106]));
			$e->set('EEF083', valueToDIField($a[107]));
			$e->set('EEF084', valueToDIField($a[108]));
			$e->set('EEF085', valueToDIField($a[109]));
			$e->set('EEF086', valueToDIField($a[110]));
			$e->set('EEF087', valueToDIField($a[111]));
			$e->set('EEF088', valueToDIField($a[112]));
			$e->set('EEF089', valueToDIField($a[113]));
			$e->set('EEF090', valueToDIField($a[114]));
			$e->set('EEF091', valueToDIField($a[115]));
			$e->set('EEF092', valueToDIField($a[116]));
			$e->set('EEF093', valueToDIField($a[117]));
			//if ($bExist < 0) {
			//	$j = $e->insert();
			//} else {
				$j = $e->update();
			//}
			if ( ($i < 0) || ($j < 0) ) {
				print $line . ' ' . $DisasterSerial . ' ' . $i . ' ' . $j . "\n";
			}			
			if (($line > 0) && (($line % 100) == 0) ) {
				print $line . "\n";
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
		$day   = substr($prmDate,0,2);
		$month = substr($prmDate,3,2);
		$year  = substr($prmDate,6,4);
		$v = sprintf('%4d-%2d-%2d', $year, $month, $day);
		$v = str_replace(' ', '0', $v);
	}
	/*
	$a = array();
	preg_match('/([0-9]+) de (.*) de ([0-9]+)/', $prmDate, $a);
	if ( (count($a) > 2) && (is_numeric($a[3])) ) {
		$year = $a[3] + 2000;
		$month = getMonth($a[2]);
		$day = $a[1];
		$v = sprintf('%4d-%2d-%2d', $year, $month, $day);
		$v = str_replace(' ', '0', $v);
	}
	*/
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
	$f->set('EEGroupId', 'DGR');
	$f->set('EEFieldLabel', $EEFieldLabel);
	$f->set('EEFieldType', $EEFieldType);
	if ($EEFieldSize != '') {
		$f->set('EEFieldSize', $EEFieldSize);
	}
	$i = $f->insert();
	return $i;
}

function createEEFields($us) {
	$i = createEEField($us, 'Familias Afectadas'          , 'INTEGER' );      //  4
	$i = createEEField($us, 'Puentes Vehiculares'         , 'INTEGER' );      //  5
	$i = createEEField($us, 'Puentes Peatonales'          , 'INTEGER' );      //  6
	$i = createEEField($us, 'Acueductos Afectados'        , 'INTEGER' );      //  7
	$i = createEEField($us, 'Centros Comunitarios'        , 'INTEGER' );      //  8
	$i = createEEField($us, 'Fecha Trámite Administrativo', 'DATE'    );      //  9 - 32
	$i = createEEField($us, 'Menajes'                     , 'CURRENCY');      // 10 - 33
	$i = createEEField($us, 'Ap. Aliment'                 , 'CURRENCY');      // 11 - 34
	$i = createEEField($us, 'Materiales Construcción'     , 'CURRENCY');      // 12 - 35
	$i = createEEField($us, 'Sacos'                       , 'CURRENCY');      // 13 - 36
	$i = createEEField($us, 'Otros'                       , 'CURRENCY');      // 14 - 37
	$i = createEEField($us, 'Giro Directo'                , 'CURRENCY');      // 15 - 38
	$i = createEEField($us, 'Econom.'                     , 'CURRENCY');      // 16 - 39
	$i = createEEField($us, 'Valor Total'                 , 'CURRENCY');      // 17 - 40
	$i = createEEField($us, 'Apoyos en Trámite'           , 'CURRENCY');      // 18 - 41
	$i = createEEField($us, 'Fecha Recibo'                , 'DATE'    );      // 19 - 42
	$i = createEEField($us, 'Trámite ante DPAD'           , 'DATE'    );      // 20 - 43
	$i = createEEField($us, 'Atendido'                    , 'BOOLEAN' );      // 21 - 44
	$i = createEEField($us, 'Comentarios'                 , 'STRING', 200);   // 22 - 45
	$i = createEEField($us, 'Número de Iglesias'          , 'INTEGER' );      // 23 - 46
	$i = createEEField($us, 'Iglesias Afectadas'          , 'INTEGER' );      // 24 - 47
	$i = createEEField($us, 'Cepillo Adulto (Cant)'       , 'INTEGER' );      // 25 - 48
	$i = createEEField($us, 'Cepillo Adulto (Valor)'      , 'CURRENCY');      // 26 - 49
	$i = createEEField($us, 'Cepillo Niño (Cant)'         , 'INTEGER' );      // 27 - 50
	$i = createEEField($us, 'Cepillo Niño (Valor)'        , 'CURRENCY');      // 28 - 51
	$i = createEEField($us, 'Chocolatera (Cant)'          , 'INTEGER' );      // 29 - 52
	$i = createEEField($us, 'Chocolatera (Valor)'         , 'CURRENCY');      // 30 - 53
	$i = createEEField($us, 'Cinta Empalmar (Cant)'       , 'INTEGER' );      // 31 - 54
	$i = createEEField($us, 'Cinta Empalmar (Valor)'      , 'CURRENCY');      // 32 - 55
	$i = createEEField($us, 'Cobija (Cant)'               , 'INTEGER' );      // 33 - 56
	$i = createEEField($us, 'Cobija (Valor)'              , 'CURRENCY');      // 34 - 57
	$i = createEEField($us, 'Cobija Térmica (Cant)'       , 'INTEGER' );      // 35 - 58
	$i = createEEField($us, 'Cobija Térmica (Valor)'      , 'CURRENCY');      // 36 - 59
	$i = createEEField($us, 'Colchoneta (Cant)'           , 'INTEGER' );      // 37 - 60
	$i = createEEField($us, 'Colchoneta (Valor)'          , 'CURRENCY');      // 38 - 61
	$i = createEEField($us, 'Catre (Cant)'                , 'INTEGER' );      // 39 - 62
	$i = createEEField($us, 'Catre (Valor)'               , 'CURRENCY');      // 40 - 63
	$i = createEEField($us, 'Crema Desodorante (Cant)'    , 'INTEGER' );      // 41 - 64
	$i = createEEField($us, 'Crema Desodorante (Valor)'   , 'CURRENCY');      // 42 - 65
	$i = createEEField($us, 'Cuchara Acero (Cant)'        , 'INTEGER' );      // 43 - 66
	$i = createEEField($us, 'Cuchara Acero (Valor)'       , 'CURRENCY');      // 44 - 67
	$i = createEEField($us, 'Cuchara Madera (Cant)'       , 'INTEGER' );      // 45 - 68
	$i = createEEField($us, 'Cuchara Madera (Valor)'      , 'CURRENCY');      // 46 - 69
	$i = createEEField($us, 'Estufas (Cant)'              , 'INTEGER' );      // 47 - 70
	$i = createEEField($us, 'Estufas (Valor)'             , 'CURRENCY');      // 48 - 71
	$i = createEEField($us, 'Hamacas (Cant)'              , 'INTEGER' );      // 49 - 72
	$i = createEEField($us, 'Hamacas (Valor)'             , 'CURRENCY');      // 50 - 73
	$i = createEEField($us, 'Jabón Baño (Cant)'           , 'INTEGER' );      // 51 - 74
	$i = createEEField($us, 'Jabón Baño (Valor)'          , 'CURRENCY');      // 52 - 75
	$i = createEEField($us, 'Jabón Barra (Cant)'          , 'INTEGER' );      // 53 - 76
	$i = createEEField($us, 'Jabón Barra (Valor)'         , 'CURRENCY');      // 54 - 77
	$i = createEEField($us, 'Juego Cubiertos (Cant)'      , 'INTEGER' );      // 55 - 78
	$i = createEEField($us, 'Juego Cubiertos (Valor)'     , 'CURRENCY');      // 56 - 79
	$i = createEEField($us, 'Ollas (Cant)'                , 'INTEGER' );      // 57 - 80
	$i = createEEField($us, 'Ollas (Valor)'               , 'CURRENCY');      // 58 - 81
	$i = createEEField($us, 'Papel Higiénico (Cant)'      , 'INTEGER' );      // 59 - 82
	$i = createEEField($us, 'Papel Higiénico (Valor)'     , 'CURRENCY');      // 60 - 83
	$i = createEEField($us, 'Peinilla (Cant)'             , 'INTEGER' );      // 61 - 84
	$i = createEEField($us, 'Peinilla (Valor)'            , 'CURRENCY');      // 62 - 85
	$i = createEEField($us, 'Plástico Negro (Cant)'       , 'INTEGER' );      // 63 - 86
	$i = createEEField($us, 'Plástico Negro (Valor)'      , 'CURRENCY');      // 64 - 87
	$i = createEEField($us, 'Plato Hondo (Cant)'          , 'INTEGER' );      // 65 - 88
	$i = createEEField($us, 'Plato Hondo (Valor)'         , 'CURRENCY');      // 66 - 89
	$i = createEEField($us, 'Plato Pando (Cant)'          , 'INTEGER' );      // 67 - 90
	$i = createEEField($us, 'Plato Pando (Valor)'         , 'CURRENCY');      // 68 - 91
	$i = createEEField($us, 'Pocillo (Cant)'              , 'INTEGER' );      // 69 - 92
	$i = createEEField($us, 'Pocillo (Valor)'             , 'CURRENCY');      // 70 - 93
	$i = createEEField($us, 'Sábanas (Cant)'              , 'INTEGER' );      // 71 - 94
	$i = createEEField($us, 'Sábanas (Valor)'             , 'CURRENCY');      // 72 - 95
	$i = createEEField($us, 'Sobrecamas (Cant)'           , 'INTEGER' );      // 73 - 96
	$i = createEEField($us, 'Sobrecamas (Valor)'          , 'CURRENCY');      // 74 - 97
	$i = createEEField($us, 'Toallas (Cant)'              , 'INTEGER' );      // 75 - 98
	$i = createEEField($us, 'Toallas (Valor)'             , 'CURRENCY');      // 76 - 99
	$i = createEEField($us, 'Toldillos (Cant)'            , 'INTEGER' );      // 77 - 100
	$i = createEEField($us, 'Toldillos (Valor)'           , 'CURRENCY');      // 78 - 101
	$i = createEEField($us, 'Kit Aseo (Cant)'             , 'INTEGER' );      // 79 - 102
	$i = createEEField($us, 'Kit Aseo (Valor)'            , 'CURRENCY');      // 80 - 103
	$i = createEEField($us, 'Kit Cocina (Cant)'           , 'INTEGER' );      // 81 - 104
	$i = createEEField($us, 'Kit Cocina (Valor)'          , 'CURRENCY');      // 82 - 105
	$i = createEEField($us, 'Kit Alcoba (Cant)'           , 'INTEGER' );      // 83 - 106
	$i = createEEField($us, 'Kit Alcoba (Valor)'          , 'CURRENCY');      // 84 - 107
	$i = createEEField($us, 'Meanenes (Valor)'            , 'CURRENCY');      // 85 - 108
	$i = createEEField($us, 'Sacos (Cantidad)'            , 'INTEGER' );      // 86 - 109
	$i = createEEField($us, 'Sacos (Valor)'               , 'CURRENCY');      // 87 - 110
	$i = createEEField($us, 'Mercados (Cant)'             , 'INTEGER' );      // 88 - 111
	$i = createEEField($us, 'Mercados (Valor)'            , 'CURRENCY');      // 89 - 112
	$i = createEEField($us, 'Cemento (Cant)'              , 'INTEGER' );      // 90 - 113
	$i = createEEField($us, 'Cemento (Valor)'             , 'CURRENCY');      // 91 - 114
	$i = createEEField($us, 'Tejas (Cant)'                , 'INTEGER' );      // 92 - 115
	$i = createEEField($us, 'Tejas (Valor)'               , 'CURRENCY');      // 93 - 116
}

</script>
