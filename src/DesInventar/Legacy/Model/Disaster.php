<?php
/*
 * DesInventar - http://www.desinventar.org
 * (c) Corporacion OSSO
 */
namespace DesInventar\Legacy\Model;

use DesInventar\Common\Date;
use DesInventar\Common\Util;

class Disaster extends Record
{
    const WARNING = 1;
    const EFFECT_SECTOR = 'SectorTransport/SECTOR,SectorCommunications/SECTOR,' .
        'SectorRelief/SECTOR,SectorAgricultural/SECTOR,SectorWaterSupply/SECTOR,' .
        'SectorSewerage/SECTOR,SectorEducation/SECTOR,SectorPower/SECTOR,' .
        'SectorIndustry/SECTOR,SectorHealth/SECTOR,SectorOther/SECTOR';
    protected $sEffectDef1 =
        'EffectPeopleDead/INTEGER,' .
        'EffectPeopleMissing/INTEGER,' .
        'EffectPeopleInjured/INTEGER,' .
        'EffectPeopleHarmed/INTEGER,' .
        'EffectPeopleAffected/INTEGER,' .
        'EffectPeopleEvacuated/INTEGER,' .
        'EffectPeopleRelocated/INTEGER,' .
        'EffectHousesDestroyed/INTEGER,' .
        'EffectHousesAffected/INTEGER';
    protected $sEffectDef2 =
        'EffectLossesValueLocal/DOUBLE,' .
        'EffectLossesValueUSD/DOUBLE,' .
        'EffectRoads/DOUBLE,' .
        'EffectFarmingAndForest/DOUBLE,' .
        'EffectLiveStock/INTEGER,' .
        'EffectEducationCenters/INTEGER,' .
        'EffectMedicalCenters/INTEGER,' .
        'EffectOtherLosses/STRING,' .
        'EffectNotes/STRING';
    protected $sFieldQDef =
        'EffectPeopleDeadQ/INTEGER,' .
        'EffectPeopleMissingQ/INTEGER,' .
        'EffectPeopleInjuredQ/INTEGER,' .
        'EffectPeopleHarmedQ/INTEGER,' .
        'EffectPeopleAffectedQ/INTEGER,' .
        'EffectPeopleEvacuatedQ/INTEGER,' .
        'EffectPeopleRelocatedQ/INTEGER,' .
        'EffectHousesDestroyedQ/INTEGER,' .
        'EffectHousesAffectedQ/INTEGER';
    protected $sEEFieldDef = '';
    protected $sEffectDef = '';
    protected $EEFieldCount = 0;

    public function __construct($prmSession, $prmDisasterId)
    {
        $this->sTableName   = 'Disaster';
        $this->sPermPrefix  = 'DISASTER';
        $this->sFieldKeyDef = 'DisasterId/STRING';
        $this->sFieldDef    =
            'RegionId/STRING,' .
            'DisasterSerial/STRING,' .
            'DisasterBeginTime/STRING,' .
            'GeographyId/STRING,' .
            'DisasterSiteNotes/STRING,' .
            'DisasterLatitude/DOUBLE,' .
            'DisasterLongitude/DOUBLE,' .
            'DisasterSource/STRING,' .

            'RecordStatus/STRING,' .
            'RecordAuthor/STRING,' .
            'RecordCreation/DATETIME,' .
            'RecordSync/DATETIME,' .
            'RecordUpdate/DATETIME,' .

            'EventId/STRING,' .
            'EventNotes/STRING,' .
            'EventDuration/INTEGER,' .
            'EventMagnitude/STRING,' .

            'CauseId/STRING,' .
            'CauseNotes/STRING';
        $this->sEffectDef = $this->sEffectDef1 . ',' . self::EFFECT_SECTOR . ',' . $this->sEffectDef2;
        $this->sFieldDef .= ',' . $this->sEffectDef;
        $this->sFieldDef .= ',' . $this->sFieldQDef;
        parent::__construct($prmSession);
        $this->sEEFieldDef  = $this->buildEEFieldDef();
        $this->EEFieldCount = count($this->explodeFieldList($this->sEEFieldDef));
        if ($this->sEEFieldDef != '') {
            $this->createFields($this->sFieldKeyDef);
            $this->createFields($this->sEEFieldDef);
        }
        $util = new Util();
        $this->set('EventPredefined', 0);
        $this->set('EventActive', 1);
        $this->set('DisasterId', $util->uuid4());
        $this->set('RecordStatus', 'PUBLISHED');

        if (!empty($prmDisasterId)) {
            $this->set('DisasterId', $prmDisasterId);
            $this->load();
        }
    }

    public function buildEEFieldDef()
    {
        $sFieldDef = '';
        $sQuery = 'SELECT * FROM EEField ORDER BY EEFieldOrder';
        $i = 0;
        foreach ($result = $this->q->dreg->query($sQuery) as $row) {
            if ($i > 0) {
                $sFieldDef .= ',';
            }
            $sFieldDef .= $row['EEFieldId'] . '/' . $row['EEFieldType'];
            $i++;
        }
        return $sFieldDef;
    }

    public function load()
    {
        $iReturn = parent::load();
        if ($iReturn > 0) {
            $iReturn = $this->loadRecord('EEData', $this->sEEFieldDef);
        }
        return $iReturn;
    }

    public function getDeleteQuery()
    {
        $sQuery = 'UPDATE ' . $this->getTableName() . ' SET RecordStatus="DELETED" ' .
            ' WHERE ' . $this->getWhereSubQuery();
        return $sQuery;
    }

    public function create($withValidate = 1, $bStrict = 1)
    {
        // First, create Disaster record
        $iReturn = parent::create($withValidate, $bStrict);
        if ($iReturn > 0) {
            // Create EEData Record
            $iReturn = $this->createRecord('EEData');
        }
        return $iReturn;
    }

    public function validateCreate($bStrict)
    {
        $iReturn = 1;
        $iReturn = $this->validateNotNull(-51, 'DisasterId');
        $iReturn = $this->validatePrimaryKey(-52);
        $iReturn = parent::validateCreate($bStrict);
        return $iReturn;
    }

    public function validateUpdate($bStrict)
    {
        $iReturn = parent::validateUpdate($bStrict);
        $iReturn = $this->validateNotNull(-53, 'DisasterSerial');
        if ($bStrict > 0) {
            $iReturn = $this->validateUnique(-54, 'DisasterSerial');
        } else {
            $iReturn = $this->validateUnique(-54, 'DisasterSerial', self::WARNING);
        }
        $iReturn = $this->validateNotNull(-55, 'DisasterBeginTime');
        $iReturn = $this->validateNotNull(-57, 'RecordStatus');
        $iReturn = $this->validateRef(-58, 'GeographyId', 'Geography', 'GeographyId');
        $iReturn = $this->validateRef(-59, 'EventId', 'Event', 'EventId');
        $iReturn = $this->validateRef(-60, 'CauseId', 'Cause', 'CauseId');
        // Warning
        $bStrict2 = $bStrict;
        if ($this->get('RecordStatus') != 'PUBLISHED') {
            $bStrict2 = false;
        }
        if ($bStrict2 > 0) {
            $iReturn = $this->validateNotNull(-56, 'DisasterSource');
            if ($iReturn > 0) {
                $iReturn = $this->validateEffects(-61, 1);
            }
            if ($iReturn > 0) {
                $iReturn = $this->validateDisasterBeginTime(-62);
            }
        }
        if (count($this->status->error)) {
            $keys = array_keys($this->status->error);
            $iReturn = reset($keys);
        }
        return $iReturn;
    }

    public function validateDisasterBeginTime($ErrCode)
    {
        $r = new Region($this->session, $this->RegionId);
        $PeriodBeginDate   = trim($r->get('PeriodBeginDate'));
        $PeriodEndDate     = trim($r->get('PeriodEndDate'));
        $DisasterBeginTime = $this->get('DisasterBeginTime');

        $iReturn = self::ERR_NO_ERROR;
        if ($iReturn > 0) {
            if ($PeriodBeginDate != '') {
                if ($DisasterBeginTime < $PeriodBeginDate) {
                    $iReturn = $ErrCode;
                }
            }
        }

        if ($iReturn > 0) {
            if ($PeriodEndDate != '') {
                $PeriodEndDate = Date::doCeil($PeriodEndDate);
                if ($DisasterBeginTime > $PeriodEndDate) {
                    $iReturn = $ErrCode;
                }
            }
        }
        return $iReturn;
    }

    public function validateEffects($ErrCode, $isError)
    {
        $iReturn = self::ERR_NO_ERROR;
        if ($this->get('RecordStatus') == 'PUBLISHED') {
            $bFound = -1;
            $sFieldList = $this->sEffectDef;
            if ($this->sEEFieldDef != '') {
                $sFieldList .= ',' . $this->sEEFieldDef;
            }
            foreach ($this->explodeFieldList($sFieldList) as $sField) {
                $oItem = preg_split('#/#', $sField);
                $sFieldName  = $oItem[0];
                $sFieldType  = $oItem[1];
                if ($sFieldName != 'EffectNotes') {
                    switch ($sFieldType) {
                        case 'STRING':
                        case 'DATE':
                            if (trim($this->get($sFieldName)) != '') {
                                $bFound = 1;
                            }
                            break;
                        case 'INTEGER':
                        case 'SECTOR':
                            if (($this->get($sFieldName) > 0) || ($this->get($sFieldName) == -1)) {
                                $bFound = 1;
                            }
                            break;
                        case 'CURRENCY':
                        case 'DOUBLE':
                            if ($this->get($sFieldName) > 0) {
                                $bFound = 1;
                            }
                            break;
                    }
                }
            }
            if ($bFound < 0) {
                $iReturn = $ErrCode;
                if ($isError > 0) {
                    $this->status->addMsg($iReturn, ' Datacard without effects', false);
                } else {
                    $this->status->addMsg($iReturn, ' Datacard without effects', true);
                }
            }
        }
        return $iReturn;
    }

    public function update($withValidate = 1, $bStrict = 1)
    {
        $iReturn = self::ERR_NO_ERROR;
        // Calculate Values of Q Fields...
        foreach ($this->explodeFieldList($this->sFieldQDef) as $sFieldQ) {
            $oItem = $this->explodeFieldDef($sFieldQ);
            $sFieldQName = $oItem[0];
            $sFieldName  = substr($sFieldQName, 0, -1);
            $this->set($sFieldQName, $this->get($sFieldName));
            if ($this->get($sFieldQName) < 0) {
                $this->set($sFieldQName, 0);
            }
        }
        // Update Disaster record
        $iReturn = parent::update($withValidate, $bStrict);
        if ($iReturn > 0) {
            if ($this->EEFieldCount > 0) {
                // Update EEData record
                $iReturn = $this->updateRecord('EEData', $this->sEEFieldDef);
            }
        }
        return $iReturn;
    }

    public function importFromCSV($cols, $values)
    {
        $iReturn = parent::importFromCSV($cols, $values);
        $DisasterImport = array(0 => 'DisasterId',
                                1 => 'DisasterSerial',
                                2 => 'DisasterBeginTime',
                                3 => 'GeographyId',
                                4 => 'DisasterSiteNotes',
                                5 => 'DisasterSource',
                                6 => 'DisasterLongitude',
                                7 => 'DisasterLatitude',
                                8 => 'RecordAuthor',
                                9 => 'RecordCreation',
                                10 => 'RecordStatus',
                                11 => 'EventId',
                                12 => 'EventDuration',
                                13 => 'EventMagnitude',
                                14 => 'EventNotes',
                                15 => 'CauseId',
                                16 => 'CauseNotes',
                                // Effects on People
                                17 => 'EffectPeopleDead',
                                18 => 'EffectPeopleMissing',
                                19 => 'EffectPeopleInjured',
                                20 => 'EffectPeopleHarmed',
                                21 => 'EffectPeopleAffected',
                                22 => 'EffectPeopleEvacuated',
                                23 => 'EffectPeopleRelocated',
                                // Effects on Houses
                                24 => 'EffectHousesDestroyed',
                                25 => 'EffectHousesAffected',
                                // Effects General
                                26 => 'EffectLossesValueLocal',
                                27 => 'EffectLossesValueUSD',
                                28 => 'EffectRoads',
                                29 => 'EffectFarmingAndForest',
                                30 => 'EffectLiveStock',
                                31 => 'EffectEducationCenters',
                                32 => 'EffectMedicalCenters',
                                // Other Losses
                                33 => 'EffectOtherLosses',
                                34 => 'EffectNotes',
                                // Sectors Affected
                                35 => 'SectorTransport',
                                36 => 'SectorCommunications',
                                37 => 'SectorRelief',
                                38 => 'SectorAgricultural',
                                39 => 'SectorWaterSupply',
                                40 => 'SectorSewerage',
                                41 => 'SectorEducation',
                                42 => 'SectorPower',
                                43 => 'SectorIndustry',
                                44 => 'SectorHealth',
                                45 => 'SectorOther'
                           );
        $this->set('EventId', 'OTHER');
        $this->set('CauseId', 'UNKNOWN');
        foreach ($DisasterImport as $Index => $Field) {
            $this->set($Field, $values[$Index]);
        }

        // Validation Settings
        $this->set('RecordStatus', 'PUBLISHED');
        if ($this->get('DisasterSource') == '') {
            $this->set('RecordStatus', 'DRAFT');
        }

        $DI6GeographyCode = $this->get('GeographyId');
        $GeographyId = GeographyItem::getIdByCode($this->session, $DI6GeographyCode);
        if ($GeographyId == '') {
            $GeographyId = $DI6GeographyCode;
        }
        $this->set('GeographyId', $GeographyId);

        $DI6EventId = $this->get('EventId');
        $EventId = Event::getIdByName($this->session, $DI6EventId);
        if ($EventId == '') {
            $EventId = $DI6EventId;
        }
        $this->set('EventId', $EventId);

        $DI6CauseId = $this->get('CauseId');
        $CauseId = Cause::getIdByName($this->session, $DI6CauseId);
        if ($CauseId == '') {
            $CauseId = $DI6CauseId;
        }
        $this->set('CauseId', $CauseId);

        //2009-07-25 Save fechapor/fechafec in EffectNotes
        $this->set(
            'EffectNotes',
            $this->get('EffectNotes') . ' ' .
            '(DI6Author : ' . $this->get('RecordAuthor') . ' ' .
            'DI6Date : ' . $this->get('RecordCreation') . ')'
        );
        $this->set('RecordAuthor', $this->session->UserId);
        $this->set('RecordCreation', gmdate('c'));

        return $iReturn;
    }

    public static function existId($prmSession, $prmDisasterId)
    {
        $iReturn = self::ERR_UNKNOWN_ERROR;
        $Query= 'SELECT * FROM Disaster WHERE DisasterId="' . $prmDisasterId . '"';
        foreach ($prmSession->q->dreg->query($Query) as $row) {
            $iReturn = self::ERR_NO_ERROR;
        }
        return $iReturn;
    }

    public function findIdBySerial($prmDisasterSerial)
    {
        $id = '';
        $Query= 'SELECT * FROM Disaster WHERE DisasterSerial="' . $prmDisasterSerial . '"';
        foreach ($this->session->q->dreg->query($Query) as $row) {
            $id = $row['DisasterId'];
            break;
        }
        return $id;
    }

    public static function getEffectSectorFields()
    {
        $list = array();
        foreach (explode(',', self::EFFECT_SECTOR) as $field) {
            $a = explode('/', trim($field));
            $list[] = $a[0];
        }
        return $list;
    }
}