<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporación OSSO
#

class DIGraphXML extends DIGraph
{
	public function __construct($prmSession, $prmXML)
	{
		$options = query_convert_v2_to_v1($prmXML);
		$graph_options = $this->convert_v2_to_v1($prmXML);
		if (count($graph_options) > 0)
		{
			$options['Graph'] = $graph_options;
		}
		parent::__construct($prmSession, $options);
	} #__construct()
	
	public function convert_v2_to_v1($xml_string)
	{
		$answer = array();
		$xml_doc = new SimpleXMLElement($xml_string);
		$xml_query = reset($xml_doc->xpath('graph'));
		$answer['Type']    = query_trim(reset($xml_query->xpath('type')));
		$answer['SubType'] = query_trim(reset($xml_query->xpath('subtype')));
		return $answer;		
	}
} #class
</script>
