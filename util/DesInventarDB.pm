package DesInventarDB;

BEGIN { require 5.005; }

$VERSION = '1.0';

# 2009-02-21 DesInventar Database Structure
%TableDef = ('Region' =>
                {'RegionId/STRING'           => 'RegionUUID',
                 'RegionLabel/STRING'        => undef,
                 'LangIsoCode/STRING'        => 'RegionLangCode',
                 'CountryIso/STRING'         => 'CountryIsoCode',
                 'RegionOrder/INTEGER'       => 0,
                 'RegionStatus/INTEGER'      => 1,
                 'RegionLastUpdate/DATETIME' => 'RegionStructLastUpdate',
                 'IsCRegion/INTEGER'         => 0,
                 'IsVRegion/INTEGER'         => 0
                },
            'RegionAuth' =>
                {'UserName/STRING'     => 'UserName',
                 'RegionId/STRING'     => 'RegionUUID',
                 'Authkey/STRING'      => 'AuthKey',
                 'AuthValue/INTEGER'   => 'AuthValue',
                 'AuthAuxValue/STRING' => 'AuthAuxValue'
                },
            'User' =>
                {'UserName/STRING'           => undef,
                 'UserEMail/STRING'          => undef,
                 'UserPasswd/STRING'         => undef,
                 'UserFullName/STRING'       => undef,
                 'Organization/STRING'       => '',
                 'CountryIso/STRING'         => 'UserCountry',
                 'UserCity/STRING'           => undef,
                 'UserCreationDate/DATETIME' => undef,
                 'UserNotes/STRING'          => undef,
                 'UserActive/INTEGER'        => undef
                },
            'Event' =>
                {'EventId/STRING'             => undef,
                 'LangIsoCode/STRING'         => '',
                 'SyncRecord/DATETIME'        => 'DATETIME',
                 'EventName/STRING'           => 'EventLocalName',
                 'EventDesc/STRING'           => 'EventLocalDesc',
                 'EventActive/INTEGER'        => undef,
                 'EventPreDefined/INTEGER'    => undef,
                 'EventRGBColor/STRING'       => '',
                 'EventKeyWords/STRING'       => '',
                 'EventCreationDate/DATETIME' => undef,
                 'EventLastUpdate/DATETIME'   => 'EventCreationDate'
                },
            'Cause' =>
                {'CauseId/STRING'             => undef,
                 'LangIsoCode/STRING'         => '',
                 'SyncRecord/DATETIME'        => 'DATETIME',
                 'CauseName/STRING'           => 'CauseLocalName',
                 'CauseDesc/STRING'           => 'CauseLocalDesc',
                 'CauseActive/INTEGER'        => undef,
                 'CausePreDefined/INTEGER'    => undef,
                 'CauseRGBColor/STRING'       => '',
                 'CauseKeyWords/STRING'       => '',
                 'CauseCreationDate/DATETIME' => undef,
                 'CauseLastUpdate/DATETIME'   => 'CauseCreationDate'
                },
            'GeoLevel' =>
                {'GeoLevelId/STRING'          => undef,
                 'LangIsoCode/STRING'         => '',
                 'SyncRecord/DATETIME'        => 'DATETIME',
                 'GeoLevelName/STRING'        => undef,
                 'GeoLevelDesc/STRING'        => undef,
                 'GeoLevelActive/INTEGER'     => 1,
                 'GeoLevelLayerFile/STRING'   => undef,
                 'GeoLevelLayerName/STRING'   => undef,
                 'GeoLevelLayerCode/STRING'   => undef
                },
            'Geography' =>
                {'GeographyId/STRING'         => undef,
                 'LangIsoCode/STRING'         => '',
                 'SyncRecord/DATETIME'        => 'DATETIME',
                 'GeographyCode/STRING'       => undef,
                 'GeographyName/STRING'       => undef,
                 'GeographyLevel/INTEGER'     => undef,
                 'GeographyActive/INTEGER'    => undef,
                },
            'Disaster' =>
                {'DisasterId/STRING'          => undef,
                 'SyncRecord/DATETIME'        => 'DATETIME',
                 'DisasterSerial/STRING'      => undef,
                 'DisasterBeginTime/STRING'   => undef,
                 'DisasterGeographyId/STRING' => undef,
                 'DisasterSiteNotes/STRING'   => undef,
                 'DisasterLatitude/DOUBLE'    => undef,
                 'DisasterLongitude/DOUBLE'   => undef,
                 'DisasterSource/STRING'      => undef,
                 # Record Data
                 'RecordStatus/STRING'        => undef,
                 'RecordStatus/STRING'        => undef,
                 'RecordAuthor/STRING'        => undef,
                 'RecordCreation/DATETIME'    => undef,
                 'RecordLastUpdate/DATETIME'  => undef,
                 # Event Fields
                 'EventId/STRING'             => undef,
                 'EventNotes/STRING'          => undef,
                 'EventDuration/INTEGER'      => undef,
                 'EventMagnitude/STRING'      => undef,
                 # Cause Fields
                 'CauseId/STRING'             => undef,
                 'CauseNotes/STRING'          => undef,
                 # Numeric Effects
                 'EffectPeopleDead/INTEGER'          => undef,
                 'EffectPeopleMissing/INTEGER'       => undef,
                 'EffectPeopleInjured/INTEGER'       => undef,
                 'EffectPeopleHarmed/INTEGER'        => undef,
                 'EffectPeopleAffected/INTEGER'      => undef,
                 'EffectPeopleEvacuated/INTEGER'     => undef,
                 'EffectPeopleRelocated/INTEGER'     => undef,
                 'EffectHousesDestroyed/INTEGER'     => undef,
                 'EffectHousesAffected/INTEGER'      => undef,
                 # Stat Fields
                 'EffectPeopleDeadQ/INTEGER'         => 'EffectPeopleDeadStat',
                 'EffectPeopleMissingQ/INTEGER'      => 'EffectPeopleMissingStat',
                 'EffectPeopleInjuredQ/INTEGER'      => 'EffectPeopleInjuredStat',
                 'EffectPeopleHarmedQ/INTEGER'       => 'EffectPeopleHarmedStat',
                 'EffectPeopleAffectedQ/INTEGER'     => 'EffectPeopleAffectedStat',
                 'EffectPeopleEvacuatedQ/INTEGER'    => 'EffectPeopleEvacuatedStat',
                 'EffectPeopleRelocatedQ/INTEGER'    => 'EffectPeopleRelocatedStat',
                 'EffectHousesDestroyedQ/INTEGER'    => 'EffectHousesDestroyedStat',
                 'EffectHousesAffectedQ/INTEGER'     => 'EffectHousesAffectedStat',
                 #
                 'EffectLossesValueLocal/DOUBLE'     => undef,
                 'EffectLossesValueUSD/DOUBLE'       => undef,
                 'EffectRoads/DOUBLE'                => undef,
                 'EffectFarmingAndForest/DOUBLE'     => undef,
                 'EffectLiveStock/INTEGER'           => undef,
                 'EffectEducationCenters/INTEGER'    => undef,
                 'EffectMedicalCenters/INTEGER'      => undef,
                 'EffectOtherLosses/STRING'          => undef,
                 'EffectNotes/STRING'                => undef,
                 # Sectors Affected
                 'SectorTransport/INTEGER'           => undef,
                 'SectorCommunications/INTEGER'      => undef,
                 'SectorRelief/INTEGER'              => undef,
                 'SectorAgricultural/INTEGER'        => undef,
                 'SectorWaterSupply/INTEGER'         => undef,
                 'SectorSewerage/INTEGER'            => undef,
                 'SectorEducation/INTEGER'           => undef,
                 'SectorPower/INTEGER'               => undef,
                 'SectorIndustry/INTEGER'            => undef,
                 'SectorHealth/INTEGER'              => undef,
                 'SectorOther/INTEGER'               => undef
                },
             'DatabaseLog' => 
                {'DBLogDate/DATETIME'                => undef,
                 'SyncRecord/DATETIME'               => 'DATETIME',
                 'DBLogType/STRING'                  => undef,
                 'DBLogNotes/STRING'                 => undef,
                 'DBLogUserName/STRING'              => undef
                },
             'EEField' =>
                {'EEFieldId/STRING'      => undef,
                 'SyncRecord/DATETIME'   => 'DATETIME',
                 'EEGroupId/STRING'      => undef,
                 'EEFieldLabel/STRING'   => undef,
                 'EEFieldDesc/STRING'    => undef,
                 'EEFieldType/STRING'    => undef,
                 'EEFieldSize/INTEGER'   => undef,
                 'EEFieldOrder/INTEGER'  => undef,
                 'EEFieldStatus/INTEGER' => 'EEFieldActive'
                },
             'EEGroup' =>
                {'EEGroupId/STRING'      => undef,
                 'SyncRecord/DATETIME'   => 'DATETIME',
                 'EEGroupLabel/STRING'   => undef,
                 'EEGroupDesc/STRING'    => undef,
                 'EEGroupStatus/INTEGER' => 'EEGroupActive'
                }
            );
1;

