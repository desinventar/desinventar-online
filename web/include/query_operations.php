<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

function query_is_v1($xml_string)
{
    $xml_tag = '<DIQuery />';
    $pos = strpos($xml_string, $xml_tag);
    if ($pos == false) {
        $pos = -1;
    }
    if ($pos >= 0) {
        $pos += 11;
    }
    return $pos;
}

function query_is_v2($xml_string)
{
    $iReturn = 1;
    try {
        $xml_doc = new SimpleXMLElement($xml_string);
        $xml_query = reset($xml_doc->xpath('query'));
        if ($xml_query == '') {
            $iReturn = -2; # Valid XML but no query_design element
        }
    } catch (Exception $e) {
        $iReturn = -1;
        showErrorMsg(debug_backtrace(), $e, 'XML Cannot be parsed');
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

function query_convert_v2_to_v1($xml_query)
{
    $query = array();
    $period_start = query_trim(reset($xml_query->xpath('period/start')));
    $period_end   = query_trim(reset($xml_query->xpath('period/end')));
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
    $a = array();
    foreach ($xml_query->xpath('status/value') as $status) {
        $a[] = query_trim($status);
    }
    $query['D_RecordStatus'] = $a;

    $a = array();
    foreach ($xml_query->xpath('geography/id') as $id) {
        $a[] = query_trim($id);
    }
    $query['D_GeographyId'] = $a;
    $query['QueryGeography']['OP'] = 'AND';
    $query['D_DisasterSiteNotes'][0] = '';
    $query['D_DisasterSiteNotes'][1] = query_trim(reset($xml_query->xpath('disaster_site_notes')));

    $a = array();
    foreach ($xml_query->xpath('event/id') as $id) {
        $a[] = query_trim($id);
    }
    $query['QueryEvent']['OP'] = 'AND';
    $query['D_EventId'] = $a;
    $query['D_EventDuration'] = query_trim(reset($xml_query->xpath('event/duration')));
    $query['D_EventNotes'][1] = query_trim(reset($xml_query->xpath('event/notes')));

    $a = array();
    foreach ($xml_query->xpath('cause/id') as $id) {
        $a[] = query_trim($id);
    }
    $query['QueryCause']['OP'] = 'AND';
    $query['D_CauseId'] = $a;
    $query['D_CauseNotes'][1] = query_trim(reset($xml_query->xpath('cause/notes')));

    return $query;
} #query_convert_v1_to_v2()

function query_trim($value)
{
    $value = (string) $value;
    $value = preg_replace('/\n/', '', $value);
    $value = preg_replace('/\t/', '', $value);
    $value = trim($value);
    return $value;
}
