<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporación OSSO
#

class DIProfile
{
	protected $xml = '';
	public function __construct($prmSession, $xml_profile)
	{
		$this->session = $prmSession;
		$this->xml = $xml_profile;
	} #__construct()
	
	public function execute($xml = '')
	{
		if ($xml == '') { $xml = $this->xml; }
		$html = '';
		foreach($xml->xpath('title') as $node)
		{
			$title = query_trim($node);
			$html .= $title . '<br />';
		}
		$xml_query = reset($xml->xpath('query'));
		foreach($xml->xpath('graph') as $xml_graph)
		{
			$graph = new DIGraphXML($this->session, $xml_graph, $xml_query);
			$graph->execute();
			$html .= '<img src="' . $graph->output['ImageURL'] . '" /><br />';
		}
		return $html;		

	}
} #class
</script>
