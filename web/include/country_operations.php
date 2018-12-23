<?php
function countryGetName($prm_base_conn, $prm_lang_iso_code, $prm_country_iso)
{
    $country_name = '';
    try {
        $query = 'SELECT CountryName FROM CountryName WHERE CountryIso=:country_iso AND LangIsoCode=:lang_iso_code';
        $sth = $prm_base_conn->prepare($query);
        $sth->bindParam(':country_iso', $prm_country_iso, PDO::PARAM_STR);
        $sth->bindParam(':lang_iso_code', $prm_lang_iso_code, PDO::PARAM_STR);
        $sth->execute();
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $country_name = $row['CountryName'];
        }
        $sth = null;
    } catch (Exception $e) {
        showErrorMsg(debug_backtrace(), $e, '');
    }

    return $country_name;
}

function countryGetList($prm_conn, $prm_lang_iso_code)
{
    $sQuery = 'SELECT CountryIso, CountryName FROM CountryName' .
        ' WHERE LangIsoCode="' . $prm_lang_iso_code . '" ORDER BY CountryName';
    $data = array('' => '');
    try {
        foreach ($prm_conn->query($sQuery) as $row) {
            $data[$row['CountryIso']] = $row['CountryName'];
        }
    } catch (Exception $e) {
        showErrorMsg(debug_backtrace(), $e, '');
    }

    return $data;
}

function countryGetListWithDatabases($prm_core, $prm_base, $prm_lang_iso_code)
{
    $country_list = array();
    $query = 'SELECT DISTINCT CountryIso FROM Region' .
        ' WHERE RegionStatus=3 AND CountryIso<>""';
    $index = 0;
    $country_iso_list = '';
    foreach ($prm_core->query($query) as $row) {
        if ($index > 0) {
            $country_iso_list .= ',';
        }
        $country_iso_list .= '"' . $row['CountryIso'] . '"';
        $index++;
    }
    $query = 'SELECT CountryIso,CountryName FROM CountryName' .
        ' WHERE CountryIso IN (' . $country_iso_list . ') AND' .
        ' LangIsoCode="' . $prm_lang_iso_code . '";';
    foreach ($prm_base->query($query) as $row) {
        $country_list[] = array(
            'CountryIso'  => $row['CountryIso'],
            'CountryName' => $row['CountryName']
        );
    }

    return $country_list;
}
