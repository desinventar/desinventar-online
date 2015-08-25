<?php
	require_once('../include/loader.php');
	//phpInfo();
	$zip = new ZipArchive();
	$filename = 'D:/Tmp/c.zip';
	if ($zip->open($filename) !== TRUE) {
		exit('Cannot open ' . $filename . "\n");
	}
	$zip->extractTo(TEMP, 'info.xml');
	
	// Stream reader example, open directly the xml files and parses it
	$reader = new XMLReader();
	$reader->open('zip://' . $filename . '#info.xml');
	$odt_meta = array();
	while($reader->read()) {
		if ($reader->nodeType == XMLREADER::ELEMENT) {
			$elm = $reader->name;
		} else {
			if ($reader->nodeType == XMLREADER::END_ELEMENT && $reader->name == 'office:meta') {
				break;
			}
			if (!trim($reader->value)) {
				continue;
			}
			$odt_meta[$elm] = $reader->value;
		}
	}
	print_r($odt_meta);
