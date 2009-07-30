#!/usr/bin/perl
#
# DesInventar - http://www.desinventar.org
# (c) 1999-2009 Corporacion OSSO
#
# DesInventar Database Structure - Perl Interface
#
# 2009-07-04 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
package DesInventarDB;

BEGIN { require 5.005; }

$VERSION = '1.1';

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
                {'UserId/STRING'       => 'UserName',
                 'RegionId/STRING'     => 'RegionUUID',
                 'Authkey/STRING'      => 'AuthKey',
                 'AuthValue/INTEGER'   => 'AuthValue',
                 'AuthAuxValue/STRING' => 'AuthAuxValue'
                },
            'User' =>
                {'UserId/STRING'             => undef,
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
                 'LangIsoCode/STRING'         => 'spa',
                 'EventName/STRING'           => 'EventLocalName',
                 'EventDesc/STRING'           => 'EventLocalDesc',
                 'EventActive/INTEGER'        => undef,
                 'EventPreDefined/INTEGER'    => undef,
                 'EventRGBColor/STRING'       => '',
                 'EventKeyWords/STRING'       => '',
                 'RecordCreation/DATETIME'    => 'DATETIME',
                 'RecordSync/DATETIME'        => 'DATETIME',
                 'RecordUpdate/DATETIME'      => 'DATETIME'
                },
            'Cause' =>
                {'CauseId/STRING'             => undef,
                 'LangIsoCode/STRING'         => 'spa',
                 'CauseName/STRING'           => 'CauseLocalName',
                 'CauseDesc/STRING'           => 'CauseLocalDesc',
                 'CauseActive/INTEGER'        => undef,
                 'CausePreDefined/INTEGER'    => undef,
                 'CauseRGBColor/STRING'       => '',
                 'CauseKeyWords/STRING'       => '',
                 'RecordCreation/DATETIME'    => 'DATETIME',
                 'RecordSync/DATETIME'        => 'DATETIME',
                 'RecordUpdate/DATETIME'      => 'DATETIME'
                },
            'GeoLevel' =>
                {'GeoLevelId/INTEGER'         => undef,
                 'LangIsoCode/STRING'         => 'spa',
                 'GeoLevelName/STRING'        => undef,
                 'GeoLevelDesc/STRING'        => undef,
                 'GeoLevelActive/INTEGER'     => 1,
                 'RecordCreation/DATETIME'    => 'DATETIME',
                 'RecordSync/DATETIME'        => 'DATETIME',
                 'RecordUpdate/DATETIME'      => 'DATETIME'
                },
			'GeoCarto' =>
				{'GeographyId/STRING'         => undef,
				 'GeoLevelId/INTEGER'         => undef,
				 'LangIsoCode/STRING'         => 'spa',
				 'RegionId/STRING'            => undef,
                 'GeoLevelLayerFile/STRING'   => undef,
                 'GeoLevelLayerName/STRING'   => undef,
                 'GeoLevelLayerCode/STRING'   => undef,
                 'RecordCreation/DATETIME'    => 'DATETIME',
                 'RecordSync/DATETIME'        => 'DATETIME',
                 'RecordUpdate/DATETIME'      => 'DATETIME'
                },
            'Geography' =>
                {'GeographyId/STRING'         => undef,
                 'LangIsoCode/STRING'         => 'spa',
                 'GeographyCode/STRING'       => undef,
                 'GeographyName/STRING'       => undef,
                 'GeographyLevel/INTEGER'     => undef,
                 'GeographyActive/INTEGER'    => undef,
                 'RecordCreation/DATETIME'    => 'DATETIME',
                 'RecordSync/DATETIME'        => 'DATETIME',
                 'RecordUpdate/DATETIME'      => 'DATETIME'
                },
            'Disaster' =>
                {'DisasterId/STRING'          => undef,
                 'DisasterSerial/STRING'      => undef,
                 'DisasterBeginTime/STRING'   => undef,
                 'DisasterGeographyId/STRING' => undef,
                 'DisasterSiteNotes/STRING'   => undef,
                 'DisasterLatitude/DOUBLE'    => undef,
                 'DisasterLongitude/DOUBLE'   => undef,
                 'DisasterSource/STRING'      => undef,
                 # Record Data
                 'RecordStatus/STRING'        => undef,
                 'RecordAuthor/STRING'        => undef,
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
                 'SectorOther/INTEGER'               => undef,
                 'RecordCreation/DATETIME'           => 'DATETIME',
                 'RecordSync/DATETIME'               => 'DATETIME',
                 'RecordUpdate/DATETIME'             => 'DATETIME'
                },
             'DatabaseLog' => 
                {'DBLogDate/DATETIME'                => undef,
                 'DBLogType/STRING'                  => undef,
                 'DBLogNotes/STRING'                 => undef,
                 'DBLogUserId/STRING'                => undef
                },
             'EEField' =>
                {'EEFieldId/STRING'      => undef,
                 'EEGroupId/STRING'      => undef,
                 'EEFieldLabel/STRING'   => undef,
                 'EEFieldDesc/STRING'    => undef,
                 'EEFieldType/STRING'    => undef,
                 'EEFieldSize/INTEGER'   => undef,
                 'EEFieldOrder/INTEGER'  => undef,
                 'EEFieldStatus/INTEGER' => 'EEFieldActive',
                 'RecordCreation/DATETIME'           => 'DATETIME',
                 'RecordSync/DATETIME'               => 'DATETIME',
                 'RecordUpdate/DATETIME'             => 'DATETIME'
                },
             'EEGroup' =>
                {'EEGroupId/STRING'      => undef,
                 'EEGroupLabel/STRING'   => undef,
                 'EEGroupDesc/STRING'    => undef,
                 'EEGroupStatus/INTEGER' => 'EEGroupActive',
                 'RecordCreation/DATETIME'           => 'DATETIME',
                 'RecordSync/DATETIME'               => 'DATETIME',
                 'RecordUpdate/DATETIME'             => 'DATETIME'
                },
             'EEData' =>
                {'DisasterId/STRING'     => undef,
                 'RecordCreation/DATETIME'           => 'DATETIME',
                 'RecordSync/DATETIME'               => 'DATETIME',
                 'RecordUpdate/DATETIME'             => 'DATETIME'
                }
            );
1;

