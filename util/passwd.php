#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . '/include/dievent.class.php');
	require_once(BASE . '/include/dicause.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/didisaster.class.php');
	require_once(BASE . '/include/dieedata.class.php');
	require_once(BASE . '/include/diimport.class.php');
	
	//$pass = generatePasswd();
	//print($pass) . "<br />\n";
	
	showPasswd('di8');
	showPasswd('di82');
	function showPasswd($myPasswd) {
		print $myPasswd . ' => ' . md5($myPasswd) . "\n";
	}
</script>
