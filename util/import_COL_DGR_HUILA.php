#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2011 Corporacion OSSO
  
  2011-02-09 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from DGR (Direccion de Gestion del Riesgo) 2010
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


$RegionId = 'COL-HUILA';
$us->login('diadmin','di8');
$us->open($RegionId);

//createEEFields();

$r = new DIRegion($us, $RegionId);
//$r->copyEvents('spa');
//$r->copyCauses('spa');

$line = 1;
$a = fgetcsv(STDIN, 0, ',');
$a = fgetcsv(STDIN, 0, ',');
while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 0, ',');
	if (count($a) > 1) {
		for($i = 0; $i<count($a); $i++) {
			$a[$i] = trim($a[$i]);
		}
		// 0 - DisasterSerial
		$DisasterSerial = $a[0];
		$p = $us->getDisasterIdFromSerial($DisasterSerial);
		$DisasterId = $p['DisasterId'];		
		$DisasterBeginTime = strToISO8601($a[1]);
		//printf('%-10s %-20s' . "\n", $DisasterSerial, $DisasterBeginTime);
		//$DisasterBeginTime = '';
		if ($DisasterBeginTime != '') {
			$d = new DIDisaster($us, $DisasterId);
			
			$d->set('DisasterSerial', $DisasterSerial);
			
			$d->set('DisasterSource', 'DGR-HUILA');
			
			// 1 - DisasterBeginTime
			$d->set('DisasterBeginTime', $DisasterBeginTime);
			$MpioFixes = array(
							'00013' => array( // Huila
								'DEPARTAMENTO'             => '',
								'BOMBEROS GARZÓN' => 'Garzón',
								'ELÍAS'          => 'Elías',
								'ELIAS'          => 'Elías',
								'GRAZON'         => 'Garzón',
								'GARZÓN'         => 'Garzón',
								'GARZON'         => 'Garzón',
								'NÁTAGA'         => 'Nátaga',
								'NATAGA'         => 'Nátaga',
								'PLATA'          => 'La Plata',
								'SALADO BLANCO'  => 'Saladoblanco',
								'SAN AGUSTÍN'    => 'San Agustín',
								'SAN AGUSTIN'    => 'San Agustín',
								'SANTA MARIA'    => 'Santa María',
								'SANTA MARÍA'    => 'Santa María',
								'TIMANA'         => 'Timaná',
								'TIMANÁ'         => 'Timaná',
								'YAGUARA'        => 'Yaguará',
								'YAGUARÁ'        => 'Yaguará'
							)
						);
			// 2-3 - GeographyName (Departamento/Municipio)
			$Dpto = $a[2];
			$Mpio = $a[3];
			$DptoCode = DIGeography::getIdByName($us, $Dpto, '');
			$GeographyId = $DptoCode;

			if ( array_key_exists($DptoCode, $MpioFixes) && array_key_exists($Mpio, $MpioFixes[$DptoCode]) )
			{
				$Mpio = $MpioFixes[$DptoCode][$Mpio];
			}
			if ($GeographyId != '') {
				if ($Mpio != '') {
					$GeographyId = DIGeography::getIdByName($us, $Mpio, $GeographyId);
				}
			}
			$d->set('GeographyId', $GeographyId);
			if ($GeographyId == '')
			{
				printf('GEOGRAPHY ERROR : %-10s %-5s %-20s %-20s' . "\n", $DisasterSerial, $DptoCode, $Dpto, $Mpio);
			}

			// 4 - Cause
			$CauseName = 'Desconocida'; // There is no Cause column in original data...
			$CauseFix = array(
							'No especificado' => 'Otra causa',
						);
			if (array_key_exists($CauseName, $CauseFix))
			{
				$CauseName = $CauseFix[$CauseName];
			}
			$CauseId = DICause::getIdByName($us, $CauseName);
			$d->set('CauseId', $CauseId);
			if ($CauseId == '')
			{
				printf('CAUSE ERROR : %-10s %-20s' . "\n", $DisasterSerial, $CauseName);
			}
			
			// 5 - Event
			$EventName = $a[5];
			$EventFix = array(
			                'ACCIDENTE DE TRANSITO'   => 'Accidente',
			                'ACCIDENTE DE TRÁNSITO'   => 'Accidente de Tránsito',
			                'CAÍDA DE ÁRBOL'          => 'Caída de Arbol',
			                'INSPECCIÓN'              => 'Inspección',
			                'INFORMACION'             => 'Otro',
			                'VENDAVAL-LLUVIA'         => 'Vendaval',
			                'LLUVIAS-DESLIZAMIENTO'   => 'Lluvias',
			                'CRECIENTE( SIN CONFIRMAR)' => 'Creciente',
			                'COLAPSO-CAIDA DE VIVIENDA' => 'Colapso Estructural',
			                'DESLIZAMIENTO(SIN CONFIRMAR)' => 'Deslizamiento',
			                'INUNDACION-DERRUMBES'    => 'Inundación',
			                'ACCIDENTE VEHICULAR'     => 'Accidente de Tránsito',
			                'INCENDIO ELÉCTRICO'      => 'Incendio',
			                'DESLIZAMIENTO DE TIERRA' => 'Deslizamiento',
			                'FUERTES LLUVIAS'         => 'Lluvias',
			                'INCENDIOS ESTRUCTURAL'   => 'Incendio',
			                'AVALANCHA-DESLIZAMIENTO' => 'Deslizamiento',
			                'DESLIZAMIENTO DE TIERRA-VENDAVAL' => 'Deslizamiento',
			                'INUNDACION-DESLIZAMIENTO DE TIERRA' => 'Inundación',
			                'LLUVIAS-VENDAVAL'        => 'Lluvias',
			                'VENDAVAL-DESLIZAMIENTO DE TIERRA' => 'Vendaval',
			                'DESLIZAMIENTO DE TIERRA INUNDACION' => 'Deslizamiento',
			                'DESLIZAMIENTO DE TIERRA-INUNDACION' => 'Deslizamiento',
			                'DESLIZAMIENTO-INUNDACION' => 'Deslizamiento',
			                'INUNDACIONES-VENDAVALES' => 'Inundación',
			                'LLUVIAS TORRENCIALES'    => 'Lluvias',
			                'ERUPCION-AVALANCHA'      => 'Avalancha',
			                'INTOXICACION MASIVA'     => 'Intoxicación',
			                'CAIDA DE ARBOL'          => 'Corte de Arbol',
			                'CORTE DE ÁRBOL'          => 'Corte de Arbol',
			                'FALLA GEOLOGICA'         => 'Falla Geológica',
			                'INUNDACIÓN'              => 'Inundación',
							'TORMENTA ELECTRICA'      => 'Tormenta eléctrica',
							'AVALANCHA'               => 'Alud',
							'EROSION'                 => 'Erosión',
							'LLUVIA'                  => 'Lluvias'
						);
			if (array_key_exists($EventName, $EventFix))
			{
				$EventName = $EventFix[$EventName];
			}
			$EventId = DIEvent::getIdByName($us, $EventName);
			$d->set('EventId', $EventId);
			if ($EventId == '')
			{
				printf('EVENT ERROR : %-10s %-20s' . "\n", $DisasterSerial, $EventName);
			}

			// 7 - DisasterSiteNotes
			$d->set('DisasterSiteNotes'     , $a[6] . ' ' . $a[7]);

			// 8 - EventNotes
			$d->set('EventNotes'            , $a[8]);
			
			// 9 - 12 Effects (Basic) 
			$d->set('EffectPeopleDead'      , valueToDIField($a[9]));
			$d->set('EffectPeopleInjured'   , valueToDIField($a[10]));
			$d->set('EffectPeopleMissing'   , valueToDIField($a[11]));
			$d->set('EffectPeopleAffected'  , valueToDIField($a[12]));
			// 13 Familias (EEF) ?
			$d->set('EffectHousesDestroyed' , valueToDIField($a[14]));
			$d->set('EffectHousesAffected'  , valueToDIField($a[15]));
			$d->set('EffectRoads'           , valueToDIField($a[16]));
			// 17 Puentes Vehic
			// 18 Puentes Peatonales
			$d->set('SectorWaterSupply'     , valueToDIField($a[19]));
			$d->set('SectorSewerage'        , valueToDIField($a[20]));
			$d->set('EffectMedicalCenters'  , valueToDIField($a[21]));
			$d->set('EffectEducationCenters', valueToDIField($a[22]));
			// 23 Centros Comunitarios
			$d->set('EffectFarmingAndForest', valueToDIField($a[24]));
			$d->set('EffectNotes'           , $a[25]);

			$DisasterId = $d->get('DisasterId');
			$e = new DIEEData($us, $DisasterId);

			$e->set('EEF004', valueToDIField($a[13]));  // 13 - Familias Afectadas
			$e->set('EEF005', valueToDIField($a[17]));  // 17 - Puentes Vehiculares
			$e->set('EEF006', valueToDIField($a[18]));  // 18 - Puentes Peatonales
			$e->set('EEF008', valueToDIField($a[23]));  // 20 - Centros Comunitarios
			$e->set('EEF009',   strToISO8601($a[26]));  // 26 - Fecha Tramite Administrativo
			$e->set('EEF010', valueToDIField($a[27]));  // 27 - Menajes
			$e->set('EEF011', valueToDIField($a[28]));  // 28 - Ap. Aliment.
			$e->set('EEF012', valueToDIField($a[29]));  // 29 - Materiales Construccion
			$e->set('EEF013', valueToDIField($a[30]));  // 30 - Sacos
			$e->set('EEF014', valueToDIField($a[31]));  // 31 - Otros
			$e->set('EEF016', valueToDIField($a[32]));  // 32 - Giro Directo
			$e->set('EEF017', valueToDIField($a[33]));  // 33 - Valor Total

			// Validate Effects and Save as DRAFT if needed
			if ($d->validateEffects(-61) < 0) 
			{
				$d->set('RecordStatus', 'DRAFT');
			}

			$Cmd = '';
			$bExist = $d->exist();
			if ($bExist < 0) {
				$i = $d->insert(true,false);
				$j = $e->insert(true,false);
				$Cmd = 'INSERT';
			} else {
				$i = $d->update(true,false);
				$j = $e->update(true,false);
				$Cmd = 'UPDATE';
			}

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
		$year  = substr($prmDate,0,4);
		$month = substr($prmDate,5,2);
		$day   = substr($prmDate,8,2);
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
