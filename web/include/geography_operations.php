<?php
use DesInventar\Legacy\Model\GeographyItem;

function geographyDeleteItems($prmConn, $prmGeoLevelId)
{
    $answer = ERR_NO_ERROR;
    $query = 'DELETE FROM Geography WHERE GeographyLevel>=' . $prmGeoLevelId;
    $prmConn->query($query);
    return $answer;
}

function geographyImportFromDbf(
    $prmSession,
    $prmGeoLevelId,
    $prmFilename,
    $prmCode,
    $prmName,
    $prmParentCode,
    $prmCharset
) {
    $iReturn = ERR_NO_ERROR;
    if (! file_exists($prmFilename)) {
        $iReturn = ERR_DEFAULT_ERROR;
    }
    if ($iReturn > 0) {
        $geo_list = array();
        $query = 'SELECT GeographyId,GeographyCode,GeographyName FROM Geography ' .
            ' WHERE GeographyLevel=' . $prmGeoLevelId;
        $query .= ' ORDER BY GeographyId';
        foreach ($prmSession->q->dreg->query($query, PDO::FETCH_ASSOC) as $row) {
            if ($row['GeographyId'] != '') {
                $geo_list[$row['GeographyCode']] = array(
                    'id' => $row['GeographyId'],
                    'name' => $row['GeographyName'],
                    'updated' => 0
                );
                $geo_name_count[$row['GeographyName']] = 1;
            }
        }

        // Set default value GeographyActive=1 for elements in this level
        $query = 'UPDATE Geography SET GeographyActive=1 WHERE GeographyActive>0 AND GeographyLevel=' . $prmGeoLevelId;
        $prmSession->q->dreg->query($query);

        $item_count = 0;
        $parent_cache = array();
        $dbf = dbase_open($prmFilename, 'r');
        $dbf_count = dbase_numrecords($dbf);
        for ($i = 1; $i <= $dbf_count; $i++) {
            $row = dbase_get_record_with_names($dbf, $i);
            if ($row['deleted'] == 0) {
                $geography_code = trim($row[$prmCode]);
                $geography_name = trim($row[$prmName]);
                if ($prmCharset !== 'UTF-8') {
                    $geography_name = utf8_encode($geography_name);
                }
                $geography_id = '';
                if (isset($geo_list[$geography_code])) {
                    $geography_id = $geo_list[$geography_code]['id'];
                    $geo_list[$geography_code]['updated'] = 1;
                } else {
                    $parent_code = '';
                    $geography_id = '';
                    if ($prmParentCode != '') {
                        $parent_code = trim($row[$prmParentCode]);
                    }
                    if (isset($parent_cache[$parent_code])) {
                        $parent_id = $parent_cache[$parent_code];
                    } else {
                        $parent_id = GeographyItem::getIdByCode($prmSession, $parent_code);
                        $parent_cache[$parent_code] = $parent_id;
                    }
                }
                $o = new GeographyItem($prmSession, $geography_id);
                $o->set('GeographyName', $geography_name);
                $o->set('GeographyCode', $geography_code);
                $o->set('GeographyLevel', $prmGeoLevelId);
                if ($geography_id == '') {
                    $o->setGeographyId($parent_id);
                    $geography_id = $o->get('GeographyId');
                    if (isset($geo_name_count[$geography_name])) {
                        $geography_name .= ' ' . $geo_name_count[$geography_name] + 1;
                        $o->set('GeographyName', $geography_name);
                    }
                    $o->setGeographyFQName();
                    $geography_active = 1;
                    if (count($geo_list) > 0) {
                        $geography_active = 2;
                    }
                    $o->set('GeographyActive', $geography_active);
                    $r = $o->insert();
                } else {
                    $r = $o->update();
                }
                if ($r > 0) {
                    $item_count++;
                }
            }
        }
        dbase_close($dbf);
        // Search the elements that are not found in the new shape file and
        // mark them for revision
        $item_count = 0;
        foreach ($geo_list as $key => $value) {
            if ($value['updated'] < 1) {
                $item_count++;
                $query = 'UPDATE Geography SET GeographyActive=3 WHERE GeographyId LIKE "' . $value['id'] . '%"';
                $prmSession->q->dreg->query($query);
            }
        }
    }
    return $iReturn;
}

function geographyUpdateDbfRecord(
    $prmDBFFile,
    $prmFieldCode,
    $prmFieldName,
    $prmGeographyCode,
    $prmGeographyName,
    $prmNewGeographyCode = ''
) {
    $answer = 1;

    if (! file_exists($prmDBFFile)) {
        $answer = 0;
    }
    if ($answer > 0) {
        $dbf = dbase_open($prmDBFFile, 2);

        $field_list = geographyGetFieldsFromDbf($dbf);
        $field_code = array_search($prmFieldCode, $field_list);
        if (false === $field_code) {
            $answer = 0;
        }
        if ($answer > 0) {
            $field_name = array_search($prmFieldName, $field_list);
            if (false === $field_name) {
                $answer = 0;
            }
        }
    }
    if ($answer > 0) {
        $i = 0;
        $count = dbase_numrecords($dbf);
        $bContinue = 1;
        $answer = 0;
        while ($bContinue > 0) {
            $row = dbase_get_record($dbf, $i);
            if (trim($row[$field_code]) == $prmGeographyCode) {
                $row[$field_name] = trim(utf8_decode($prmGeographyName));
                if ($prmNewGeographyCode != '') {
                    $row[$field_code] = trim($prmNewGeographyCode);
                } else {
                    $row[$field_code] = trim($row[$field_code]);
                }
                unset($row['deleted']);
                dbase_replace_record($dbf, $row, $i);
                $row = dbase_get_record($dbf, $i);
                $answer = 1;
            }
            $i++;
            if ($i > $count) {
                $bContinue = 0;
            }
        }
        dbase_close($dbf);
    }
    return $answer;
}

function geographyGetFieldsFromDbf($dbf)
{
    $header = dbase_get_header_info($dbf);
    $field_list = array();
    foreach ($header as $field) {
        $field_list[] = $field['name'];
    }
    return $field_list;
}

function geographyGetFieldsFromDbfFile($prmFilename)
{
    $dbf = dbase_open($prmFilename, 'r');
    $field_list = geographyGetFieldsFromDbf($dbf);
    dbase_close($dbf);
    return $field_list;
}

function geographyGetItemsCount($conn, $prmGeoLevelId)
{
    $query = 'SELECT COUNT(*) AS C FROM Geography WHERE GeographyLevel=' . $prmGeoLevelId;
    $count = 0;
    foreach ($conn->query($query, PDO::FETCH_ASSOC) as $row) {
        $count = $row['C'];
    }
    return $count;
}

function geographyExportToCsv($conn)
{
    $query = 'SELECT * FROM Geography ORDER BY GeographyId';
    $csv = '';
    foreach ($conn->query($query, PDO::FETCH_ASSOC) as $row) {
        $csv .= sprintf(
            '%d,"%s","%s","%s",%d' . "\n",
            $row['GeographyLevel'],
            $row['GeographyId'],
            $row['GeographyCode'],
            $row['GeographyName'],
            $row['GeographyActive']
        );
    }
    return $csv;
}
