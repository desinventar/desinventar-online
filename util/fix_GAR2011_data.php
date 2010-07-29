#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-07-28 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Process GAR2011 databases, apply rules to discard records.
  Fix GAR2011 Databases for some events that should not be deleted..

*/

require_once('../web/include/loader.php');

$RegionList = array();
$RegionList['GAR-ISDR-2011_CHL'] = 'CHL-1257983285-chile_inventario_historico_de_desastres';

foreach ($RegionList as $RegionId => $RegionOld) {
	$us->open($RegionId);
	print $RegionId . "\n";
	
	$SubQuery1 = 'DisasterBeginTime BETWEEN ("1970-00-01" AND "2009-12-31")';
	$SubQuery2 = 'LENGTH(GeographyId)==5';

	// Local Events
	//'EventId="ESTRUCTURA"';
	
	// Local Events CHL
	if ($RegionId == 'GAR-ISDR-2011_CHL') {
		$iCount += removeData($us, 'EventId="FALLA"');
		$iCount += removeData($us, 'EventId="INTOXICACIÓN"');
		$iCount += removeData($us, 'EventId="LITORAL"');
		$iCount += removeData($us, 'EventId="OZONO"');
	}

	// Local Events COL
	if ($RegionId == 'GAR-ISDR-2011_COL') {
		$iCount += removeData($us, 'EventId="INTOXICACION"');
	}
	
	// Local Events PER
	if ($RegionId == 'GAR-ISDR-2011_PER') {
		$iCount += removeData($us, 'EventId="EPIZOOTIA"');
		$iCount += removeData($us, 'EventId="EROSION"');
		$iCount += removeData($us, 'EventId="INTOXICACION"');
		$iCount += removeData($us, 'EventId="OLA DE FRIO"');
	}

	//Local Events VEN
	if ($RegionId == 'GAR-ISDR-2011_VEN') {
		$iCount += removeData($us, 'EventId="A. TRANSITO"');
		$iCount += removeData($us, 'EventId="ARBOL CAIDO"');
	}
	
	print 'Eventos Locales         : ' . $iCount . "\n";

	//$sQuery = 'UPDATE Disaster SET RecordStatus="DRAFT" WHERE LENGTH(GeographyId)==5';
	//$us->q->dreg->query($sQuery);
} //foreach

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
