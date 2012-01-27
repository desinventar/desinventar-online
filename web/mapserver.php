<script language="php">
	require_once('/usr/share/pear/FirePHPCore/fb.php');
	$queryString = '';

	$_GET['MAP']         = '/usr/share/desinventar-8.2/worldmap/worldmap.map';
	$_GET['LAYERS']      = 'base';
	$_GET['TRANSPARENT'] = 'false';
	foreach($_GET as $key => $value)
	{
		if ($queryString != '') {
			$queryString .= '&';
		}
		$queryString .= $key . '=' . urlencode($value);
	}
	$url = 'http://127.0.0.1/cgi-bin/mapserv' . '?' . $queryString;
	fb($url);
	header('Content-type: image/png');
	echo file_get_contents($url);
</script>
