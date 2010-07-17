#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-07-04 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Process GAR2011 databases, apply rules to discard records.

*/

require_once('../web/include/loader.php');

$RegionList = array();
foreach($us->q->core->query("SELECT * FROM Region WHERE RegionId LIKE 'GAR-ISDR-2011_%'") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
$RegionList = array('GAR-ISDR-2011_SLV');
foreach ($RegionList as $RegionId) {
	$us->open($RegionId);
	print $RegionId . "\n";
	
	$sQuery = 'SELECT COUNT(*) AS C FROM Disaster';
	foreach($us->q->dreg->query($sQuery) as $row) {
		$iCount = $row['C'];
	}
	print 'Registros Inicio        : ' . $iCount . "\n";
	
	$iCount = 0;
	// Remove datacard outside of period 1970-2009
	$iCount += removeData($us, 'DisasterBeginTime < 1970');
	$iCount += removeData($us, 'DisasterBeginTime >= 2010');
	
	print 'Fichas Fuera del Periodo: ' . $iCount . "\n";
	
	// Remove events outside of list
	$iCount = 0;
	$iCount += removeData($us, 'EventId="ACCIDENT"');
	$iCount += removeData($us, 'EventId="BIOLOGICAL"');
	$iCount += removeData($us, 'EventId="COASTLINE"');
	$iCount += removeData($us, 'EventId="STRUCTURE"');
	$iCount += removeData($us, 'EventId="POLLUTION"');
	$iCount += removeData($us, 'EventId="EPIDEMIC"');
	$iCount += removeData($us, 'EventId="LEAK"');
	$iCount += removeData($us, 'EventId="EXPLOSION"');
	$iCount += removeData($us, 'EventId="LIQUEFACTION"');
	$iCount += removeData($us, 'EventId="OTHER"');
	$iCount += removeData($us, 'EventId="PLAGUE"');
	$iCount += removeData($us, 'EventId="PANIC"');
	$iCount += removeData($us, 'EventId="SEDIMENTATION"');
	
	print 'Eventos Predefinidos    : ' . $iCount . "\n";
	
	$iCount = 0;

	// Local Events
	$iCount += removeData($us, 'EventId="ESTRUCTURA"');
	
	// Local Events CHL
	$iCount += removeData($us, 'EventId="FALLA"');
	$iCount += removeData($us, 'EventId="INTOXICACIÓN"');
	$iCount += removeData($us, 'EventId="LITORAL"');
	$iCount += removeData($us, 'EventId="OZONO"');

	// Local Events COL
	$iCount += removeData($us, 'EventId="fb1184be-1912-46c3-b4de-22011701e26c"');
	$iCount += removeData($us, 'EventId="INTOXICACION"');
	$iCount += removeData($us, 'EventId="f8280625-4320-4033-81cb-8feb19bd2f2e"');
	$iCount += removeData($us, 'EventId="8d1f7788-2766-47f2-8472-deea68515086"');
	
	// Local Events MEX
	$iCount += removeData($us, 'EventId="f8ec243e-f589-4c57-8b93-beeef2043aca"');
	$iCount += removeData($us, 'EventId="3cc22906-21be-4ba9-baed-591c06ad2bea"');
	$iCount += removeData($us, 'EventId="2be72ac5-c4a8-4d42-bea2-acaa8666e3f4"');
	$iCount += removeData($us, 'EventId="dfeee180-23fa-4a69-9fc2-b95e777426b3"');
	$iCount += removeData($us, 'EventId="7ce5f128-934c-40a5-b131-dd32c7ac8a34"');
	$iCount += removeData($us, 'EventId="dd1aeeed-735e-4a81-96a5-e9d7bf404f19"');

	// Local Events PER
	$iCount += removeData($us, 'EventId="EPIZOOTIA"');
	$iCount += removeData($us, 'EventId="EROSION"');
	$iCount += removeData($us, 'EventId="INTOXICACION"');
	$iCount += removeData($us, 'EventId="OLA DE FRIO"');

	//Local Events VEN
	$iCount += removeData($us, 'EventId="A. TRANSITO"');
	$iCount += removeData($us, 'EventId="ARBOL CAIDO"');
	
	//Local Events SLV
	$iCount += removeData($us, 'EventId="1cb0d2bf-a5c8-4a4d-adbd-43112a8244ea"'); // PAJIZO
	$iCount += removeData($us, 'EventId="6c3d7356-36cd-425b-a68d-cd9b7d91f0d9"'); // Deforestación
	
	print 'Eventos Locales         : ' . $iCount . "\n";

	// Change RecordStatus=DRAFT when only GeoLevel0 is selected
	$iCount = 0;
	$sQuery = 'SELECT COUNT(*) AS C FROM Disaster WHERE LENGTH(GeographyId)==5';
	foreach($us->q->dreg->query($sQuery) as $row) {
		$iCount = $row['C'];
	}
	print 'Borrador (GeoLevel=0)   : ' . $iCount . "\n";
		
	$sQuery = 'UPDATE Disaster SET RecordStatus="DRAFT" WHERE LENGTH(GeographyId)==5';
	$us->q->dreg->query($sQuery);

	$sQuery = 'SELECT COUNT(*) AS C FROM Disaster';
	foreach($us->q->dreg->query($sQuery) as $row) {
		$iCount = $row['C'];
	}
	print 'Registros Finales       : ' . $iCount . "\n";
		
} //foreach
exit();

function removeData($us, $params) {
	$sQuery = 'SELECT DisasterId FROM Disaster WHERE ' . $params;
	$iCount = 0;
	$dlist = '';
	foreach($us->q->dreg->query($sQuery) as $row) {
		if ($iCount > 0) {
			$dlist .= ',';
		}
		$dlist .= '"' . $row['DisasterId'] . '"';
		$iCount++;
	}
	$us->q->dreg->query('DELETE FROM Disaster WHERE DisasterId IN (' . $dlist . ');');
	$us->q->dreg->query('DELETE FROM EEData   WHERE DisasterId IN (' . $dlist . ');');
	return $iCount;
}
</script>
