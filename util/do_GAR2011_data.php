#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-08-06 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Process GAR2011 databases, apply rules to discard records.

*/

require_once('../web/include/loader.php');

$RegionList = array();
foreach($us->q->core->query("SELECT * FROM Region WHERE RegionId LIKE 'GAR-ISDR-2011_%'") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
$RegionList = array('GAR-ISDR-2011_MOZ');
foreach ($RegionList as $RegionId) {
	$us->open($RegionId);
	print $RegionId . "\n";
	
	$sQuery = 'SELECT COUNT(*) AS C FROM Disaster';
	foreach($us->q->dreg->query($sQuery) as $row) {
		$iCount = $row['C'];
	}
	print 'FICHAS INICIALES : ' . $iCount . "\n";

	$iCount = 0;
	// Remove datacard outside of period 1970-2009
	$answer = removeData($us, 'Fichas Fuera del Periodo' , "DisasterBeginTime < '1970-00-00' OR DisasterBeginTime >= '2010-00-00'");
	//printList($answer, 'Fichas Fuera del Periodo');
	
	// Remove events outside of list
	print 'EVENTOS PREDEFINIDOS' . "\n";
	$iCount = 0;
	$iCount += removeData($us, 'ACCIDENTE'     , 'EventId="ACCIDENT"');
	$iCount += removeData($us, 'BIOLOGICO'     , 'EventId="BIOLOGICAL"');
	$iCount += removeData($us, 'LINEA DE COSTA', 'EventId="COASTLINE"');
	$iCount += removeData($us, 'ESTRUCTURA'    , 'EventId="STRUCTURE"');
	$iCount += removeData($us, 'CONTAMINACION' , 'EventId="POLLUTION"');
	$iCount += removeData($us, 'EPIDEMIA'      , 'EventId="EPIDEMIC"');
	$iCount += removeData($us, 'ESCAPE'        , 'EventId="LEAK"');
	$iCount += removeData($us, 'EXPLOSION'     , 'EventId="EXPLOSION"');
	$iCount += removeData($us, 'LICUACION'     , 'EventId="LIQUEFACTION"');
	$iCount += removeData($us, 'OTRO'          , 'EventId="OTHER"');
	$iCount += removeData($us, 'PLAGA'         , 'EventId="PLAGUE"');
	$iCount += removeData($us, 'PANICO'        , 'EventId="PANIC"');
	$iCount += removeData($us, 'SEDIMENTACION' , 'EventId="SEDIMENTATION"');
	
	print 'FICHAS BORRADAS POR EVENTOS PREDEFINIDOS  : ' . $iCount . "\n";

	$iCount = 0;
	print 'EVENTOS LOCALES' . "\n";

	// Local Events ARG
	if ($RegionId == 'GAR-ISDR-2011_ARG') {
		$iCount += removeData($us, 'Epizootia'    , 'EventId="EPIZOOTIA"');
		$iCount += removeData($us, 'Intoxicación' , 'EventId="INTOXICACION"');
		$iCount += removeData($us, 'Litoral'      , 'EventId="LITORAL"');
	}
	
	// Local Events CHL
	if ($RegionId == 'GAR-ISDR-2011_CHL') {
		$iCount += removeData($us, 'Falla'       , 'EventId="FALLA"');
		$iCount += removeData($us, 'Intoxicación', 'EventId="INTOXICACIÓN"');
		$iCount += removeData($us, 'Litoral'     , 'EventId="LITORAL"');
		$iCount += removeData($us, 'Ozono'       , 'EventId="OZONO"');
	}

	// Local Events COL
	if ($RegionId == 'GAR-ISDR-2011_COL') {
		$iCount += removeData($us, 'Erosión'      , 'EventId="fb1184be-1912-46c3-b4de-22011701e26c"'); 
		$iCount += removeData($us, 'Intoxicación' , 'EventId="INTOXICACION"');
		$iCount += removeData($us, 'Naufragio'    , 'EventId="f8280625-4320-4033-81cb-8feb19bd2f2e"');
		$iCount += removeData($us, 'Represamiento', 'EventId="8d1f7788-2766-47f2-8472-deea68515086"');
	}

	// Local Events CHL
	if ($RegionId == 'GAR-ISDR-2011_ECU') {
		$iCount += removeData($us, 'Estructura', "EventId='208fbb9e-b687-48e6-8713-f7d3f4fc6804'");
		$iCount += removeData($us, 'Falla'     , "EventId='b882c6a8-f4ba-4282-8ebc-852209e74db3'");
	}
	
	// Local Events MEX
	if ($RegionId == 'GAR-ISDR-2011_MEX') {
		$iCount += removeData($us, 'Hambruna'     , 'EventId="f8ec243e-f589-4c57-8b93-beeef2043aca"'); // Hambruna
		$iCount += removeData($us, 'Intoxicación' , 'EventId="2be72ac5-c4a8-4d42-bea2-acaa8666e3f4"'); // Intoxicación
		$iCount += removeData($us, 'Racionamiento', 'EventId="7ce5f128-934c-40a5-b131-dd32c7ac8a34"'); // Racionamiento
		
		//$iCount += removeData($us, 'Hundimiento'      , 'EventId="3cc22906-21be-4ba9-baed-591c06ad2bea"');
		//$iCount += removeData($us, 'Niebla'           , 'EventId="dfeee180-23fa-4a69-9fc2-b95e777426b3"');
		//$iCount += removeData($us, 'Tormenta Tropical', 'EventId="dd1aeeed-735e-4a81-96a5-e9d7bf404f19"');
	}
	
	if ($RegionId == 'GAR-ISDR-2011_PAN') {
		$iCount += removeData($us, 'Ahogamiento' , 'EventId="AHOGAMIENTO"');
		$iCount += removeData($us, 'Falla'       , 'EventId="FALLA"');
		$iCount += removeData($us, 'Naufragio'   , 'EventId="NAUFRAGIO"');		
	}

	// Local Events PER
	if ($RegionId == 'GAR-ISDR-2011_PER') {
		$iCount += removeData($us, 'Epizotia'    , 'EventId="EPIZOOTIA"');
		$iCount += removeData($us, 'Erosión'     , 'EventId="EROSION"');
		$iCount += removeData($us, 'Intoxicación', 'EventId="INTOXICACION"');
		//$iCount += removeData($us, 'EventId="OLA DE FRIO"');
	}

	//Local Events VEN
	if ($RegionId == 'GAR-ISDR-2011_VEN') {
		$iCount += removeData($us, 'Accidente Tránsito', 'EventId="A. TRANSITO"');
		$iCount += removeData($us, 'Arbol Caído'       , 'EventId="ARBOL CAIDO"');
	}
	
	//Local Events SLV
	if ($RegionId == 'GAR-ISDR-2011_SLV') {
		//$iCount += removeData($us, 'Pajizo', 'EventId="1cb0d2bf-a5c8-4a4d-adbd-43112a8244ea"'); // PAJIZO
		$iCount += removeData($us, 'Deforestación' , 'EventId="6c3d7356-36cd-425b-a68d-cd9b7d91f0d9"'); // Deforestación
	}
	
	if ($RegionId == 'GAR-ISDR-2011_IND_TAMILNADU') {
		$iCount += removeData($us, 'Sea Erosion', 'EventId="a313f9c8-9057-4f1d-8366-b6fcc1729f5b"');
	}
	
	print 'FICHAS BORRADAS POR EVENTOS LOCALES : ' . $iCount . "\n";

	// Change RecordStatus=DRAFT when only GeoLevel0 is selected
	$iCount = 0;
	$sQuery = 'SELECT COUNT(*) AS C FROM Disaster WHERE LENGTH(GeographyId)==5';
	foreach($us->q->dreg->query($sQuery) as $row) {
		$iCount = $row['C'];
	}
	print 'FICHAS CONVERTIDAS A BORRADOR POR GEOGRAFIA NIVEL 0 : ' . $iCount . "\n";
		
	$sQuery = 'UPDATE Disaster SET RecordStatus="DRAFT" WHERE LENGTH(GeographyId)==5';
	$us->q->dreg->query($sQuery);

	$sQuery = 'SELECT COUNT(*) AS C FROM Disaster';
	foreach($us->q->dreg->query($sQuery) as $row) {
		$iCount = $row['C'];
	}
	print 'FICHAS FINALES : ' . $iCount . "\n";
} //foreach
exit();

function removeData($us, $Label, $params) {
	$answer = array();
	$sQuery = 'SELECT DisasterId,DisasterSerial FROM Disaster WHERE ' . $params;
	$iCount = 0;
	$dlist = '';
	$answer['Serial'] = array();
	foreach($us->q->dreg->query($sQuery) as $row) {
		if ($iCount > 0) {
			$dlist .= ',';
		}
		$dlist .= '"' . $row['DisasterId'] . '"';
		array_push($answer['Serial'], $row['DisasterSerial']);
		$iCount++;
	}
	$us->q->dreg->query('DELETE FROM Disaster WHERE DisasterId IN (' . $dlist . ');');
	$us->q->dreg->query('DELETE FROM EEData   WHERE DisasterId IN (' . $dlist . ');');
	$answer['Count'] = $iCount;
	printList($answer, $Label);
	return $answer['Count'];
}

function printList($List, $Label) {
	print '     ' . $Label . ' : ' . $List['Count'] . "\n";
	$iCol = 0;
	foreach($List['Serial'] as $Item) {
		if (($iCol % 5) == 0) {
			print '          ';
		}	
		printf('%-12s', $Item);
		$iCol++;
		if (($iCol % 5) == 0) {
			print "\n";
		}
	}
	if (($iCol % 5) != 0) {
		print "\n";
	}
}

</script>
