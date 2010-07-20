#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . '/include/dievent.class.php');
	require_once(BASE . '/include/dicause.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/didisaster.class.php');
	require_once(BASE . '/include/dieedata.class.php');
	require_once(BASE . '/include/diimport.class.php');
	require_once(BASE . '/include/diregion.class.php');
	
	$YearLast     = 2009;
	$VarList      = array('EffectPeopleDead','EffectHousesDestroyed','DisasterId');
	$FileList1    = array('EffectPeopleDead'      => 'Muertos',
	                      'EffectHousesDestroyed' => 'VivDestruidas',
	                      'DisasterId'            => 'NoFichas');
	$StatusList   = array('PUBLISHED','PUBLISHED,READY');
	$FileList2    = array('PUBLISHED' => 'Extensivo',
	                      'PUBLISHED,READY' => 'Extensivo+Intensivo');
	$RegionList   = array('GAR-ISDR-2011_COL','GAR-ISDR-2011_ECU','GAR-ISDR-2011_PER');
	$GeoLevelList = array('GAR-ISDR-2011_COL' => 1,
	                      'GAR-ISDR-2011_ECU' => 1,
	                      'GAR-ISDR-2011_PER' => 2);
	//$EveList      = array();
	//$EveList      = array('RAIN','FLOOD');
	//$EveList      = array('EARTHQUAKE');
	
	$us->login('diadmin','di8');
	foreach($RegionList as $RegionId) {
		print 'Region   : ' . $RegionId . "\n";
		$GeoLevel = $GeoLevelList[$RegionId];
		$us->open($RegionId);
		
		$sQuery = 'SELECT GeographyId,GeographyCode,GeographyFQName FROM Geography WHERE GeographyLevel=' . $GeoLevel . ' ORDER BY GeographyId';
		$table = array();
		foreach($us->q->dreg->query($sQuery) as $row) {
			$table[$row['GeographyId']]['Id']     = $row['GeographyId'];
			$table[$row['GeographyId']]['Codigo'] = $row['GeographyCode'];
			$table[$row['GeographyId']]['Nombre'] = $row['GeographyFQName'];
		}
		foreach($StatusList as $Status) {
			print 'Estado   : ' . $Status . "\n";
			foreach($VarList as $Field) {
				print 'Variable : ' . $Field . "\n";
				$iTotal = 0;
				for ($iYear = 1970; $iYear <= $YearLast; $iYear++) {
					printf('%02d ', $iYear % 100);
					foreach($table as $key => $row) {
						$table[$key][$iYear] = 0;
					}
					$GeoLen = ($GeoLevel + 1) * 5;
					
					// EffectField SubQuery
					if ($Field == 'DisasterId') {
						$SubQuery1 = 'COUNT(' . $Field . ') AS S';
						$SubQuery2 = '';
					} else {
						$SubQuery1 = 'SUM(' . $Field . 'Q) as S';
						$SubQuery2 = ' AND ' . $Field . 'Q>-1';
					}
					
					// RecordStatus SubQuery
					$SubQuery3 = '';
					$st = explode(',', $Status);
					$bFirst = true;
					foreach($st as $sta) {
						if (! $bFirst) { $SubQuery3 .= ' OR '; }
						$SubQuery3 .= 'RecordStatus="' . $sta . '"';
						$bFirst = false;
					}
					
					$SubQuery4 = '';
					$FileExt4 = '';
					$bFirst = true;
					foreach($EveList as $Event) {
						if (! $bFirst) { $SubQuery4 .= ' OR '; $FileExt4 .= '+'; }
						$SubQuery4 .= 'EventId="' . $Event . '"';
						$FileExt4 .= $Event;
						$bFirst = false;
					}
					if ($SubQuery4 != '') {
						$SubQuery4 = ' AND (' . $SubQuery4 . ')';
					}
					if ($FileExt4 != '') {
						$FileExt4 = '_' . $FileExt4;
					}
					
					// Assembly Query
					$sQuery = 'SELECT SUBSTR(GeographyId,1,' . $GeoLen . ') AS G, ' . $SubQuery1 . ' FROM Disaster WHERE ' .
							  ' (' . $SubQuery3 . ') ' . 
							  ' AND DisasterBeginTime LIKE "' . $iYear . '%" ' . 
							  $SubQuery2 .
							  $SubQuery4 . 
							  ' GROUP BY G ORDER BY G';
					foreach($us->q->dreg->query($sQuery) as $row) {
						if (array_key_exists($row['G'], $table)) {
							$table[$row['G']][$iYear] = $row['S'];
						}
					}
				}
				print "\n";

				$fh = fopen($RegionId . '_' . $FileList1[$Field] . '_' . $FileList2[$Status] . $FileExt4 . '.csv', 'w+');
				// Header
				$line = '"ID","CODIGO","NOMBRE",';
				for($iYear = 1970; $iYear <= $YearLast; $iYear++) {
					$line .= '"' . $iYear . '"';
					if ($iYear < $YearLast) {
						$line .= ',';
					}
				}
				fwrite($fh , $line . "\n");
				// Data
				foreach($table as $key => $row) {
					$line = sprintf('"%s","%s","%s",', $row['Id'],$row['Codigo'], $row['Nombre']);
					for($iYear = 1970; $iYear <= $YearLast; $iYear++) {
						$line .= $row[$iYear];
						if ($iYear < $YearLast) {
							$line .= ',';
						}
					}
					fwrite($fh , $line . "\n");
				} //foreach
				fclose($fh);
			} //foreach $Field
		} //foreach $Status
		$us->close();
	} //foreach $RegionId
	$us->logout();
</script>
