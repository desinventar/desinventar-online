<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
namespace DesInventar\Legacy;

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

    public static function create($us, $prmRegionId, $prmRegionInfo)
    {
        $iReturn = ERR_NO_ERROR;
        if ($iReturn > 0) {
            $RegionId = $prmRegionId;
            $r = new DIRegionRecord($us, $RegionId);
            $iReturn = $r->setFromArray($prmRegionInfo);
            if ($r->get('RegionId') == '') {
                $iReturn = ERR_UNKNOWN_ERROR;
            }
        }
        if ($iReturn > 0) {
            $RegionId = $r->get('RegionId');
            if (DIRegion::existRegion($us, $RegionId) > 0) {
                # Database already exists
                $iReturn = ERR_UNKNOWN_ERROR;
            } else {
                $iReturn = $r->insert();
            }
        }
        if ($iReturn > 0) {
            # Set Role ADMINREGION in RegionAuth: master for this region
            $r->removeRegionUserAdmin();
            $RegionUserAdmin = $us->UserId;
            $iReturn = $us->setUserRole($RegionUserAdmin, $r->get('RegionId'), 'ADMINREGION');
        }
        if ($iReturn > 0) {
            $r2 = new DIRegionDB($us, $RegionId);
            $iReturn = $r2->createRegionDB();
        }
        return $iReturn;
    }

    public static function replace($us, $prmRegionId, $prmRegionLabel, $prmFilename)
    {
        $iReturn = ERR_NO_ERROR;
        $RegionId = $prmRegionId;
        $RegionLabel = $prmRegionLabel;
        $OutDir = TMP_DIR . '/' . $us->sSessionId;
        $filename = $OutDir . '/' . $prmFilename;
        if (! file_exists($filename)) {
            $iReturn = ERR_DEFAULT_ERROR;
        }

        if ($iReturn > 0) {
            # Open ZIP File, extract all files and return values...
            $zip = new ZipArchive();
            $res = $zip->open($filename);
            if ($res == false) {
                $iReturn = ERR_UNKNOWN_ERROR;
            }
            if ($iReturn > 0) {
                $DBDir = $us->getDBDir();
                $zip->extractTo($DBDir);
                $zip->close();

                $r = new DIRegion($us, $RegionId);
                $r->set('RegionId', $RegionId);
                if ($RegionLabel != '') {
                    $r->set('RegionLabel', $RegionLabel);
                }
                $r->update();
            }
        }
        if (file_exists($filename)) {
            unlink($filename);
        }
        return $iReturn;
    }
}
