<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function geography_delete_items($prmConn, $prmGeoLevelId)
{
	$answer = ERR_NO_ERROR;
	$query = 'DELETE FROM Geography WHERE GeographyLevel>=' . $prmGeoLevelId;
	$prmConn->query($query);
	return $answer;
}

function geography_import_from_dbf($prmSession, $prmGeoLevelId, $prmFilename, $prmCode, $prmName, $prmParentCode)
{
	$iReturn = ERR_NO_ERROR;
	if (! file_exists($prmFilename))
	{	
		$iReturn = ERR_DEFAULT_ERROR;
	}
	if ($iReturn > 0)
	{
		$geo_list = array();
		$query = 'SELECT GeographyId,GeographyCode FROM Geography WHERE GeographyLevel=' . $prmGeoLevelId . ' ORDER BY GeographyId';
		foreach($prmSession->q->dreg->query($query,PDO::FETCH_ASSOC) as $row)
		{
			if ($row['GeographyId'] != '')
			{
				$geo_list[$row['GeographyCode']] = array(
					'id' => $row['GeographyId'],
					'updated' => 0
				);
			}
		}

		# Set default value GeographyActive=1 for elements in this level
		$query = 'UPDATE Geography SET GeographyActive=1 WHERE GeographyActive>0 AND GeographyLevel=' . $prmGeoLevelId;
		$prmSession->q->dreg->query($query);

		$item_count = 0;
		$parent_cache = array();
		$dbf = dbase_open($prmFilename, 'r');
		for($i = 1; $i <= dbase_numrecords($dbf); $i++)
		{
			$row = dbase_get_record_with_names($dbf, $i);
			if ($row['deleted'] == 0)
			{
				$geography_code = $row[$prmCode];
				$geography_name = utf8_encode($row[$prmName]);
				$geography_id = '';
				if (isset($geo_list[$geography_code]['id']))
				{
					$geography_id = $geo_list[$geography_code]['id'];
					$geo_list[$geography_code]['updated'] = 1;
				}
				else
				{
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
				}
				$o = new DIGeography($prmSession, $geography_id);
				$o->set('GeographyName', $geography_name);
				$o->set('GeographyCode', $geography_code);
				$o->set('GeographyLevel', $prmGeoLevelId);
				if ($geography_id == '')
				{
					$o->setGeographyId($parent_id);
					$geography_id = $o->get('GeographyId');
					$o->setGeographyFQName();
					if (count($geo_list) > 0)
					{
						$o->set('GeographyActive',2);
					}
					else
					{
						$o->set('GeographyActive',1);
					}
					$r = $o->insert();
				}
				else
				{
					$r = $o->update();
				}
				if ($r > 0)
				{
					$item_count++;
				}
			}
		} #for
		dbase_close($dbf);
		# Search the elements that are not found in the new shape file and
		# mark them for revision
		foreach($geo_list as $key => $value)
		{
			if ($value['updated'] < 1)
			{
				$query = 'UPDATE Geography SET GeographyActive=3 WHERE GeographyId LIKE "' . $value['id'] . '%"';
				$prmSession->q->dreg->query($query);
			}
		}
	}
	return $iReturn;
} #import_geography_from_dbf

function geography_update_dbf_record($prmDBFFile, $prmFieldCode, $prmFieldName, $prmGeographyCode, $prmGeographyName)
{
	$answer = 1;

	if (! file_exists($prmDBFFile) )
	{
		$answer = 0;
	}
	if ($answer > 0)
	{
		$dbf = dbase_open($prmDBFFile, 2);

		$field_list = geography_get_fields_from_dbf($dbf);
		$field_code = array_search($prmFieldCode, $field_list);
		if (false === $field_code)
		{	
			$answer = 0;
		}
		if ($answer > 0)
		{
			$field_name = array_search($prmFieldName, $field_list);
			if (false === $field_name)
			{
				$answer = 0;
			}
		}
	}
	if ($answer > 0)
	{
		$i = 0;
		$count = dbase_numrecords($dbf);
		$bContinue = 1;
		$answer = 0;
		while($bContinue > 0)
		{
			$row = dbase_get_record($dbf, $i);
			if ($row[$field_code] == $prmGeographyCode)
			{
				$row[$field_name] = utf8_decode($prmGeographyName);
				unset($row['deleted']);
				dbase_replace_record($dbf, $row, $i);
				$row = dbase_get_record($dbf, $i);
				$answer = 1;
			}
			$i++;
			if ($i > $count)
			{
				$bContinue = 0;
			}
		}
		dbase_close($dbf);
	}
	return $answer;
}

function geography_get_fields_from_dbf($dbf)
{
	$header = dbase_get_header_info($dbf);
	$field_list = array();
	foreach($header as $field)
	{
		$field_list[] = $field['name'];
	}
	return $field_list;
}

function geography_get_fields_from_dbffile($prmFilename)
{
	$dbf = dbase_open($prmFilename, 'r');
	$field_list = geography_get_fields_from_dbf($dbf);
	dbase_close($dbf);
	return $field_list;
}

function geography_get_items_count($conn, $prmGeoLevelId)
{
	$query = 'SELECT COUNT(*) AS C FROM Geography WHERE GeographyLevel=' . $prmGeoLevelId;
	$count = 0;
	foreach($conn->query($query, PDO::FETCH_ASSOC) as $row)
	{
		$count = $row['C'];
	}
	return $count;
}

function geography_export_to_csv($conn)
{
	$query = 'SELECT * FROM Geography ORDER BY GeographyId';
	$csv = '';
	foreach($conn->query($query, PDO::FETCH_ASSOC) as $row)
	{
		$csv .= sprintf('%d,"%s","%s","%s",%d' . "\n",
			$row['GeographyLevel'],
			$row['GeographyId'],
			$row['GeographyCode'],
			$row['GeographyName'],
			$row['GeographyActive']
		);
	}
	return $csv;
}
</script>
