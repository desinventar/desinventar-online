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
		$options['MAP']         = $config->maps['worldmap_dir'] . '/world_adm0.map';
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
	$queryString .= '&mde=map';
	$url = 'http://' . $_SERVER['SERVER_ADDR'];
	if (! is_ssl() && isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != 80)) {
		$url .= ':' . $_SERVER['SERVER_PORT'];
	}
	$url .= '/cgi-bin/' . MAPSERV . '?' . $queryString;
	header('Content-type: ' . $options['FORMAT']);
	echo file_get_contents($url);
</script>
