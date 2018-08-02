<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
namespace DesInventar\Legacy;

use DesInventar\Legacy\DIRegionDB;
use DesInventar\Legacy\DIRegionRecord;

use \ZipArchive;

class DatabaseOperations
{
    public static function delete($core, $prmRegionId)
    {
        $answer = ERR_NO_ERROR;
        $database_dir = VAR_DIR .'/database/'. $prmRegionId;
        rrmdir($database_dir);
        $query = 'DELETE FROM Region WHERE RegionId="' . $prmRegionId . '"';
        $pdo = $core->query($query);
        $query = 'DELETE FROM RegionAuth WHERE RegionId="' . $prmRegionId . '"';
        $pdo = $core->query($query);
        return $answer;
    }

    public static function create($session, $prmRegionId, $prmRegionInfo)
    {
        $iReturn = ERR_NO_ERROR;
        if ($iReturn > 0) {
            $RegionId = $prmRegionId;
            $region = new DIRegionRecord($session, $RegionId);
            $iReturn = $region->setFromArray($prmRegionInfo);
            if ($region->get('RegionId') == '') {
                $iReturn = ERR_UNKNOWN_ERROR;
            }
        }
        if ($iReturn > 0) {
            $RegionId = $region->get('RegionId');
            $iReturn = DIRegion::existRegion($session, $RegionId) > 0 ? ERR_UNKNOWN_ERROR : $region->insert();
        }
        if ($iReturn > 0) {
            // Set Role ADMINREGION in RegionAuth: master for this region
            $region->removeRegionUserAdmin();
            $RegionUserAdmin = $session->UserId;
            $iReturn = $session->setUserRole($RegionUserAdmin, $region->get('RegionId'), 'ADMINREGION');
        }
        if ($iReturn > 0) {
            $region2 = new DIRegionDB($session, $RegionId);
            $iReturn = $region2->createRegionDB();
        }
        return $iReturn;
    }

    public static function replace($session, $prmRegionId, $prmRegionLabel, $prmFilename, $tmpDir)
    {
        $iReturn = ERR_NO_ERROR;
        $RegionId = $prmRegionId;
        $RegionLabel = $prmRegionLabel;
        $OutDir = $tmpDir . '/' . $session->sSessionId;
        $filename = $OutDir . '/' . $prmFilename;
        if (! file_exists($filename)) {
            $iReturn = ERR_DEFAULT_ERROR;
        }

        if ($iReturn > 0) {
            // Open ZIP File, extract all files and return values...
            $zip = new ZipArchive();
            $res = $zip->open($filename);
            if ($res == false) {
                $iReturn = ERR_UNKNOWN_ERROR;
            }
            if ($iReturn > 0) {
                $DBDir = $session->getDBDir();
                $zip->extractTo($DBDir);
                $zip->close();

                $region = new DIRegion($session, $RegionId);
                $region->set('RegionId', $RegionId);
                if ($RegionLabel != '') {
                    $region->set('RegionLabel', $RegionLabel);
                }
                $region->update();
            }
        }
        if (file_exists($filename)) {
            unlink($filename);
        }
        return $iReturn;
    }
}
