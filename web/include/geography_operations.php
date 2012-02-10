<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function import_geography_from_dbf($prmSession, $prmGeoLevelId, $prmFilename, $prmCode, $prmName, $prmParentCode)
{
	$iReturn = ERR_NO_ERROR;
	if (! file_exists($prmFilename))
	{	
		$iReturn = ERR_DEFAULT_ERROR;
	}
	if ($iReturn > 0)
	{
		$parent_cache = array();
		$dbf = dbase_open($prmFilename, 'r');
		for($i = 1; $i <= dbase_numrecords($dbf); $i++)
		{
			$row = dbase_get_record_with_names($dbf, $i);
			if ($row['deleted'] == 0)
			{
				$geography_code       = $row[$prmCode];
				$geography_name       = iconv('windows-1252', 'utf-8', $row[$prmName]);
				$parent_code = '';
				if ($prmParentCode != '')
				{
					$parent_code = $row[$prmParentCode];
				}
				if (isset($parent_cache[$parent_code]))
				{
					$parent_id = $parent_cache[$parent_code];
				}
				else
				{
					$parent_id = DIGeography::getIdByCode($prmSession, $parent_code);
					$parent_cache[$parent_code] = $parent_id;
				}
				$o = new DIGeography($prmSession);
				$o->set('GeographyName', $geography_name);
				$o->set('GeographyCode', $geography_code);
				$o->setGeographyId($parent_id);
				$r = $o->insert();
				$geography_id = $o->get('GeographyId');
				printf('%3d %-10s %-10s %-20s %-20s %-20s %d' . "\n", $i, $geography_code, $parent_code, $geography_name, $parent_id, $geography_id, $r);
			}
		}
		dbase_close($dbf);
	}
	return $iReturn;
} //doImportGeographyFromDBF

</script>
