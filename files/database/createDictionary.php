<?php
require_once '../../vendor/autoload.php';

use DesInventar\Common\Util;

$h = fopen('./doc-db.csv', 'r');
$i = 0;
print "DELETE FROM Dictionary;" . "\n";
print "DELETE FROM LabelGroup;" . "\n";
if (!$h) {
    fprintf(STDERR, 'Cannot open strings file' . "\n");
    exit(0);
}
while (!feof($h)) {
    $a = fgetcsv($h);
    $util = new Util();
    if (($i === 0) || (!empty($a) && (count($a) <= 1))) {
        $i++;
        continue;
    }
    $now = gmdate('c');
    $query = sprintf(
        'INSERT INTO LabelGroup VALUES ("%s","%s","%s","%s",%d,"%s","%s","%s");',
        $i,
        $a[0],
        $a[1],
        $a[2],
        null,
        $now,
        $now,
        $now
    );
    print $query . "\n";
    $langlist = array('eng','spa','por','fre');
    $Index = 3; // First column with language information
    foreach ($langlist as $Lang) {
        $query = sprintf(
            'INSERT INTO Dictionary VALUES ("%s","%s","%s","%s","%s","%s","%s","%s","%s");',
            $i,
            $Lang,
            $a[$Index],
            $util->escapeQuotes($a[$Index+4]),
            $util->escapeQuotes($a[$Index+8]),
            $util->escapeQuotes($a[$Index+12]),
            $now,
            $now,
            $now
        );
        print $query . "\n";
        $Index++;
    }
    $i++;
}
fclose($h);
