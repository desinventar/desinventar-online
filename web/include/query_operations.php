<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporacion OSSO
#

function query_is_v1($xml_string)
{
	$xml_tag = '<DIQuery />';
	$pos = strpos($xml_string, $xml_tag);
	if ($pos == false)
	{
		$pos = -1;
	}
	if ($pos >= 0)
	{
		$pos += 11;
	}
	return $pos;
}

function query_is_v2($xml_string)
{
	$iReturn = 1;
	try
	{
		$xml_doc = new SimpleXMLElement($xml_string);
		fb('query v2 loaded');
	}
	catch (Exception $e)
	{
		$iReturn = -1;
		showErrorMsg('XML cannot be parsed');
	}
	return $iReturn;
}

function query_read_v1($xml_string)
{
	# Attempt to read as 1.0 query version (malformed XML)		
	$pos = query_is_v1($xml_string);
	$value = substr($xml_string, $pos);
	$query = unserialize(base64_decode($value));
	return $query;
}

function query_convert_v1_to_v2()
{
} #query_convert_v1_to_v2()

</script>
