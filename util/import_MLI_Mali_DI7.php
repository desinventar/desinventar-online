#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2011 Corporacion OSSO
  
  2011-10-30 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data for MLI database using DI7 report.
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/didisaster.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dieefield.class.php');
require_once(BASE . '/include/date.class.php');


$RegionId = 'MLI-1272020970-mali_disaster_database';
$us->login('diadmin','di8');
$us->open($RegionId);

//createEEFields();

$r = new DIRegion($us, $RegionId);
//$r->copyEvents('spa');
//$r->copyCauses('spa');

$line = 1;

$a = fgetcsv(STDIN, 0, ',');

while (! feof(STDIN) )
{
	$a = fgetcsv(STDIN, 0, ',');
	if (count($a) > 1)
	{
		for($i = 0; $i<count($a); $i++)
		{
			$a[$i] = trim($a[$i]);
		}
		// 0 - DisasterSerial
		$DisasterSerial = $a[0];
		$p = $us->getDisasterIdFromSerial($DisasterSerial);
		$DisasterId = $p['DisasterId'];		
		$DisasterBeginTime = dateToISO8601($a[8]);
		printf('%-10s %-30s' . "\n", $DisasterSerial, $DisasterBeginTime);
		if ($DisasterBeginTime != '')
		{
			$d = new DIDisaster($us, $DisasterId);
			
			$d->set('DisasterSerial', $DisasterSerial);
			
			$d->set('DisasterSource', 'MLI');
			
			// 1 - DisasterBeginTime
			$d->set('DisasterBeginTime', $DisasterBeginTime);
			$DptoFixes = array(
							'ATLANTICO' => 'Atlántico',
							'BOGOTA D.C.' => 'Bogotá D.C.',
							'BOLIVAR'     => 'Bolívar',
							'BOYACA'      => 'Boyacá',
							'CAQUETA'     => 'Caquetá',
							'CHOCO'       => 'Chocó',
							'CORDOBA'     => 'Córdoba',
							'GUAINIA'     => 'Guainía',
							'GUAJIRA'     => 'La Guajira',
							'NARIÑO'      => 'Nariño',
							'QUINDIO'     => 'Quindio',
							'SAN ANDRES'  => 'Archipiélago de San Andrés Providencia y Santa Catalina'
						 );
			$MpioFixes = array(
							'00001' => array( // Antioquia
								'APARTADO' => 'Apartadó',
								'MEDELLIN'         => 'Medellín',
								'MURINDO'          => 'Murindó',
								'YONDO'            => 'Yondó',
								'VIGIA DEL FUERTE' => 'Vigía del Fuerte',
								'TITIRIBI'  => 'Titiribí',
								'MUTATA'    => 'Mutatá',
								'JERICO'    => 'Jericó',
								'RIO NEGRO' => 'Rionegro',
								'NECOCLI'   => 'Necoclí',
								'NECHI'     => 'Nechí',
								'TAMESIS'   => 'Támesis',
								'LA UNION'  => 'La Unión',
								'CHIGORODO' => 'Chigorodó',
								'VEGACHI'   => 'Vegachí',
								'BEGACHI'   => 'Vegachí',
								'ENTRERIOS' => 'Entrerrios',
								'SANTAFE DE ANTIOQUIA' => 'Santafé de Antioquia',
								'YALI'      => 'Yalí',
								'CIUDAD BOLIVAR' => 'Ciudad Bolívar',
								'BAGRE'     => 'El Bagre',
								'SAN PEDRO DE URABA' => 'San Pedro de Urabá',
								'SANTUARIO' => 'El Santuario',
								'SAN VICENTE DE FERRER' => 'San Vicente',
								'ANORI' => 'Anorí',
								'SANTA BARBARA' => 'Santa Bárbara',
								'NARIÑO' => 'Nariño',
								'AMAGA' => 'Amagá',
								'MACEO' => 'Meceo',
								'SAN ANDRES DE CUERQUIA' => 'San Andrés de Cuerquía',
								'BRICEÑO' => 'Briceño',
								'JARDIN' => 'Jardín',
								'DEPARTAMENTO' => ''
							),
							'00025' => array( // Arauca
								'PUERTO RONDON' => 'Puerto Rondón',
								'DEPARTAMENTO'  => ''
							),
							'00002' => array( // Atlántico
								'USIACURI' => 'Usiacurí',
								'TUBARA'   => 'Tubará',
								'PIOJO'    => 'Piojó',
								'DEPARTAMENTO' => '',
								'MANATI'       => 'Manatí',
								'POLO NUEVO' => 'Polonuevo',
								'REPELON'      => 'Repelón',
								'SANTO TOMAS'  => 'Santó Tomás',
								'SANTA LUCIA'  => 'Santa Lucía'
							),
							'00004' => array( // Bolívar
								'ARENAL SUR' => 'Arenal',
								'ARROYO HONDO' => 'Arroyohondo',
								'CARMEN DE BOLIVAR' => 'El Carmen de Bolívar',
								'ACHI' => 'Achí',
								'RIOVIEJO' => 'Río Viejo',
								'DEPARTAMENTO' => '',
								'TIQUISIO' => 'Tiquiso',
								'CORDOBA' => 'Córdoba',
								'SIMITI' => 'Simití',
								'EL PEÑON' => 'El Peñón',
								'SANTA ROSA SUR' => 'Santa Rosa del Sur',
								'MAGANGUE' => 'Magangué',
								'NOROSI' => 'Norosí',
								'SAN CRISTOBAL' => 'San Cristóbal',
								'SOPLAVIENTO' => 'Soplavento',
								'MOMPOX' => 'Mompós',
								'MARIA LA BAJA' => 'María la Baja',
								'SAN JUAN NEPOMUCENO' => 'San Juan de Nepomuceno',
								'SAN JUAN NEPUMUCENO' => 'San Juan de Nepomuceno',
								'SAN MARTIN DE LOBA'  => 'San Martín de Loba',
								'SAN ESTANISLAO DE KOSTA' => 'San Estanislao',
								'SANTA ROSA DE LIMA' => 'Santa Rosa',
								'TURBANA' => 'Turbaná'
							),
							'00005' => array( // Boyacá
								'CIENEGA' => 'Ciénaga',
								'CUITIVA' => 'Cuítiva',
								'CHIQUINQUIRA' => 'Chiquinquirá',
								'COMBITA' => 'Cómbita',
								'COOPER' => 'Coper',
								'GACHANTIVA' => 'Gachantivá',
								'QUIPAMA' => 'Quípama',
								'GUICAN'  => 'Güican',
								'BELEN'   => 'Belén',
								'JERICO'  => 'Jericó',
								'OICATA'  => 'Oicatá',
								'PAEZ'    => 'Páez',
								'SABOYA'  => 'Saboyá',
								'SOATA'   => 'Soatá',
								'TIBANA'  => 'Tibaná',
								'RAQUIRA' => 'Ráquira',
								'MONIQUIRA' => 'Moniquirá',
								'MONGOA' => 'Mongua',
								'PUERTO BOYACA' => 'Puerto Boyacá',
								'SOGAMOSO'      => 'Sogamosos',
								'SOGAMOZO'      => 'Sogamosos',
								'SOTAQUIRA'    => 'Sotaquirá',
								'DEPARTAMENTO'  => '',
								'SANTA ANA'  => 'Santana',
								'SORACA'   => 'Soracá',
								'SUSACON'   => 'Susacón',
								'CIENAGA'   => 'Ciénaga',
								'MONGUI'    => 'Monguí',
								'TOPAGA'    => 'Tópaga',
								'TUTASA'    => 'Tutazá',
								'MARIPI'    => 'Maripí',
								'PAZ DE RIO' => 'Paz de Río'								
							),
							'00006' => array( // Caldas
								'LA VICTORIA' => 'Victoria',
								'PACORA' => 'Pácora',
								'SAMANA' => 'Samaná',
								'SUPIA' => 'Supía',
								'VILLA MARIA' => 'Villamaria'
							),
							'00007' => array( // Caquetá
								'BELEN DE LOS ANDAQUIES' => 'Belén de los Andaquíes',
								'DONCELLO' => 'El Doncello',
								'LA MONTAÑITA' => 'La Montañita',
								'MILAN' => 'Milán',
								'PAUJIL' => 'El Paujil',
								'SAN JOSE DEL FRAGUA' => 'San José del Fragua',
								'SOLITA' => 'Salita'
							),
							'00026' => array( //Casanare
								'MANI' => 'Maní',
								'NUNCHIA' => 'Nunchía',
								'OROCUE' => 'Orocué'
							),
							'00025' => array( // Arauca
								'PUERTO RONDON' => 'Puerto Rondón',
							),
							'00008' => array( // Cauca
								'BOLIVAR' => 'Bolívar',
								'GUACHENE' => 'Gachené',
								'JAMBALO' => 'Jambaló',
								'MONDOMO' => 'Santander de Quilichao',
								'PIENDAMO' => 'Piendamó',
								'PURACE' => 'Puracé',
								'TIMBIQUI' => 'Timbiquí',
								'INZA' => 'Inzá',
								'POPAYAN' => 'Popayán',
								'CAJIBIO' => 'Cajibío',
								'SUAREZ' => 'Suárez',
								'LOPEZ DE MICAY' => 'López',
								'TIMBA' => 'Buenos Aires',
								'TIMBIO' => 'Timbío',
								'PATIA' => 'Patía',
								'SAN SEBASTIAN' => 'San Sebastián',
								'TOTORO' => 'Totoró'
							),
							'00009' => array( // Cesar
								'GONZALEZ' => 'González',
								'CURUMANI' => 'Curumaní',
								'CODAZZI' => 'Agustín Codazzi',
								'AGUSTIN CODAZZI' => 'Agustín Codazzi',
								'DEPARTAMENTO' => '',
								'CHIRIGUANA' => 'Chiriguaná',
								'SAN MARTIN' => 'San Martín',
								'RIO DE ORO' => 'Río de Oro'
							), 
							'00012' => array( // Choco
								'BAJO SAN JUAN' => 'El Litoral de San Juan',
								'LLORO' => 'Lloró',
								'NUQUI' => 'Nuquí',
								'QUIBDO' => 'Quibdó',
								'ITSMINA' => 'Istmina',
								'LITORAL DEL SAN JUAN' => 'El Litoral de San Juan',
								'SIPI' => 'Sipí',
								'RIO IRO' => 'Río Iró',
								'CARMEN DEL DARIEN' => 'Carmen del Darién',
								'ALTO BAUDO' => 'Alto Baudó',
								'BOJAYA' => 'Bojayá',
								'NOVITA' => 'Nóvita',
								'JURADO' => 'Juradó',
								'TADO'   => 'Tadó',
								'PIZARRO' => 'Bajo Baudo',
								'UNGUIA'  => 'Unguía',
								'ACANDI' => 'Acandí',
								'BAHIA SOLANO' => 'Bahía Solano',
								'BAGADO' => 'Bagadó',
								'CARMEN DE ATRATO' => 'El Carmen de Atrato',
								'CANTON DE SAN PABLO' => 'El Cantón del San Pablo',
								'SAN JOSE DEL PALMAR' => 'San José del Palmar'
							),
							'00010' => array( // Córdoba
								'DEPARTAMENTO' => '',
								'CHIMA' => 'Chimá',
								'CIENAGA DE ORO' => 'Ciénaga de Oro',
								'CERETE' => 'Cereté',
								'CHINU' => 'Chinú',
								'LOS CORDOBAS' => 'Los Córdobas',
								'MONTERIA' => 'Montería',
								'MONTELIBANO' => 'Montelíbano',
								'MOÑITOS' => 'Moñitos',
								'PURISIMA' => 'Purísima',
								'SAN ANDRES DE SOTAVENTO' => 'San Andrés de Sotavento',
								'SAN JOSE DE URE' => 'San José de Uré',
								'SAN MATEO' => 'Chinú',
								'TUCHIN' => 'Tuchín'
							),
							'00011' => array( // Cundinamarca
								'ALBAN' => 'Albán',
								'FUQUENE' => 'Fúquene',
								'GACHALA' => 'Gachalá',
								'GUACHETA' => 'Guachetá',
								'GUATAQUI' => 'Guataquí',
								'LA PEÑA' => 'La Peña',
								'MESITAS DEL COLEGIO' => 'El Colegio',
								'PULI' => 'Pulí',
								'SAN JUAN DE RIOSECO' => 'San Juan de Río Seco',
								'TIBACUI' => 'Tibacuy',
								'VIOTA' => 'Viotá',
								'YACOPI' => 'Yacopí',
								'ZIPACON' => 'Zipacón',
								'ZIPAQUIRA' => 'Zipaquirá',
								'GUTIERREZ' => 'Gutiérrez',
								'CAJICA' => 'Cajicá',
								'CHOACHI' => 'Choachí',
								'GACHETA' => 'Guachetá',
								'SAN FRANCISO' => 'San Francisco',
								'SOPO' => 'Sopó',
								'UBATE' => 'Villa de San Diego de Ubate',
								'QUIPILE' => 'Quiple',
								'FUSAGASUGA' => 'Fusagasugá',
								'SIBATE' => 'Sibaté',
								'CHIA' => 'Chía',
								'CAPARRAPI' => 'Caparrapí',
								'TOCANCIPA' => 'Tocancipá',
								'CHAGUANI' => 'Chaguaní'
							),
							'00014' => array( // La Guajira
								'DISTRACCION' => 'Distracción'
							),
							'00030' => array( //Guainía
								'PUERTO INIRIDA' => 'Inírida',
							),
							'00031' => array( //Guaviare
								'SAN JOSE DEL GUAVIARE' => 'San José del Guaviare'
							),
							'00013' => array( // Huila
								'CAMPO ALEGRE' => 'Campoalegre',
								'NATAGA' => 'Nátaga',
								'SAN AGUSTIN' => 'San Agustín',
								'SANTA MARIA' => 'Santa María',
								'TIMANA' => 'Timaná',
								'YAGUARA' => 'Yaguará'
							),
							'00015' => array( // Magdalena
								'CIENAGA' => 'Ciénaga',
								'DEPARTAMENTO' => '',
								'ARIGUANI' => 'Ariguaní',
								'SITIO NUEVO' => 'Sitionuevo',
								'ZAPAYAN' => 'Zapayán',
								'EL PIÑON' => 'El Piñon',
								'SANTA BARBARA DE PINTO' => 'Santa Bárbara de Pinto',
								'PIJIÑO DEL CARMEN' => 'Pijiño del Carmen',
								'SAN SEBASTIAN' => 'San Sebastián de Buenavista',
								'CERRO DE SAN ANTONIO' => 'Cerro San Antonio',
								'EL RETEN' => 'El Retén',
								'FUNDACION' => 'Fundación',
								'SAN ZENON' => 'San Zenón'
							),
							'00016' => array( // Meta
								'CALVARIO' => 'El Calvario',
								'LEJANIAS' => 'Lejanías',
								'MAPIRIPAN' => 'Mapiripán',
								'VISTA HERMOSA' => 'Vistahermosa',
								'PUERTO LOPEZ' => 'Puerto López',
								'SAN MARTIN' => 'San Martín',
								'BARRANCA DE UPIA' => 'Barrana de Upía',
								'CUMARAL' => 'Cumarral',
								'ACACIAS' => 'Acacías'
							),
							'00017' => array( // Nariño
								'ALBAN' => 'Albán',
								'BELEN' => 'Belén',
								'CHACHAGUI' => 'Chachagüí',
								'EL TABLON' => 'El Tablón de Gómez',
								'LA UNION' => 'La Unión',
								'LEYVA' => 'Leiva',
								'POTOSI' => 'Potosí',
								'ROBERTO PAYAN' => 'Roberto Payán',
								'ANDES' => 'Los Andes',
								'ANCUYA' => 'Ancuyá',
								'GUALMATAN' => 'Gualmatán',
								'SAN JOSE DE ALBAN' => 'Albán',
								'TUMACO' => 'San Andrés de Tumaco',
								'MAGUI PAYAN' => 'Magüi',
								'SANDONA' => 'Sandoná',
							),
							'00018' => array( // Norte de Santander
								'CACHIRA' => 'Cachirá',
								'CONVENCION' => 'Convención',
								'CHITAGA' => 'Chitagá',
								'CUCUTA' => 'Cúcuta',
								'TIBU' => 'Tibú',
								'OCAÑA' => 'Ocaña',
								'HERRAN' => 'Herrán',
								'CHINACOTA' => 'Chinácota',
								'HACARI' => 'Hacarí',
								'LA PLAYA DE BELEN' => 'La Playa',
								'ZULIA' => 'El Zulia'
							),
							'00027' => array( //Putumayo
								'VILLA GARZON' => 'Villagarzón'
							),
							'00019' => array( // Quindio
								'CALARCA' => 'Calarcá',
								'GENOVA' => 'Génova',
								'CIRCACIA' => 'Circasia',
								'CORDOBA' => 'Córdoba'
							),
							'00020' => array( // Risaralda
								'GUATICA' => 'Guática',
								'BELEN DE UMBRIA' => 'Belén de Umbría',
								'QUINCHIA' => 'Quinchía',
								'APIA' => 'Apía',
								'MISTRATO' => 'Mistrató',
								'DEPARTAMENTO' => ''
							),
							'00028' => array( // San Andrés
								'SAN ANDRES' => 'San Andrés'
							),
							'00021' => array( // Santander
								'CHARALA' => 'Charalá',
								'CONCEPCION' => 'Concepción',
								'EL CERRITO' => 'Cerrito',
								'EL PLAYON' => 'El Playón',
								'GUACAMAYO' => 'El Guacamayo',
								'GUAVATA' => 'Guavatá',
								'JESUS MARIA' => 'Jesús María',
								'MALAGA' => 'Málaga',
								'SAN JOAQUIN' => 'San Joaquín',
								'BOLIVAR' => 'Bolívar',
								'VELEZ' => 'Vélez',
								'SURATA' => 'Suratá',
								'SAN VICENTE' => 'San Vicente de Chucurí',
								'SAN VICENTE DE CHUCURI' => 'San Vicente de Chucurí',
								'CONTRATACION' => 'Contratación',
								'SAN JOSE MIRANDA' => 'San José de Miranda',
								'CARCASI' => 'Carcasí',
								'CURITI' => 'Curití',
								'GIRON' => 'Girón',
								'DEPARTAMENTO' => '',
								'CEPITA' => 'Cepitá',
								'EL HATO' => 'Hato',
								'EL PALMAR' => 'Palmar',
								'GALAN' => 'Galán',
								'GUAPOTA' => 'Guapotá',
								'SANTA HELENA DEL OPON' => 'Santa Helena del Opón',
								'VALLE DE SAN JOSE' => 'Valle de San José',
								'EL CARMEN DE CHUCURI' => 'El Carmen de Chucurí',
								'GUABATA' => 'Guavatá'
							),
							'00022' => array( // Sucre
								'CHALAN' => 'Chalán',
								'SUCRE/MAJAGUAL' => 'Majagual',
								'SAN ONOFRE' => 'San Onofré',
								'SAN PEDRO' => 'San Pedró',
								'SAN BENITO ABAD' => 'San Benito de Abad',
								'DEPARTAMENTO' => '',
								'COVEÑAS' => 'Coveñas',
								'SAMPUES' => 'Sampués',
								'SINCE' => 'Sincelejo',
								'TOLU' => 'Tolú Viejo',
								'TOLUVIEJO' => 'Tolú Viejo',
								'LA UNION' => 'La Unión',
							),
							'00023' => array( // Tolima
								'ANSOATEGUI' => 'Anzoátegui',
								'ARMERO GUAYABAL' => 'Armero',
								'LIBANO' => 'Líbano',
								'IBAGUE' => 'Ibagué',
								'HERBEO' => 'Herveo',
								'VANDAILLO' => 'Venadillo',
								'VILLA HERMOSA' => 'Villahermosa',
								'VILLARICA' => 'Villarrica',
								'CARMEN DE APICALA' => 'Carmen de Apicalá',
								'PURIFICACION' => 'Purificación',
								'SUAREZ' => 'Suárez',
								'MARIQUITA' => 'San Sebastián de Mariquita'
							),
							'00024' => array( // Valle del Cauca
								'EL AGUILA' => 'El Águila',
								'JAMUNDI' => 'Jamundí',
								'CALIMA DARIEN' => 'Calima',
								'ANDALUCIA' => 'Andalucía',
								'GUACARI' => 'Guacarí',
								'BOLIVAR' => 'Bolívar',
								'LA UNION' => 'La Unión',
								'RIOFRIO' => 'Riofrío',
								'TULUA' => 'Tuluá',
								'ALCALA' => 'Alcalá',
								'DEPARTAMENTO' => ''								
							)
						);
			$BlockFixes = array(
			);

			// 2-3 - GeographyName (Departamento/Municipio)
			$Dpto = $a[3];
			$Mpio = $a[5];
			$Block = $a[7];
			if (array_key_exists($Dpto, $DptoFixes))
			{
				$Dpto = $DptoFixes[$Dpto];
			}
			$DptoCode = DIGeography::getIdByName($us, $Dpto, '');
			$GeographyId = $DptoCode;
			/*
			if ( array_key_exists($DptoCode, $MpioFixes) && 
			     array_key_exists($Mpio, $MpioFixes[$DptoCode]) )
			{
				$Mpio = $MpioFixes[$DptoCode][$Mpio];
			}
			if ($GeographyId != '')
			{
				if ($Mpio != '')
				{
					$MpioCode = DIGeography::getIdByName($us, $Mpio, $GeographyId);
					$GeographyId = $MpioCode;
				}
			}

			if ( array_key_exists($MpioCode, $BlockFixes) && 
			     array_key_exists($Block, $BlockFixes[$MpioCode]) )
			{
				$Block = $BlockFixes[$MpioCode][$Block];
			}
			if ($GeographyId != '')
			{
				if ($Block != '')
				{
					$GeographyId = DIGeography::getIdByName($us, $Block, $GeographyId);
				}
			}
			*/

			$d->set('GeographyId', $GeographyId);
			if ($GeographyId == '')
			{
				printf('GEOGRAPHY ERROR : %-10s %-5s %-20s %-20s %-20s' . "\n", $DisasterSerial, $DptoCode, $Dpto, $Mpio, $Block);
			}

			// Cause - 10
			$CauseName = $a[10];
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
			
			// Event - 1
			$EventName = $a[1];
			$EventFix = array(
							'TORMENTA ELECTRICA' => 'Tormenta eléctrica',
							'AVALANCHA' => 'Alud',
							'EROSION' => 'Erosión',
							'SEQUIA' => 'DROUGHT',
							'INCENDIO ESTRUCTURAL' => 'FIRE'
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

			/*
			// 6-9 - Effects (Basic) 
			$d->set('EffectPeopleDead'      , valueToDIField($a[6]));
			$d->set('EffectPeopleInjured'   , valueToDIField($a[7]));
			$d->set('EffectPeopleMissing'   , valueToDIField($a[8]));
			$d->set('EffectPeopleAffected'  , valueToDIField($a[9]));
			// 10 Familias (EEF)
			$d->set('EffectHousesDestroyed' , valueToDIField($a[11]));
			$d->set('EffectHousesAffected'  , valueToDIField($a[12]));
			$d->set('EffectRoads'           , valueToDIField($a[13]));
			// 14 Puentes Vehic
			// 15 Puentes Peatonales
			$d->set('SectorWaterSupply'     , valueToDIField($a[16]));
			$d->set('SectorSewerage'        , valueToDIField($a[17]));
			$d->set('EffectMedicalCenters'  , valueToDIField($a[18]));
			$d->set('EffectEducationCenters', valueToDIField($a[19]));
			// 20 Centros Comunitarios
			$d->set('EffectFarmingAndForest', valueToDIField($a[21]));
			$d->set('EffectOtherLosses'     , $a[22]);
			// 23 - 37 ????
			$d->set('EffectNotes'           , $a[38]);


			$d->set('EEF004', valueToDIField($a[10]));  // 10 - Familias Afectadas
			$d->set('EEF005', valueToDIField($a[14]));  // 14 - Puentes Vehiculares
			$d->set('EEF006', valueToDIField($a[15]));  // 15 - Puentes Peatonales
			$d->set('EEF007', valueToDIField($a[16]));  // 16 - Acueductos Afectados
			$d->set('EEF008', valueToDIField($a[20]));  // 20 - Centros Comunitarios
			$d->set('EEF009',   dateToISO8601($a[23]));  // 23 - Fecha Tramite Administrativo
			$d->set('EEF010', valueToDIField($a[24]));  // 24 Menajes
			$d->set('EEF011', valueToDIField($a[25]));  // 25 Ap. Aliment.
			$d->set('EEF012', valueToDIField($a[26]));  // 26 Materiales Construccion
			$d->set('EEF013', valueToDIField($a[27]));  // Sacos
			$d->set('EEF014', valueToDIField($a[28]));  // Otros
			$d->set('EEF015', valueToDIField($a[29]));  // Giro Directo
			$d->set('EEF018', valueToDIField($a[31]));  // Apoyos en Tramite
			$d->set('EEF019',   dateToISO8601($a[32]));  // Fecha Recibo
			$d->set('EEF021', valueToDIField($a[35]));  // Atendido
			$d->set('EEF022', valueToDIField($a[37]));  // Analisis y Evaluacion de la Solicitud
			$d->set('EEF033', valueToDIField($a[39]));  // Cobija (Cant)
			$d->set('EEF034', valueToDIField($a[40]));  // Cobija (Valor)
			$d->set('EEF035', valueToDIField($a[41]));  // Cobija Térmica (Cant)
			$d->set('EEF036', valueToDIField($a[42]));  // Cobija Térmica (Valor)
			$d->set('EEF037', valueToDIField($a[43]));  // Colchoneta (Cant)
			$d->set('EEF038', valueToDIField($a[44]));  // Colchoneta (Valor)
			$d->set('EEF039', valueToDIField($a[45]));  // Catre (Cant)
			$d->set('EEF040', valueToDIField($a[46]));  // Catre (Valor)
			$d->set('EEF049', valueToDIField($a[47]));  // Hamacas (Cant)
			$d->set('EEF050', valueToDIField($a[48]));  // Hamacas (Valor)
			$d->set('EEF061', valueToDIField($a[49]));  // Peinilla (Cant)
			$d->set('EEF062', valueToDIField($a[50]));  // Peinilla (Valor)
			$d->set('EEF063', valueToDIField($a[51]));  // Plastico Negro (Cant)
			$d->set('EEF064', valueToDIField($a[52]));  // Plastico Negro (Valor)
			$d->set('EEF065', valueToDIField($a[53]));  // Plato Hondo (Cant)
			$d->set('EEF066', valueToDIField($a[54]));  // Plato Hondo (Valor)
			$d->set('EEF067', valueToDIField($a[55]));  // Plato Pando (Cant)
			$d->set('EEF068', valueToDIField($a[56]));  // Plato Pando (Valor)
			$d->set('EEF069', valueToDIField($a[57]));  // Pocillo (Cant)
			$d->set('EEF070', valueToDIField($a[58]));  // Pocillo (Valor)
			$d->set('EEF071', valueToDIField($a[59]));  // Sábanas (Cant)
			$d->set('EEF072', valueToDIField($a[60]));  // Sábanas (Valor)
			$d->set('EEF073', valueToDIField($a[61]));  // Sobrecamas (Cant)
			$d->set('EEF074', valueToDIField($a[62]));  // Sobrecamas (Valor)
			$d->set('EEF075', valueToDIField($a[63]));  // Toallas (Cant)
			$d->set('EEF076', valueToDIField($a[64]));  // Toallas (Valor)
			$d->set('EEF077', valueToDIField($a[65]));  // Toldillos (Cant)
			$d->set('EEF078', valueToDIField($a[66]));  // Toldillos (Valor)
			$d->set('EEF079', valueToDIField($a[67]));  // Kit Aseo (Cant)
			$d->set('EEF080', valueToDIField($a[68]));  // Kit Aseo (Valor)
			$d->set('EEF081', valueToDIField($a[69]));  // Kit Cocina (Cant)
			$d->set('EEF082', valueToDIField($a[70]));  // Kit Cocina (Valor)
			$d->set('EEF085', valueToDIField($a[71]));  // Menajes (Valor Total)
			$d->set('EEF086', valueToDIField($a[72])); // Sacos (Cant)
			$d->set('EEF087', valueToDIField($a[73])); // Sacos (Valor)
			$d->set('EEF088', valueToDIField($a[74])); // Mercados (Cant)
			$d->set('EEF089', valueToDIField($a[75])); // Mercados (Valor)
			$d->set('EEF090', valueToDIField($a[76])); // Cemento (Cant)
			$d->set('EEF091', valueToDIField($a[77])); // Cemento (Valor)
			$d->set('EEF092', valueToDIField($a[78])); // Tejas (Cant)
			$d->set('EEF093', valueToDIField($a[79])); // Tejas (Valor)
			*/
			
			/*
			$d->set('EEF025', valueToDIField($a[39]));  // Cepillo Adulto (Cant)
			$d->set('EEF026', valueToDIField($a[40]));  // Cepillo Adulto (Valor)
			$d->set('EEF027', valueToDIField($a[41]));  // Cepillo Niño (Cant)
			$d->set('EEF028', valueToDIField($a[42]));  // Cepillo Niño (Valor)
			$d->set('EEF029', valueToDIField($a[43]));  // Chocolatera (Cant)
			$d->set('EEF030', valueToDIField($a[44]));  // Chocolatera (Valor)
			$d->set('EEF031', valueToDIField($a[45]));  // Cinta Empalmar (Cant)
			$d->set('EEF032', valueToDIField($a[46]));  // Cinta Empalmar (Valor)
			$d->set('EEF041', valueToDIField($a[55]));  // Crema Desod. (Cant)
			$d->set('EEF042', valueToDIField($a[56]));  // Crema Desoc. (Valor)
			$d->set('EEF043', valueToDIField($a[57]));  // Cuchara Acero (Cant)
			$d->set('EEF044', valueToDIField($a[58]));  // Cuchara Acero (Valor)
			$d->set('EEF045', valueToDIField($a[59]));  // Cuchara Madera (Cant)
			$d->set('EEF046', valueToDIField($a[60]));  // Cuchara Madera (Valor)
			$d->set('EEF047', valueToDIField($a[61]));  // Estufas (Cant)
			$d->set('EEF048', valueToDIField($a[62]));  // Estufas (Valor)
			$d->set('EEF051', valueToDIField($a[65]));  // Jabon Baño (Cant)
			$d->set('EEF052', valueToDIField($a[66]));  // Jabon Baño (Valor)
			$d->set('EEF053', valueToDIField($a[67]));  // Jabon Barra (Cant)
			$d->set('EEF054', valueToDIField($a[68]));  // Jabon Barra (Valor)
			$d->set('EEF055', valueToDIField($a[69]));  // Juego Cubiertos (Cant)
			$d->set('EEF056', valueToDIField($a[70]));  // Juego Cubiertos (Valor)
			$d->set('EEF057', valueToDIField($a[71]));  // Ollas (Cant)
			$d->set('EEF058', valueToDIField($a[72]));  // Ollas (Valor)
			$d->set('EEF059', valueToDIField($a[73]));  // Papel Higiénico (Cant)
			$d->set('EEF060', valueToDIField($a[74]));  // Papel Higiénico (Valor)
			$d->set('EEF083', valueToDIField($a[97]));  // Kit Alcoba (Cant)
			$d->set('EEF084', valueToDIField($a[98]));  // Kit Alcoba (Valor)
			*/
		
			$i = 1;

			$DisasterId = $d->get('DisasterId');
			if ($DisasterId=='')
			{
				$d->set('DisasterId', uuid());
				$DisasterId = $d->get('DisasterId');
			}

			// Validate Effects and Save as DRAFT if needed
			$i = $d->validateEffects(-61,0);
			if ($i < 0) 
			{
				$d->set('RecordStatus', 'DRAFT');
			}
			$Cmd = '';
			$bExist = $d->exist();
			if ($bExist < 0) {
				//$i = $d->insert(1,0);
				$Cmd = 'INSERT';
			} else {
				//$i = $d->update(1,0);
				$Cmd = 'UPDATE';
			}
			if ($i < 0)
			{
				print $line . ' ' . $DisasterSerial . ' ' . $i . "\n";
			}			
			if (($line > 0) && (($line % 100) == 0) )
			{
				print $line . "\n";
			}
		} //if
	} //if
	$line++;
} //while

$us->close();
$us->logout();
exit();

function valueToDIField($prmValue)
{
	$Value = 0;
	$prmValue = preg_replace('/\$/', '', $prmValue);
	$prmValue = preg_replace('/\./', '', $prmValue);
	$prmValue = preg_replace('/,/', '.', $prmValue);
	if (is_numeric($prmValue))
	{
		$Value = $prmValue;
	}
	else
	{
		if ($prmValue == 'hubo')
		{
			$Value = -1;
		}
		if ($prmValue == 'no hubo')
		{
			$Value = 0;
		}
	}
	return $Value;
}

function dateToISO8601($prmDate)
{
	$v = '';
	if (strlen($prmDate) > 0)
	{
		$a = preg_split('#/#', $prmDate);
		$year  = $a[0];
		$month = $a[1];
		$day   = $a[2];
		$v = sprintf('%4d-%2d-%2d', $year, $month, $day);
		$v = str_replace(' ', '0', $v);
	}
	return $v;
}

function getMonth($prmMonthName)
{
	$m = array('ene' =>  1, 'feb' =>  2, 'mar' =>  3, 'apr' =>  4, 'may' =>  5, 'jun' =>  6, 
	           'jul' =>  7, 'aug' =>  8, 'sep' =>  9, 'oct' => 10, 'nov' => 11, 'dec' => 12);
	$v = 0;
	$MonthName = strtolower($prmMonthName);
	if (array_key_exists($MonthName, $m)) {
		$v = $m[$MonthName];
	}
	return $v;
}

function createEEField($prmSession, $EEFieldLabel, $EEFieldType, $EEFieldSize='')
{
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

function createEEFields($us)
{
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
