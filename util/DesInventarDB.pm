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
                 'IsCRegion/BOOLEAN'         => 0,
                 'IsVRegion/BOOLEAN'         => 0
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
                 'EventLangCode/STRING'       => '',
                 'SyncRecord/TIMESTAMP'       => 'TIME',
                 'EventName/STRING'           => 'EventLocalName',
                 'EventDesc/STRING'           => 'EventLocalDesc',
                 'EventActive/BOOLEAN'        => undef,
                 'EventPreDefined/BOOLEAN'    => undef,
                 'EventRGBColor/STRING'       => '',
                 'EventKeyWords/STRING'       => '',
                 'EventCreationDate/DATETIME' => undef,
                 'EventLastUpdate/DATETIME'   => 'EventCreationDate'
                },
            'Cause' =>
                {'CauseId/STRING'             => undef,
                 'CauseLangCode/STRING'       => '',
                 'SyncRecord/TIMESTAMP'       => 'TIME',
                 'CauseName/STRING'           => 'CauseLocalName',
                 'CauseDesc/STRING'           => 'CauseLocalDesc',
                 'CauseActive/BOOLEAN'        => undef,
                 'CausePreDefined/BOOLEAN'    => undef,
                 'CauseRGBColor/STRING'       => '',
                 'CauseKeyWords/STRING'       => '',
                 'CauseCreationDate/DATETIME' => undef,
                 'CauseLastUpdate/DATETIME'   => 'CauseCreationDate'
                },
            'GeoLevel' =>
                {'GeoLevelId/STRING'          => undef,
                 #'GeoLevelLangCode/STRING'    => '',
                 #'SyncRecord/TIMESTAMP'       => 'TIME',
                 #'GeoLevelName/STRING'        => undef,
                 #'GeoLevelDesc/STRING'        => undef,
                 'GeoLevelActive/INTEGER'      => 1,
                 #'GeoLevelLayerFile/STRING'   => undef,
                 #'GeoLevelLayerName/STRING'   => undef,
                 #'GeoLevelLayerCode/STRING'   => undef
                }                
            );
1;

