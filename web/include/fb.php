<?php
if (isset($_SERVER["HTTP_HOST"]))
{
	if (isset($_SERVER["WINDIR"]))
	{
		// Running on a Windows Server
		// 2011-02-25 (jhcaiced) Use DOCUMENT_ROOT to get installation path	
		$Install_Dir = dirname(dirname($_SERVER['DOCUMENT_ROOT']));		
		$FBCore = $Install_Dir . '/apps/FirePHPCore-0.3.2/lib/FirePHPCore/fb.php';
	}
	else
	{
		// Running on a Linux Server
		$FBCore = SRCDIR . '/vendor/firephp/firephp-core/lib/FirePHPCore/fb.php';
	}

	if (file_exists($FBCore))
	{
		require_once($FBCore);
	}
	else
	{
		function fb()
		{
			// dummy fb() function, doesn't do anything...
		}
	}
}
else
{
	// Command Line Mode...
	function fb($Msg)
	{
		print "fb: "; print_r($Msg); print "\n";
	}
}

function fbdebug($sMsg)
{
	fb(time() . ' ' . $sMsg);
}

function showErrorMsg($sMsg, Exception $e = null)
{
	if ($e != null)
	{
		$sMsg = 'ERROR ' . $sMsg . $e->getMessage();
	}
	fb($sMsg);
}
