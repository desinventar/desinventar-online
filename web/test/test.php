#!/usr/bin/php
<script language="php">
	require_once('../include/loader.php');
	require_once('../include/digeolevel.class.php');

	$us = new UserSession();
	
	$o = new DIGeoCarto($us);
	echo $o->getCreateTable() . "\n";
	print_r($o->oField);
	
	#$pass = generatePasswd();
	#print($pass) . "<br />\n";
	#print md5('di8welcome') . "<br />\n";
	#print md5('nidia') . '<br />';
	/*
	
	$r = new DIRegion($us, '', TMP_DIR . '/info.xml');
	print $r->get('RegionId') . "<br />\n";
	print $r->get('RegionLabel') . "<br />\n";
	print $r->get('LangIsoCode') . "<br />\n";
	print $r->get('CountryIso') . "<br />\n";
	*/
</script>
