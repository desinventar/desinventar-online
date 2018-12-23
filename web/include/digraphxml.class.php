<?php
namespace DesInventar\Legacy;

use DesInventar\Common\QueryOperations;

class DIGraphXML extends DIGraph
{
    public function __construct($prmSession, $xml_graph, $xml_query)
    {
        $this->xml_graph = $xml_graph;
        $this->xml_query = $xml_query;
        $query = new QueryOperations();
        $options = $query->convertV2toV1($this->xml_query);
        $graph_options = $this->convertV2ToV1($this->xml_graph);
        if (count($graph_options) > 0) {
            $options['Graph'] = $graph_options;
        }
        parent::__construct($prmSession, $options);
    }

    public function convertV2ToV1($xml_graph)
    {
        $query = new QueryOperations();
        $answer = array();
        $answer['Type']    = $query->trim(reset($xml_graph->xpath('type')));
        $answer['SubType'] = $query->trim(reset($xml_graph->xpath('subtype')));
        return $answer;
    }
}
