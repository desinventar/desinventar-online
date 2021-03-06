<?php

namespace DesInventar\Legacy;

use Exception;
use PDO;
use DesInventar\Helpers\Dbf;
use DesInventar\Helpers\LoggerHelper;
use DesInventar\Legacy\Model\GeographyItem;

class GeographyOperations
{
    protected $conn = null;
    protected $logger = null;
    public function __construct($conn, $logger)
    {
        $this->conn = $conn;
        $this->logger = $logger;
    }

    public static function getValueFromArray($record, $column)
    {
        if (!isset($record[$column])) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ .  ':DBF record does not have column: ' . $column);
        }
        return trim($record[$column]);
    }

    public static function getRecordsFromDbf($filename, $options)
    {
        $dbf = new Dbf($filename);
        $count = $dbf->getRecordCount();
        $records = $dbf->getRecords($count);
        $items = [];
        foreach ($records as $row) {
            $newItem = [
                'code' => self::getValueFromArray($row, $options['code']),
                'name' => self::getValueFromArray($row, $options['name']),
                'deleted' => self::getValueFromArray($row, 'deleted')
            ];
            if (isset($options['charset']) && $options['charset'] !== 'UTF-8') {
                $newItem['name'] = utf8_encode($newItem['name']);
            }
            if (isset($options['parentCode']) && $options['parentCode'] !== '') {
                $newItem['parentCode'] = self::getValueFromArray($row, $options['parentCode']);
            }
            $items[$newItem['code']] = $newItem;
        }
        return $items;
    }

    public static function filterDeletedRecords($records)
    {
        return array_filter($records, function ($value) {
            return !isset($value['deleted']) || (isset($value['deleted']) && $value['deleted'] !== 0);
        });
    }

    public function getGeograhyItemsByLevel($prmGeoLevelId)
    {
        $list = [];
        $query = 'SELECT GeographyId,GeographyCode,GeographyName FROM Geography ' .
            ' WHERE GeographyLevel=' . $prmGeoLevelId;
        $query .= ' ORDER BY GeographyId';
        foreach ($this->conn->query($query, PDO::FETCH_ASSOC) as $row) {
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
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ': Cannot create file : ' . $filename);
        }
        fputcsv($fh, array_keys(current($records)));
        foreach ($records as $row) {
            fputcsv($fh, $row);
        }
        fclose($fh);
    }

    public static function getRecordsFromCsv($filename, $options)
    {
        $lines = file($filename, FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return [];
        }
        $csv = array_map("str_getcsv", $lines);
        $keys = array_shift($csv);
        if (!$keys) {
            return [];
        }
        foreach ($csv as $i => $row) {
            if ($row) {
                $csv[$i] = array_combine($keys, $row);
            }
        }
        $items = [];
        foreach ($csv as $row) {
            $newItem = [
                'code' => self::getValueFromArray($row, $options['code']),
                'name' => self::getValueFromArray($row, $options['name'])
            ];
            if (isset($options['charset']) && $options['charset'] !== 'UTF-8') {
                $newItem['name'] = utf8_encode($newItem['name']);
            }
            if (isset($options['parentCode']) && $options['parentCode'] !== '') {
                $newItem['parentCode'] = self::getValueFromArray($row, $options['parentCode']);
            }
            $items[] = $newItem;
        }
        return $items;
    }

    public function importFromCsv($prmGeoLevelId, $prmFilename, $options)
    {
        if (! file_exists($prmFilename)) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ': File not found:' . $prmFilename);
        }
        $records = self::getRecordsFromCsv($prmFilename, $options);
        return $this->importFromArray($prmGeoLevelId, $records);
    }

    public function importFromDbf($prmGeoLevelId, $prmFilename, $prmOptions)
    {
        if (! file_exists($prmFilename)) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ': File not found:' . $prmFilename);
        }
        $dbfRecords = self::filterDeletedRecords(
            self::getRecordsFromDbf($prmFilename, $prmOptions)
        );
        return $this->importFromArray($prmGeoLevelId, $dbfRecords);
    }

    public function importFromArray($prmGeoLevelId, $records)
    {
        // Get current geography items in this level (use this for cache)
        $geo_list = $this->getGeograhyItemsByLevel($prmGeoLevelId);

        // Keep track of the geography names to avoid having duplicates
        $geo_name_count = [];
        foreach ($geo_list as $item) {
            $parentId = substr($item['id'], 0, intval((strlen($item['id'])/5) - 1));
            $parentId = ($parentId === '') ? 'root': $parentId;
            $geo_name_count[$parentId][$item['name']] = 1;
        }

        // Set default value GeographyActive=1 for elements in this level
        self::setActiveByLevelId($this->conn, $prmGeoLevelId);

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
                $o = new GeographyItem($this->conn, $geography_id);
                $o->setFromArray([
                    'GeographyName' => $geography_name,
                    'GeographyCode' => $geography_code,
                    'GeographyLevel' => $prmGeoLevelId
                ]);
                $this->logger->debug($geography_code . ' ' . $geography_name . ' UPDATE');
                $o->update();
                continue;
            }

            // Insert new record
            $parent_code = '';
            $geography_id = '';
            $canInsert = false;
            if (isset($row['parentCode']) && $row['parentCode'] != '') {
                $parent_code = trim($row['parentCode']);
                if (isset($parent_cache[$parent_code])) {
                    $parent_id = $parent_cache[$parent_code];
                    $canInsert = true;
                } else {
                    $parent_id = GeographyItem::getIdByCode($this->conn, $parent_code);
                    if ($parent_id !== '') {
                        $canInsert = true;
                        $parent_cache[$parent_code] = $parent_id;
                    }
                }
            } else {
                $canInsert = true;
            }
            if (!$canInsert) {
                $this->logger->warning($geography_code . ' ' . $geography_name . ' SKIP');
                 // skip this record
                continue;
            }
            $o = new GeographyItem($this->conn, $geography_id);
            $o->set('GeographyName', $geography_name);
            $o->set('GeographyCode', $geography_code);
            $o->set('GeographyLevel', $prmGeoLevelId);
            $o->setGeographyId($parent_id);
            $geography_id = $o->get('GeographyId');
            $parentId = ($parent_id === '') ? 'root': $parent_id;
            if (isset($geo_name_count[$parentId][$geography_name])) {
                $o->set('GeographyName', $geography_name . ' - ' . $geo_name_count[$parentId][$geography_name]);
            }
            $geo_name_count[$parentId][$geography_name] =
                isset($geo_name_count[$parentId][$geography_name])
                ? $geo_name_count[$parentId][$geography_name]  + 1
                : 1;
            $o->setGeographyFQName();
            $geography_active = 1;
            if (count($geo_list) > 0) {
                $geography_active = 2;
            }
            $o->set('GeographyActive', $geography_active);
            $r = $o->insert();
            $this->logger->debug($geography_code . ' ' . $geography_name . ' INSERT');

            if ($r > 0) {
                $item_count++;
            }
        }
        // Search the elements that are not found in the new shape file and
        // mark them for revision
        foreach ($geo_list as $value) {
            if ($value['updated'] < 1) {
                $query = 'UPDATE Geography SET GeographyActive=3 WHERE GeographyId LIKE "' . $value['id'] . '%"';
                $this->conn->query($query);
            }
        }
        return $item_count;
    }
}
