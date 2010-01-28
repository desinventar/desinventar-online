<?
	require_once('../include/loader.php');
	$pass = generatePasswd();
	print($pass) . "<br />\n";
	print md5('di8welcome') . "<br />\n";
?>
