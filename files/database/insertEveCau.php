<?php
require_once '../../vendor/autoload.php';

use DesInventar\Legacy\Model\Cause;
use DesInventar\Legacy\Model\Event;

$FileName = $argv[1];
$LangIsoCode = $argv[2];
$TableName   = $argv[3];
$fh = fopen($FileName, 'r');

// Delete existing records
$query = 'DELETE FROM ' . $TableName . ' WHERE LangIsoCode="' . $LangIsoCode . '"';
fwrite(STDOUT, $query . ";\n");

// First line are headers
$values = fgetcsv($fh, 1000, ',');

// Process lines
while (! feof($fh)) {
    $values = fgetcsv($fh, 1000, ',');
    if (!empty($values) && count($values) > 1) {
        switch ($LangIsoCode) {
            case 'spa':
                    $i = 5;
                break;
            case 'eng':
                    $i = 6;
                break;
            case 'fre':
                    $i = 7;
                break;
            case 'por':
                    $i = 8;
                break;
            default:
                    $i = 6;
                break;
        }
        if ($TableName == 'EVENT') {
            $e = new Event(null);
            $e->set('LangIsoCode', $LangIsoCode);
            $e->set('RegionId', '');
            $e->set('EventPredefined', 1);
            $e->set('EventId', $values[0]);
            $e->set('EventRGBColor', $values[1]);
            $keywords = trim($values[2]);
            if (($keywords != '') && (substr($keywords, -1, 1) != ';')) {
                $keywords .= ';';
            }
            $e->set('EventKeyWords', $keywords);
            $e->set('EventName', $values[$i]);
            $e->set('EventDesc', $values[$i+4]);
            fwrite(STDOUT, $e->getInsertQuery() . ";\n");
            fwrite(STDOUT, $e->getUpdateQuery() . ";\n");
        }
        if ($TableName == 'CAUSE') {
            $e = new Cause(null);
            $e->set('LangIsoCode', $LangIsoCode);
            $e->set('RegionId', '');
            $e->set('CausePredefined', 1);
            $e->set('CauseId', $values[0]);
            $e->set('CauseRGBColor', $values[1]);
            $keywords = trim($values[2]);
            if (($keywords != '') && (substr($keywords, -1, 1) != ';')) {
                $keywords .= ';';
            }
            $e->set('CauseKeyWords', $keywords);
            $e->set('CauseName', $values[$i]);
            $e->set('CauseDesc', $values[$i+4]);
            fwrite(STDOUT, $e->getInsertQuery() . ";\n");
            fwrite(STDOUT, $e->getUpdateQuery() . ";\n");
        }
    }
}
fclose($fh);
exit(0);
