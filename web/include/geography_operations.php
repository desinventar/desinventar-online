<?php
use DesInventar\Legacy\Model\GeographyItem;
use DesInventar\Legacy\GeographyOperations;
use DesInventar\Helpers\Dbf;

function geographyDeleteItems($prmConn, $prmGeoLevelId)
{
    $answer = ERR_NO_ERROR;
    $query = 'DELETE FROM Geography WHERE GeographyLevel>=' . $prmGeoLevelId;
    $prmConn->query($query);
    return $answer;
}

function geographyImportFromDbf(
    $logger,
    $prmSession,
    $prmGeoLevelId,
    $prmFilename,
    $prmCode,
    $prmName,
    $prmParentCode,
    $prmCharset
) {
    return GeographyOperations::importFromDbf(
        $prmSession->q->dreg,
        $prmGeoLevelId,
        $prmFilename,
        [
            'code' => $prmCode,
            'name' => $prmName,
            'parentCode' => $prmParentCode,
            'charset' => $prmCharset
        ]
    );
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

function geographyGetFieldsFromDbfFile($logger, $prmFilename)
{
    $logger->debug('geography:get columns from dbf file: ' . $prmFilename);
    $dbf = dbase_open($prmFilename, 0);
    $field_list = geographyGetFieldsFromDbf($dbf);
    dbase_close($dbf);
    $logger->debug('geography:getcolumns from dbf file: ' . json_encode($field_list));
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
