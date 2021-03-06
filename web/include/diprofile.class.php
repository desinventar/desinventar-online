<?php
namespace DesInventar\Legacy;

use DesInventar\Common\QueryOperations;

class DIProfile
{
    protected $xml = '';
    public function __construct($prmSession, $xml_profile)
    {
        $this->session = $prmSession;
        $this->xml = $xml_profile;
    }

    public function execute($xml = '')
    {
        $query = new QueryOperations();
        $LangIsoCode = $this->session->LangIsoCode;
        if ($xml == '') {
            $xml = $this->xml;
        }
        $html = '';
        foreach ($xml->xpath('title/text[@langisocode="' . $LangIsoCode . '"]') as $node) {
            $title = $query->trim($node);
            $html .= '<h3>' . $title . '</h3>';
        }
        $xml_query = reset($xml->xpath('query'));
        foreach ($xml->xpath('graph') as $xml_graph) {
            $graph_title = $query->trim(reset($xml_graph->xpath('title/text[@langisocode="' . $LangIsoCode . '"]')));
            $html .= '<h4>' . $graph_title . '</h4>';
            $graph = new DIGraphXML($this->session, $xml_graph, $xml_query);
            $graph->execute();
            $html .= '<img src="' . $graph->output['ImageURL'] . '" /><br />';
        }
        return $html;
    }
}
