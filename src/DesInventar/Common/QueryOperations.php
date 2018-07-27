<?php

namespace DesInventar\Common;

use \SimpleXMLElement;
use \Exception;

class QueryOperations
{
    public function isV1($xml_string)
    {
        $xml_tag = '<DIQuery />';
        $pos = strpos($xml_string, $xml_tag);
        if ($pos === false) {
            $pos = -1;
        }
        if ($pos >= 0) {
            $pos += 11;
        }
        return $pos;
    }

    public function isV2($xml_string)
    {
        $iReturn = 1;
        try {
            $xml_doc = new SimpleXMLElement($xml_string);
            $values = $xml_doc->xpath('query');
            $xml_query = reset($values);
            if ($xml_query == '') {
                // Valid XML but no query_design element
                $iReturn = -2;
            }
        } catch (Exception $e) {
            $iReturn = -1;
        }
        return $iReturn;
    }

    public function readV1($xml_string)
    {
        // Attempt to read as 1.0 query version (malformed XML)
        $pos = $this->isV1($xml_string);
        $value = substr($xml_string, $pos);
        $query = unserialize(base64_decode($value));
        return $query;
    }

    public function convertV2toV1($xml_query)
    {
        $query = array();
        $value = $xml_query->xpath('period/start');
        $period_start = $this->trim(reset($value));
        $value = $xml_query->xpath('period/end');
        $period_end   = $this->trim(reset($value));
        if ($period_start != '') {
            $query['D_DisasterBeginTime'][0] = substr($period_start, 0, 4);
            $query['D_DisasterBeginTime'][1] = substr($period_start, 5, 2);
            $query['D_DisasterBeginTime'][2] = substr($period_start, 8, 2);
        }

        if ($period_end != '') {
            $query['D_DisasterEndTime'][0] = substr($period_end, 0, 4);
            $query['D_DisasterEndTime'][1] = substr($period_end, 5, 2);
            $query['D_DisasterEndTime'][2] = substr($period_end, 8, 2);
        }
        $answer = array();
        foreach ($xml_query->xpath('status/value') as $status) {
            $answer[] = $this->trim($status);
        }
        $query['D_RecordStatus'] = $answer;

        $answer = array();
        foreach ($xml_query->xpath('geography/id') as $id) {
            $answer[] = $this->trim($id);
        }
        $query['D_GeographyId'] = $answer;
        $query['QueryGeography']['OP'] = 'AND';
        $query['D_DisasterSiteNotes'][0] = '';
        $value = $xml_query->xpath('disaster_site_notes');
        $query['D_DisasterSiteNotes'][1] = $this->trim(reset($value));

        $answer = array();
        foreach ($xml_query->xpath('event/id') as $id) {
            $answer[] = $this->trim($id);
        }
        $query['QueryEvent']['OP'] = 'AND';
        $query['D_EventId'] = $answer;
        $value = $xml_query->xpath('event/duration');
        $query['D_EventDuration'] = $this->trim(reset($value));
        $value = $xml_query->xpath('event/notes');
        $query['D_EventNotes'][1] = $this->trim(reset($value));

        $answer = array();
        foreach ($xml_query->xpath('cause/id') as $id) {
            $answer[] = $this->trim($id);
        }
        $query['QueryCause']['OP'] = 'AND';
        $query['D_CauseId'] = $answer;
        $value = $xml_query->xpath('cause/notes');
        $query['D_CauseNotes'][1] = $this->trim(reset($value));

        return $query;
    }

    public function trim($value)
    {
        $value = (string) $value;
        $value = preg_replace('/\n/', '', $value);
        $value = preg_replace('/\t/', '', $value);
        $value = trim($value);
        return $value;
    }
}
