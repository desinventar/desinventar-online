<?php
if (isset($_SERVER["HTTP_HOST"]))
{
	// Location of FirePHP - loaded via composer
	$FBCore = SRCDIR . '/vendor/firephp/firephp-core/lib/FirePHPCore/fb.php';

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
