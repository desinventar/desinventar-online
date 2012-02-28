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
			$geo_list[$row['GeographyCode']] = array('id' => $row['GeographyId'],'updated' => 0);
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
				$geography_code       = $row[$prmCode];
				$geography_name       = iconv('windows-1252', 'utf-8', $row[$prmName]);
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

function get_dbf_fields($prmFilename)
{
	$dbf = dbase_open($prmFilename, 'r');
	$header = dbase_get_header_info($dbf);
	$field_list = array();
	foreach($header as $field)
	{
		$field_list[] = $field['name'];
	}
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
</script>
