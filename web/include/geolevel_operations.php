<?php

function form2data($frm, $RegionId)
{
    $dat = array();
    $dat['GeoLevelId'] = isset($frm['GeoLevelId']) ? $frm['GeoLevelId'] : -1;
    $dat['GeoLevelName'] = isset($frm['GeoLevelName']) ? $frm['GeoLevelName']: '';
    $dat['GeoLevelDesc'] = isset($frm['GeoLevelDesc']) ? $frm['GeoLevelDesc']: '';
    $cartoFile = $RegionId .'_adm0'. $dat['GeoLevelId'];
    $cartoPath = VAR_DIR .'/database/'. $RegionId . '/' . $cartoFile;
    // Replace files
    if (isset($_FILES['GeoLevelFileSHP']) && $_FILES['GeoLevelFileSHP']['error'] == UPLOAD_ERR_OK &&
        isset($_FILES['GeoLevelFileSHX']) && $_FILES['GeoLevelFileSHX']['error'] == UPLOAD_ERR_OK &&
        isset($_FILES['GeoLevelFileDBF']) && $_FILES['GeoLevelFileDBF']['error'] == UPLOAD_ERR_OK) {
        $uplodedShpFile = $_FILES['GeoLevelFileSHP']['tmp_name'];
        $uploadedShxFile = $_FILES['GeoLevelFileSHX']['tmp_name'];
        $uploadedDbfFile = $_FILES['GeoLevelFileDBF']['tmp_name'];
        move_uploaded_file($uplodedShpFile, $cartoPath .'.shp');
        move_uploaded_file($uploadedShxFile, $cartoPath .'.shx');
        move_uploaded_file($uploadedDbfFile, $cartoPath .'.dbf');
    } elseif (!file_exists($cartoPath .'.shp') ||
        !file_exists($cartoPath .'.shx') ||
        !file_exists($cartoPath .'.dbf')
    ) {
        // Check if exists files of map..
        $cartoFile = '';
    }
    $dat['GeoLevelLayerFile'] = $cartoFile;
    $dat['GeoLevelLayerCode'] = isset($frm['GeoLevelLayerCode']) ? $frm['GeoLevelLayerCode']: '';
    $dat['GeoLevelLayerName'] = isset($frm['GeoLevelLayerName']) ? $frm['GeoLevelLayerName']: '';
    return $dat;
}
