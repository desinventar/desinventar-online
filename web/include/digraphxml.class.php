<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporación OSSO
#

class DIGraphXML extends DIGraph
{
	public function __construct($prmSession, $xml_graph, $xml_query)
	{
		$this->xml_graph = $xml_graph;
		$this->xml_query = $xml_query;
		$options = query_convert_v2_to_v1($this->xml_query);
		$graph_options = $this->convert_v2_to_v1($this->xml_graph);
		if (count($graph_options) > 0)
		{
			$options['Graph'] = $graph_options;
		}
		parent::__construct($prmSession, $options);
	} #__construct()
	
	public function convert_v2_to_v1($xml_graph)
	{
		$answer = array();
		$answer['Type']    = query_trim(reset($xml_graph->xpath('type')));
		$answer['SubType'] = query_trim(reset($xml_graph->xpath('subtype')));
		return $answer;		
	}
} #class
</script>
