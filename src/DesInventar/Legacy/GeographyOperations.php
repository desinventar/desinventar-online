<?php
namespace DesInventar\Legacy;

use Exception;
use PDO;
use DesInventar\Helpers\Dbf;
use DesInventar\Helpers\LoggerHelper;
use DesInventar\Legacy\Model\GeographyItem;

class GeographyOperations
{
    public static function getValueFromDbfRecord($record, $column)
    {
        if (!isset($record[$column])) {
            throw new Exception('DBF record does not have column: ' . $column);
        }
        return trim($record[$column]);
    }

    public static function getRecordsFromDbf($filename, $options)
    {
        $count = Dbf::getRecordCount($filename);
        $records = Dbf::getRecords($filename, $count);
        $items = [];
        foreach ($records as $row) {
            $newItem = [
                'code' => self::getValueFromDbfRecord($row, $options['code']),
                'name' => self::getValueFromDbfRecord($row, $options['name']),
                'deleted' => self::getValueFromDbfRecord($row, 'deleted')
            ];
            if (isset($options['charset']) && $options['charset'] !== 'UTF-8') {
                $newItem['name'] = utf8_encode($newItem['name']);
            }
            if (isset($options['parentCode']) && $options['parentCode'] !== '') {
                $newItem['parentCode'] = self::getValueFromDbfRecord($row, $options['parentCode']);
            }
            $items[] = $newItem;
        }
        return $items;
    }

    public static function filterDeletedRecords($records)
    {
        return array_filter($records, function ($value) {
            return !isset($value['deleted']) || (isset($value['deleted']) && $value['deleted'] !== 0);
        });
    }

    public static function getGeograhyItemsByLevel($conn, $prmGeoLevelId)
    {
        $list = [];
        $query = 'SELECT GeographyId,GeographyCode,GeographyName FROM Geography ' .
            ' WHERE GeographyLevel=' . $prmGeoLevelId;
        $query .= ' ORDER BY GeographyId';
        foreach ($conn->query($query, PDO::FETCH_ASSOC) as $row) {
            if (empty($row['GeographyId'])) {
                continue;
            }
            $list[$row['GeographyCode']] = [
                'id' => $row['GeographyId'],
                'code' => $row['GeographyCode'],
                'name' => $row['GeographyName'],
                'updated' => 0
            ];
        }
        return $list;
    }

    public static function countByLevelId($conn, $levelId)
    {
        $query = 'SELECT COUNT(*) AS C FROM Geography WHERE GeographyLevel=' . $levelId;
        $count = 0;
        foreach ($conn->query($query, PDO::FETCH_ASSOC) as $row) {
            $count = $row['C'];
        };
        return intval($count);
    }

    public static function deleteByLevelId($conn, $levelId)
    {
        $query = 'DELETE  FROM Geography WHERE GeographyLevel=' . $levelId;
        return $conn->query($query);
    }

    public static function setActiveByLevelId($conn, $levelId)
    {
        $query = 'UPDATE Geography SET GeographyActive=1 WHERE GeographyActive>0 AND GeographyLevel=' . $levelId;
        return $conn->query($query);
    }

    public static function saveArrayToCSV($records, $filename)
    {
        $fh = fopen($filename, 'w');
        if (!$fh) {
            throw new Exception('saveArrayToCSV: Cannot create file : ' . $filename);
        }
        fputcsv($fh, array_keys(current($records)));
        foreach ($records as $row) {
            fputcsv($fh, $row);
        }
        fclose($fh);
    }

    public static function importFromDbf(
        $conn,
        $prmGeoLevelId,
        $prmFilename,
        $prmOptions
    ) {
        if (! file_exists($prmFilename)) {
            throw new Exception('geographyImportFromDbf: File not found:' . $prmFilename);
        }
        $dbfRecords = GeographyOperations::filterDeletedRecords(
            GeographyOperations::getRecordsFromDbf($prmFilename, $prmOptions)
        );
        return self::importFromArray($conn, $prmGeoLevelId, $dbfRecords);
    }

    public static function importFromArray($conn, $prmGeoLevelId, $records)
    {
        // Get current geography items in this level (use this for cache)
        $geo_list = self::getGeograhyItemsByLevel($conn, $prmGeoLevelId);

        // Keep track of the geography names to avoid having duplicates
        $geo_name_count = [];
        foreach ($geo_list as $item) {
            $geo_name_count[$item['name']] = 1;
        }

        // Set default value GeographyActive=1 for elements in this level
        self::setActiveByLevelId($conn, $prmGeoLevelId);

        $item_count = 0;
        $parent_cache = array();
        foreach ($records as $row) {
            $geography_code = trim($row['code']);
            $geography_name = trim($row['name']);
            $geography_id = '';
            $parent_id = '';
            if (isset($geo_list[$geography_code])) {
                $geography_id = $geo_list[$geography_code]['id'];
                $geo_list[$geography_code]['updated'] = 1;
            } else {
                $parent_code = '';
                $geography_id = '';
                if (isset($row['parentCode']) && $row['parentCode'] != '') {
                    $parent_code = trim($row['parentCode']);
                    if (isset($parent_cache[$parent_code])) {
                        $parent_id = $parent_cache[$parent_code];
                    } else {
                        $parent_id = GeographyItem::getIdByCode($conn, $parent_code);
                        $parent_cache[$parent_code] = $parent_id;
                    }
                }
            }
            $o = new GeographyItem($conn, $geography_id);
            $o->set('GeographyName', $geography_name);
            $o->set('GeographyCode', $geography_code);
            $o->set('GeographyLevel', $prmGeoLevelId);
            if ($geography_id === '') {
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
        // Search the elements that are not found in the new shape file and
        // mark them for revision
        foreach ($geo_list as $value) {
            if ($value['updated'] < 1) {
                $query = 'UPDATE Geography SET GeographyActive=3 WHERE GeographyId LIKE "' . $value['id'] . '%"';
                $conn->query($query);
            }
        }
        return $item_count;
    }
}
