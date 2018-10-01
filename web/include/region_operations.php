<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

function form2region($val)
{
    $dat = array();
    $dat['RegionLabel']     = $val['RegionLabel'];
    $dat['LangIsoCode']     = $val['LangIsoCode'];
    $dat['CountryIso']      = $val['CountryIso'];
    if (empty($val['RegionId'])) {
        $dat['RegionId']    = Region::buildRegionId($dat['CountryIso'], $dat['RegionLabel']);
    } else {
        $dat['RegionId']    = $val['RegionId'];
    }
    if (isset($val['RegionActive']) && $val['RegionActive'] == 'on') {
        $dat['RegionStatus'] |= CONST_REGIONACTIVE;
    } else {
        $dat['RegionStatus'] &= ~CONST_REGIONACTIVE;
    }
    if (isset($val['RegionPublic']) && $val['RegionPublic'] == 'on') {
        $dat['RegionStatus'] |= CONST_REGIONPUBLIC;
    } else {
        $dat['RegionStatus'] &= ~CONST_REGIONPUBLIC;
    }
    return $dat;
}
