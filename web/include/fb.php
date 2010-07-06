<script language="php">
if (isset($_SERVER["HTTP_HOST"])) {
	if (isset($_SERVER["WINDIR"])) {
		// Running on a Windows Server
		// 2009-05-01 (jhcaiced) Read Registry to obtain MS4W installation path	
		$shell = new COM("WScript.Shell") or die("Requires Windows Scripting Host");
		$Install_Dir = $shell->RegRead("HKEY_LOCAL_MACHINE\\Software\\OSSO\\DesInventar8\Install_Dir");		
		$FBCore = $Install_Dir . '/ms4w/apps/FirePHPCore-0.3.1/lib/FirePHPCore/fb.php';
	} else {
		// Running on a Linux Server
		$FBCore = '/usr/share/pear/FirePHPCore/fb.php';
	}

	if (file_exists($FBCore)) {
		require_once($FBCore);
	} else {
		function fb() {
			// dummy fb() function, doesn't do anything...
		}
	}
} else {
	// Command Line Mode...
	function fb($Msg) {
		print "fb: "; print_r($Msg); print "\n";
	}
}

function showErrorMsg($sMsg) {
	fb($sMsg);
}

</script>
