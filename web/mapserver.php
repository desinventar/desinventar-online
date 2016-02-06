<script language="php">
	require_once('include/loader.php');
	$options = $_GET;

	if (! isset($options['SERVICE']))
	{
		$options['SERVICE'] = 'WMS';
	}
	if (! isset($options['VERSION']))
	{
		$options['VERSION'] = '1.1.1';
	}
	if (! isset($options['FORMAT']))
	{
		$options['FORMAT'] = 'image/png';
	}
	if (! isset($options['REQUEST']))
	{
		$options['REQUEST'] = 'GetMap';
	}
	if (! isset($options['STYLES']))
	{
		$options['STYLES'] = 'default,default';
	}
	
	if (isset($options['MAPID']))
	{
		$options['MAP'] = TMP_DIR .'/map_' . $options['MAPID'] . '.map';
		$options['TRANSPARENT'] = 'true';
		unset($options['MAPID']);
	}
	else
	{
		$options['MAPID'] = 'worldmap';
	}
	
	if ($options['MAPID'] == 'worldmap')
	{
		$options['MAP']         = MAPDIR . '/world_adm0.map';
		$options['LAYERS']      = 'base';
		$options['TRANSPARENT'] = 'false';
	}

	$queryString = '';
	foreach($options as $key => $value)
	{
		if ($queryString != '')
		{
			$queryString .= '&';
		}
		$queryString .= $key . '=' . urlencode($value);
	}
	$url = 'http://' . $_SERVER['HTTP_HOST'];
	if ($_SERVER['HTTP_PORT'] != 80)
	{
		$url .= ':' . $_SERVER['HTTP_PORT'];
	}
	$url .= '/cgi-bin/' . MAPSERV . '?' . $queryString;
	//$url = 'http://127.0.0.1/cgi-bin/' . MAPSERV . '?' . $queryString;
	header('Content-type: ' . $options['FORMAT']);
	echo file_get_contents($url);
</script>
