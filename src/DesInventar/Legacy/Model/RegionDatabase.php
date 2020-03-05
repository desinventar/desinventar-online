<?php
namespace DesInventar\Legacy\Model;

use Aura\Sql\ExtendedPdo;

use DesInventar\Legacy\Model\Region;
use DesInventar\Legacy\Model\GeographyLevel;
use DesInventar\Legacy\Model\GeographyCarto;
use DesInventar\Legacy\Model\GeographyItem;
use DesInventar\Legacy\Model\RegionItem;
use DesInventar\Legacy\Model\Sync;
use DesInventar\Legacy\Model\Cause;
use DesInventar\Legacy\Model\Event;

use \PDO;
use \PDOException;
use \Exception;
use \ZipArchive;

class RegionDatabase extends Region
{
    const ERR_NO_ERROR = 1;
    const ERR_UNKNOWN_ERROR = -1;
    const ERR_FILE_NOT_FOUND = -9;

    protected $conn = null;
    protected $databaseDir = '';

    public function __construct($prmSession, $prmRegionId)
    {
        parent::__construct($prmSession, $prmRegionId);
        $this->conn = $this->doOpenDatabase($prmRegionId);
        $this->databaseDir = $this->session->config->database['db_dir'];
    }

    public function doOpenDatabase($prmRegionId, $prmDBFile = '')
    {
        $conn = null;
        $DBFile = $this->databaseDir;
        if ($prmRegionId == 'core') {
            $DBFile .= '/main/core.db';
        } else {
            if ($prmDBFile == '') {
                $DBFile .= '/database/' . $prmRegionId .'/desinventar.db';
            } else {
                $DBFile = $prmDBFile;
            }
        }
        if (file_exists($DBFile)) {
            try {
                $conn = new ExtendedPdo('sqlite:' . $DBFile);
                // set the error reporting attribute
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn->setAttribute(PDO::ATTR_TIMEOUT, 5.0);
            } catch (PDOException $e) {
                $conn = null;
            }
        }
        return $conn;
    }

    public function rebuildRegionData()
    {
        //$this->rebuildEventData();
        //$this->rebuildCauseData();
        //$this->rebuildGeoLevelData();
        $this->rebuildGeographyData();
        $this->rebuildGeoCartoData();
        $this->rebuildDisasterData();
    }

    public function rebuildGeoCartoData()
    {
        $iReturn = self::ERR_NO_ERROR;
        $query = "SELECT * FROM Sync WHERE SyncTable='GeoCarto'";
        $list = array();
        foreach ($this->conn->query($query) as $row) {
            $list[] = $row['SyncURL'];
        }

        foreach ($list as $SyncURL) {
            $url = $this->processURL($SyncURL);
            $RegionItemId = $url['regionid'];
            $prmRegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

            $query = "DELETE FROM GeoCarto WHERE GeographyId='" . $prmRegionItemGeographyId . "'";
            $this->conn->query($query);

            // Attach Database
            $this->conn->query($this->attachQuery($RegionItemId, 'RegItem'));

            $r2 = new Region($this->session, $RegionItemId);
            $CountryIso2 = $r2->get('CountryIso');

            // Copy GeoCarto Items
            $this->copyData($this->conn, 'GeoCarto', 'GeographyId', $RegionItemId, $prmRegionItemGeographyId, false);

            $sQuery = '
            UPDATE GeoCarto
            SET GeoLevelLayerFile="' . $CountryIso2 . '_"||GeoLevelLayerFile
            WHERE RegionId="' . $RegionItemId . '"';
            $this->conn->query($sQuery);

            // Copy SHP,SHX,DBF files from each RegionItem to Region
            $RegionDir     = $this->getRegionDatabaseDir($this->get('RegionId'));
            $RegionItemDir = $this->getRegionDatabaseDir($RegionItemId);
            foreach ($this->conn->query('SELECT * FROM RegItem.GeoCarto') as $row) {
                foreach (array('dbf','shp','shx','prj') as $ext) {
                    $file0 = $row['GeoLevelLayerFile'] . '.' . $ext;
                    $file1 = $RegionItemDir . '/' . $file0;
                    $file2 = $RegionDir . '/' . $CountryIso2 . '_' . $file0;
                    if (file_exists($file1)) {
                        copy($file1, $file2);
                    }
                }
            }

            $g = new GeographyCarto($this->session, 0);
            $g->set('GeographyId', $prmRegionItemGeographyId);
            $g->set('RegionId', $RegionItemId);
            $g->insert();

            $this->conn->query($this->detachQuery('RegItem'));
        }

        // Fix GeographyId for items with too many detail
        $MaxGeoLevel = 100;
        $sQuery = 'SELECT COUNT(*) AS C FROM GeoLevel';
        foreach ($this->conn->query($sQuery) as $row) {
            $MaxGeoLevel = $row['C'];
        }
        $sQuery = 'DELETE FROM GeoCarto WHERE GeoLevelId >=' . $MaxGeoLevel;
        $this->conn->query($sQuery);
        return $iReturn;
    }


    public function rebuildDisasterData($prmRegionItemId = '')
    {
        $iReturn = self::ERR_NO_ERROR;
        $query = "SELECT * FROM Sync WHERE SyncTable='Disaster'";
        if ($prmRegionItemId != '') {
            $query .= "AND SyncURL LIKE '%" . $prmRegionItemId . "%'";
        }
        $list = array();
        foreach ($this->conn->query($query) as $row) {
            $list[] = $row['SyncURL'];
        }

        $query = "DELETE FROM Disaster";
        $this->conn->query($query);
        $query = "DELETE FROM EEData";
        $this->conn->query($query);

        foreach ($list as $SyncURL) {
            $url = $this->processURL($SyncURL);
            $RegionItemId = $url['regionid'];
            $RegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

            // Attach Database
            $this->conn->query($this->attachQuery($RegionItemId, 'RegItem'));

            // Copy Disaster Table, adjust GeographyId Field
            $this->copyData($this->conn, 'Disaster', 'GeographyId', $RegionItemId, $RegionItemGeographyId, false);

            // Delete Non Published Data cards
            //$this->conn->query("DELETE FROM Disaster WHERE RecordStatus<>'PUBLISHED'");

            // Copy DisasterId from EEData, Other Fields are Ignored...
            $sQuery = "
            INSERT INTO EEData (DisasterId)
                SELECT DisasterId
                FROM Disaster
                WHERE GeographyId LIKE '" . $RegionItemGeographyId . "%'";
            $sth = $this->conn->prepare($sQuery);
            try {
                $this->conn->beginTransaction();
                $sth->execute();
                $this->conn->commit();
            } catch (Exception $e) {
                $this->conn->rollBack();
            }
            $this->conn->query($this->detachQuery('RegItem'));
        }

        // Fix GeographyId for items with too many detail
        $MaxGeoLevel = 100;
        $sQuery = 'SELECT COUNT(*) AS C FROM GeoLevel';
        foreach ($this->conn->query($sQuery) as $row) {
            $MaxGeoLevel = $row['C'];
        }
        $sQuery = 'UPDATE Disaster SET GeographyId=SUBSTR(GeographyId,1,' . $MaxGeoLevel*5 . ')';
        $this->conn->query($sQuery);
    }

    public function rebuildGeographyData()
    {
        $iReturn = self::ERR_NO_ERROR;

        // Delete existing Geography except for Level0 in Virtual Region
        $query = "DELETE FROM Geography WHERE GeographyLevel>0";
        $this->conn->query($query);

        $list = array();
        $query = "SELECT * FROM Sync WHERE SyncTable='Geography'";
        foreach ($this->conn->query($query) as $row) {
            $list[] = $row['SyncURL'];
        }

        foreach ($list as $SyncURL) {
            $url = $this->processURL($SyncURL);
            $RegionItemId = $url['regionid'];
            $prmRegionItemId = $RegionItemId;
            $prmRegionItemGeographyId = $this->getRegionItemGeographyId($RegionItemId);

            // Attach Database
            $this->conn->query($this->attachQuery($RegionItemId, 'RegItem'));

            // Copy Geography From Database
            $this->copyData(
                $this->conn,
                'Geography',
                'GeographyId',
                $prmRegionItemId,
                $prmRegionItemGeographyId,
                false
            );

            // Update GeographyFQName in child nodes
            $g = new GeographyItem($this->session, $prmRegionItemGeographyId);
            $GeographyFQName = $g->get('GeographyFQName');
            $query = '
            UPDATE Geography
            SET GeographyFQName="' . $GeographyFQName . '/' . '"||GeographyFQName
            WHERE GeographyLevel>0 AND GeographyId LIKE "' . $prmRegionItemGeographyId . '%"';
            $this->conn->query($query);

            $this->conn->query($this->detachQuery('RegItem'));
        }

        $MaxGeoLevel = 100;
        $sQuery = 'SELECT COUNT(*) AS C FROM GeoLevel';
        foreach ($this->conn->query($sQuery) as $row) {
            $MaxGeoLevel = $row['C'];
        }
        $sQuery = 'DELETE FROM Geography WHERE GeographyLevel>=' . $MaxGeoLevel;
        $this->conn->query($sQuery);
        return $iReturn;
    }

    public function processURL($prmURL)
    {
        $url = ['protocol' => '', 'host' => '', 'regionid' => ''];
        $indexA = strpos($prmURL, '://');
        if ($indexA === false) {
            return $url;
        }
        $url['protocol'] = substr($prmURL, 0, $indexA);
        $indexB = strpos($prmURL, '/', $indexA + 3);
        $url['host'] = substr($prmURL, $indexA + 3, $indexB - $indexA - 3);
        $url['regionid'] = substr($prmURL, $indexB + 1);
        return $url;
    }

    public function attachQuery($prmRegionId, $prmName)
    {
        $RegionItemDir = $this->getRegionDatabaseDir($prmRegionId);
        $RegionItemDB = $RegionItemDir . '/desinventar.db';
        $query = "ATTACH DATABASE '" . $RegionItemDB . "' AS " . $prmName;
        return $query;
    }

    public function detachQuery($prmName)
    {
        $query = "DETACH DATABASE " . $prmName;
        return $query;
    }

    public function copyData($prmConn, $prmTable, $prmField, $prmRegionItemId, $prmValue, $isNumeric)
    {
        $Queries = array();
        // Create Empty Table
        $Query = "DROP TABLE IF EXISTS TmpTable";
        array_push($Queries, $Query);
        $Query = "CREATE TABLE TmpTable AS SELECT * FROM " . $prmTable . " LIMIT 0";
        array_push($Queries, $Query);

        $endLoop = 1;
        if ($prmTable == 'Geography') {
            $endLoop = 100;
        }

        for ($i = 0; $i<$endLoop; $i++) {
            $Query = 'DELETE FROM TmpTable';
            array_push($Queries, $Query);

            $Query = "INSERT INTO TmpTable SELECT * FROM RegItem." . $prmTable;
            if ($prmTable == 'Geography') {
                $gId = self::padNumber($i, 5);
                $Query .= ' WHERE GeographyId LIKE "' . $gId . '%"';
            }
            array_push($Queries, $Query);
            if ($isNumeric) {
                $Query = "UPDATE TmpTable SET " . $prmField . "=" . $prmField . "+1";
            } else {
                $Query = "UPDATE TmpTable SET " . $prmField . "='" . $prmValue . "'||" . $prmField;
            }
            array_push($Queries, $Query);
            if ($prmTable == 'Geography') {
                $Query = "UPDATE TmpTable SET GeographyLevel=GeographyLevel+1";
                array_push($Queries, $Query);
            }
            if ($prmTable == 'GeoCarto') {
                $Query = "UPDATE TmpTable SET GeoLevelId=GeoLevelId+1";
                array_push($Queries, $Query);
                $Query = "UPDATE TmpTable SET RegionId='" . $prmRegionItemId . "'";
                array_push($Queries, $Query);
            }

            if ($isNumeric) {
                $Query = "DELETE FROM " . $prmTable . " WHERE " . $prmField . "=" . ((int)$prmValue + 1);
            } else {
                $Query = "DELETE FROM " . $prmTable . " WHERE " . $prmField . " LIKE '" . $prmValue . "%'";
            }
            $Query = "INSERT INTO " . $prmTable . " SELECT * FROM TmpTable";
            array_push($Queries, $Query);
        }
        $Query = "DROP TABLE IF EXISTS TmpTable";
        foreach ($Queries as $Query) {
            $prmConn->query($Query);
        }
    }


    public function addRegionItem($prmRegionItemId, $prmRegionItemGeographyName, $prmRegionItemGeographyId = '')
    {
        $iReturn = self::ERR_NO_ERROR;
        $RegionId = $this->get('RegionId');
        if ($prmRegionItemGeographyId == '') {
            $prmRegionItemGeographyId = $this->getRegionItemGeographyId($prmRegionItemId);
        }

        $this->addRegionItemSync($prmRegionItemId);

        // Create Geography element at GeographyLevel=0 for this RegionItem
        // Delete Existing Elements
        $query = 'DELETE FROM Geography WHERE GeographyCode=:GeographyCode';
        $sth = $this->conn->prepare($query);
        try {
            $this->conn->beginTransaction();
            $sth->bindParam(':GeographyCode', $prmRegionItemId, PDO::PARAM_STR);
            $sth->execute();
            $this->conn->commit();
            $iReturn = self::ERR_NO_ERROR;
        } catch (Exception $e) {
            $this->conn->rollBack();
            $iReturn = self::ERR_UNKNOWN_ERROR;
        }
        if ($iReturn > 0) {
            $g = new GeographyItem(
                $this->session,
                $prmRegionItemGeographyId,
                $this->get('LangIsoCode')
            );
            $g->set('GeographyCode', $prmRegionItemId);
            $g->set('GeographyName', $prmRegionItemGeographyName);
            $iReturn = $g->insert();
        }
        return $iReturn;
    }

    public function getRegionItemGeographyId($prmRegionId)
    {
        $GeographyId = '';
        $g = GeographyItem::loadByCode($this->session, $prmRegionId);
        if ($g != null) {
            $GeographyId = $g->get('GeographyId');
        }
        if ($GeographyId == '') {
            $GeographyId = $g->buildGeographyId('');
        }
        return $GeographyId;
    }

    public static function getRegionTables()
    {
        $RegionTables = array('Event','Cause','GeoLevel',
                              'GeoCarto','Geography','Disaster',
                              'EEData','EEField','EEGroup');
        return $RegionTables;
    }

    public function clearRegionTables()
    {
        // Delete ALL Record from Database - Be Careful...
        foreach ($this->getRegionTables() as $TableName) {
            $query = 'DELETE FROM ' . $TableName;
            $sth = $this->conn->prepare($query);
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
        }
    }

    public function addRegionItemSync($prmRegionItemId)
    {
        foreach ($this->getRegionTables() as $TableName) {
            $s = new Sync($this->session);
            $s->set('SyncTable', $TableName);
            $s->set('RegionId', $this->get('RegionId'));
            $s->set('SyncURL', 'file:///' . $prmRegionItemId);
            $s->insert();
        }
    }

    public function clearSyncTable()
    {
        $sth = $this->conn->prepare('DELETE FROM Sync;');
        $this->conn->beginTransaction();
        $sth->execute();
        $this->conn->commit();
    }

    public function clearGeoLevelTable()
    {
        $sth = $this->conn->prepare('DELETE FROM GeoLevel;');
        $this->conn->beginTransaction();
        $sth->execute();
        $this->conn->commit();
    }

    public function clearGeographyTable()
    {
        $sth = $this->conn->prepare('DELETE FROM Geography;');
        $this->conn->beginTransaction();
        $sth->execute();
        $this->conn->commit();
    }

    public function clearGeoCartoTable()
    {
        $sth = $this->conn->prepare('DELETE FROM Geography;');
        $this->conn->beginTransaction();
        $sth->execute();
        $this->conn->commit();
    }

    public function createGeoLevel($prmGeoLevelId, $prmGeoLevelName)
    {
        $g = new GeographyLevel($this->session, $prmGeoLevelId);
        $g->set('GeoLevelName', $prmGeoLevelName);
        $g->set('RegionId', $this->get('RegionId'));
        if ($g->exist() > 0) {
            $g->update();
        } else {
            $g->insert();
        }
    }

    public function createCause($prmCauseId, $prmCauseName)
    {
        $o = new Cause($this->session, $prmCauseId, $prmCauseName);
        $o->set('CausePredefined', 0);
        if ($o->exist() > 0) {
            $o->update();
        } else {
            $o->insert();
        }
    }

    public function createEvent($prmEventId, $prmEventName)
    {
        $o = new Event($this->session, $prmEventId, $prmEventName);
        $o->set('EventPredefined', 0);
        if ($o->exist() > 0) {
            $o->update();
        } else {
            $o->insert();
        }
    }

    public static function createRegionDBFromZip($us, $mode, $prmRegionId, $prmRegionLabel, $prmZipFile)
    {
        $iReturn = self::ERR_NO_ERROR;
        $databaseDir = $us->config->database['db_dir'] . '/database/';

        // Open zip file and extract files
        $zip = new ZipArchive();
        $res = $zip->open($prmZipFile);
        if (!$res) {
            return self::ERR_UNKNOWN_ERROR;
        }

        $OutDir = $databaseDir . '/' . $prmRegionId;
        if ($mode == 'NEW') {
            // Create directory for new database
            if (! mkdir($databaseDir . '/' . $prmRegionId, 0755)) {
                return self::ERR_UNKNOWN_ERROR;
            }
        }

        // Extract contents of zipfile
        $zip->extractTo($OutDir);
        $zip->close();

        //Create/update info.xml and core.Region data...
        if ($mode == 'NEW') {
            $dbexist = Region::existRegion($us, $prmRegionId);
            if ($dbexist > 0) {
                // RegionId already exists, cannot create db
                return self::ERR_UNKNOWN_ERROR;
            }
            $r = new Region($us, $prmRegionId, $OutDir . '/info.xml');
            $r->set('RegionId', $prmRegionId);
            $r->set('RegionLabel', $prmRegionLabel);
            $us->open($prmRegionId);
            if ($r->insert() < 0) {
                Region::deleteRegion($us, $prmRegionId);
                rmdir($OutDir);
                return self::ERR_UNKNOWN_ERROR;
            }
        }

        return self::ERR_NO_ERROR;
    }

    public function createRegionDB($prmGeoLevelName = '')
    {
        // Creates/Initialize the region database
        $iReturn = self::ERR_NO_ERROR;
        $prmRegionId = $this->get('RegionId');
        $templateDatabase = $this->databaseDir . '/main/desinventar.db';

        // Create Directory for New Region if do not already exists
        $DBDir = $this->getRegionDatabaseDir($prmRegionId);
        $DBFile = $DBDir . '/desinventar.db';
        $this->conn = null;
        $this->createRegionDBDir();
        if (file_exists($templateDatabase)) {
            // Backup previous desinventar.db if exists
            if (file_exists($DBFile)) {
                $DBFile2 = $DBFile . '.bak';
                if (file_exists($DBFile2)) {
                    unlink($DBFile2);
                }
                rename($DBFile, $DBFile2);
            }
            if (! copy($templateDatabase, $DBFile)) {
                $iReturn = self::ERR_UNKNOWN_ERROR;
            }
        }

        $this->conn = $this->doOpenDatabase($this->get('RegionId'));
        $this->session->q->setDBConnection($this->get('RegionId'));
        // Delete all database records
        $this->clearRegionTables();
        if ($iReturn > 0) {
            // Copy Predefined Event/Cause Lists
            $LangIsoCode = $this->get('LangIsoCode');
            $this->copyEvents($LangIsoCode);
            $this->copyCauses($LangIsoCode);
        }
        if ($iReturn > 0) {
            // Calculate Name of GeoLevel 0
            if ($prmGeoLevelName != '') {
                $this->session->open($this->get('RegionId'));
                $g = new GeographyLevel($this->session, 0);
                $g->set('GeoLevelName', $prmGeoLevelName);
                $g->set('RegionId', $this->get('RegionId'));
                $c = new GeographyCarto($this->session);
                $c->set('GeoLevelId', 0);
                if ($g->exist() > 0) {
                    $g->update();
                    $c->update();
                } else {
                    $g->insert();
                    $c->insert();
                }
            }
        }
        return $iReturn;
    }

    public function addPredefinedItemSync()
    {
        // Sync record for Predefined Events
        $s = new Sync($this->session);
        $s->set('SyncTable', 'Event');
        $s->set('RegionId', $this->get('RegionId'));
        $s->set('SyncURL', 'file:///base');
        $s->set('SyncSpec', '');
        $s->insert();

        // Sync record for Predefined Cause
        $s = new Sync($this->session);
        $s->set('SyncTable', 'Cause');
        $s->set('RegionId', $this->get('RegionId'));
        $s->set('SyncURL', 'file:///base');
        $s->set('SyncSpec', '');
        $s->insert();
    }

    public function addRegionItem2($prmRegionItemId, $prmRegionItemGeographyName, $prmRegionItemGeographyId = '')
    {
        $RegionId = $this->get('RegionId');
        $RegionDir = $this->getRegionDatabaseDir($this->get('RegionId'));
        $RegionItemDir = $this->getRegionDatabaseDir($prmRegionItemId);
        $RegionItemDB = $RegionItemDir . '/desinventar.db';

        if ($prmRegionItemGeographyId == '') {
            $prmRegionItemGeographyId = $this->getRegionItemGeographyId($prmRegionItemId);
        }
        $iReturn = self::ERR_NO_ERROR;
        if ($prmRegionItemId == '') {
            $iReturn = self::ERR_UNKNOWN_ERROR;
        }
        if ($iReturn > 0) {
            if (!file_exists($RegionItemDB)) {
                $iReturn = self::ERR_FILE_NOT_FOUND;
            }
        }
        if ($iReturn > 0) {
            // Add RegionItem record
            $iReturn = $this->addRegionItemRecord($prmRegionItemId);
        }
        if ($iReturn > 0) {
            // Add Geography to Level0
            if ($prmRegionItemGeographyId == '') {
                $prmRegionItemGeographyId = $this->getRegionItemGeographyId($prmRegionItemId);
            }
            $iReturn = $this->addRegionItemGeography($prmRegionItemId, $prmRegionItemGeographyId);
        }
        if ($iReturn > 0) {
            //$iReturn = $this->addRegionItemDisaster($prmRegionItemId,$prmRegionItemGeographyId);
        }
        if ($iReturn > 0) {
            if ($prmRegionItemGeographyName != '') {
                $Query = '
                UPDATE Geography
                SET GeographyName=:GeographyName
                WHERE GeographyLevel=0 AND GeographyCode=:GeographyCode';
                $sth = $this->conn->prepare($Query);
                try {
                    $this->conn->beginTransaction();
                    $sth->bindParam(':GeographyName', $prmRegionItemGeographyName, PDO::PARAM_STR);
                    $sth->bindParam(':GeographyCode', $prmRegionItemId, PDO::PARAM_STR);
                    $sth->execute();
                    $this->conn->commit();
                } catch (Exception $e) {
                    $this->conn->rollBack();
                }
            }
        }
        return $iReturn;
    }

    public function addRegionItemRecord($prmRegionItemId)
    {
        $iReturn = self::ERR_NO_ERROR;
        $RegionId = $this->get('RegionId');
        // Add RegionItem record
        $i = new RegionItem($this->session, $RegionId, $prmRegionItemId);
        $iReturn = $i->insert();
        return $iReturn;
    }

    public function addRegionItemGeography($prmRegionItemId, $prmRegionItemGeographyId)
    {
        $q = $this->conn;

        // Copy Geography From Database
        $RegionItemDir = $this->getRegionDatabaseDir($prmRegionItemId);
        $RegionItemDB = $RegionItemDir . '/desinventar.db';
        // Attach Database
        $q->query($this->attachQuery($prmRegionItemId, 'RegItem'));
        $this->copyData($q, 'Geography', 'GeographyId', $prmRegionItemId, $prmRegionItemGeographyId, false);
        $q->query($this->detachQuery('RegItem'));
        return self::ERR_NO_ERROR;
    }

    public function rebuildEventData()
    {
        $this->copyEvents($this->get('LangIsoCode'));
        $this->conn->query("DELETE FROM Event WHERE EventPredefined=0");
        $o = new Event($this->session);
        $query = "SELECT * FROM Sync WHERE SyncTable='Event'";
        foreach ($this->conn->query($query) as $row) {
            $url = $this->processURL($row['SyncURL']);
            $RegionItemId = $url['regionid'];
            $this->conn->query($this->attachQuery($RegionItemId, 'RegItem'));
            foreach ($this->conn->query("SELECT * FROM RegItem.Event WHERE EventPredefined=0") as $row) {
                $o->setFromArray($row);
                $o->insert();
            }
            $this->conn->query($this->detachQuery('RegItem'));
        }
    }

    public function rebuildCauseData()
    {
        $this->copyCauses($this->get('LangIsoCode'));
        $this->conn->query("DELETE FROM Cause WHERE CausePredefined=0");
        $o = new Cause($this->session);
        $query = "SELECT * FROM Sync WHERE SyncTable='Cause'";
        foreach ($this->conn->query($query) as $row) {
            $url = $this->processURL($row['SyncURL']);
            $RegionItemId = $url['regionid'];
            $this->conn->query($this->attachQuery($RegionItemId, 'RegItem'));
            foreach ($this->conn->query("SELECT * FROM RegItem.Cause WHERE CausePredefined=0") as $row) {
                $o->setFromArray($row);
                $o->insert();
            }
            $this->conn->query($this->detachQuery('RegItem'));
        }
    }

    public function rebuildGeoLevelData()
    {
        $iReturn = self::ERR_NO_ERROR;
        $this->conn->query("DELETE FROM GeoLevel WHERE GeoLevelId>0");
        $query = "SELECT * FROM Sync WHERE SyncTable='Cause'";
        foreach ($this->conn->query($query) as $row) {
            $url = $this->processURL($row['SyncURL']);
            $RegionItemId = $url['regionid'];
            $this->conn->query($this->attachQuery($RegionItemId, 'RegItem'));

            // GetCurrentMaxLevel
            $iMaxLevel = 0;
            foreach ($this->conn->query('SELECT MAX(GeoLevelId) AS MAXVAL FROM GeoLevel') as $row) {
                $iMaxLevel = $row['MAXVAL'];
            }
            foreach ($this->conn->query('SELECT * FROM RegItem.GeoLevel') as $row) {
                if ($iReturn > 0) {
                    if (($row['GeoLevelId'] + 1) > $iMaxLevel) {
                        $iMaxLevel++;
                        $g = new GeographyLevel($this->session, $iMaxLevel);
                        $g->set('GeoLevelName', 'Nivel ' . $iMaxLevel);
                        $iReturn = $g->insert();
                    }
                }
            }
            $this->conn->query($this->detachQuery('RegItem'));
        }
        return $iReturn;
    }

    protected function getBaseDatabase()
    {
        return $this->databaseDir . '/main/base.db';
    }

    public function copyEvents($prmLangIsoCode = '')
    {
        $baseDatabase = $this->getBaseDatabase();

        if ($prmLangIsoCode == '') {
            $prmLangIsoCode = $this->get('LangIsoCode');
        }
        $e = new Event($this->session);
        $FieldList = $e->getFieldList();
        $Queries = array();
        $Query = "ATTACH DATABASE '" . $baseDatabase . "' AS base";
        array_push($Queries, $Query);
        //Copy Predefined Event List Into Database
        $Query = "DELETE FROM Event WHERE EventPredefined=1 AND LangIsoCode='" . $prmLangIsoCode . "'";
        array_push($Queries, $Query);
        $Query = "
            INSERT INTO Event(" . $FieldList . ")
                SELECT " . $FieldList . "
                FROM base.Event WHERE LangIsoCode='" . $prmLangIsoCode . "'";
        array_push($Queries, $Query);
        $Query = 'DETACH DATABASE base';
        array_push($Queries, $Query);
        foreach ($Queries as $Query) {
            $this->conn->query($Query);
        }
    }

    public function copyCauses($prmLangIsoCode = '')
    {
        $baseDatabase = $this->getBaseDatabase();

        if ($prmLangIsoCode == '') {
            $prmLangIsoCode = $this->get('LangIsoCode');
        }
        $c = new Cause($this->session);
        $FieldList = $c->getFieldList();
        $Queries = array();
        $Query = "ATTACH DATABASE '" . $baseDatabase . "' AS base";
        array_push($Queries, $Query);
        //Copy Predefined Cause List Into Database
        $Query = "DELETE FROM Cause WHERE CausePredefined=1 AND LangIsoCode='" . $prmLangIsoCode . "'";
        array_push($Queries, $Query);
        $Query = "
            INSERT INTO Cause (" . $FieldList . ")
                SELECT " . $FieldList . "
                FROM base.Cause WHERE LangIsoCode='" . $prmLangIsoCode . "'";
        array_push($Queries, $Query);
        $Query = 'DETACH DATABASE base';
        array_push($Queries, $Query);
        foreach ($Queries as $Query) {
            $this->conn->query($Query);
        }
    }

    public function createCRegion($prmGeoLevelName)
    {
        // Set Information about this CRegion, Creates GeoLevel=0
        $iReturn = self::ERR_NO_ERROR;
        $this->set('IsCRegion', 1);
        $g = new GeographyLevel($this->session, 0, $this->get('LangIsoCode'), $prmGeoLevelName);
        // Warning : Delete All GeoLevels with this...
        $g->getQuery()->query("DELETE FROM GeoLevel");
        $iReturn = $g->insert();
        return $iReturn;
    }

    public function getGeoLevelCount()
    {
        $iAnswer = 0;
        $g = new GeographyLevel($this->session, 0);
        $iAnswer = $g->getMaxGeoLevel();
        return $iAnswer;
    }
}
