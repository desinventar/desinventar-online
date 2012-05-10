<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporación OSSO
#

class DIProfile
{
	protected $xml = '';
	public function __construct($prmSession, $prmXML)
	{
		$this->xml = $prmXML;
	} #__construct()
	
	public function execute()
	{
		$html = '';
		$xml_doc = new SimpleXMLElement($this->xml);
		$xml_query = reset($xml_doc->xpath('profile/item'));
		foreach($xml_query->xpath('title') as $node)
		{
			$html .= query_trim($node) . '<br />';
		}
		foreach($xml_query->xpath('graph') as $node)
		{
			$graph = new DIGraphXML($us, query_trim($node));
			$graph->execute();
			$html .= '<img src="' . $graph->output['ImageURL'] . '" /><br />';
		}
		return $html;		

	}
} #class
</script>
