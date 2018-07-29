<?php
$FileName    = $argv[1];
$LangIsoCode = $argv[2];

$dom = new DOMDocument('1.0', 'UTF-8');
$dom->load($FileName);

$xp = new DOMXPath($dom);

foreach ($xp->query('Group') as $tree) {
    $xp1 = new DOMXPath($dom);
    $GroupName = $tree->getAttribute('name');
    print "\n";
    print '[' . $GroupName . ']' . "\n";
    foreach ($xp1->query('Group[@name="' . $GroupName . '"]/Message') as $tree1) {
        $Message = $tree1->getAttribute('id');
        $xp2 = new DOMXPath($dom);
        $xpathQuery = 'Group[@name="' . $GroupName . '"]' .
          '/Message[@id="' . $Message . '"]' .
          '/Text[@LangIsoCode="' . $LangIsoCode . '"]';
        foreach ($xp2->query($xpathQuery) as $node) {
            $value = '' . $node->nodeValue;
            $value = preg_replace('/"/', '\'', $value);
            $value = preg_replace('/\n/', ' ', $value);
            $value = preg_replace('/\t/', ' ', $value);
            $value = preg_replace('/ +/', ' ', $value);
            print $Message . '=' . $value . "\n";
        }
    }
}
exit(0);
