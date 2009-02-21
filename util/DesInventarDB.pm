package DesInventarDB;

BEGIN { require 5.005; }

$VERSION = '1.0';

%Region = ('RegionId/STRING'           => 'RegionUUID',
           'RegionLabel/STRING'        => 'RegionLabel',
           'LangIsoCode/STRING'        => 'RegionLangCode',
           'CountryIso/STRING'         => 'CountryIsoCode',
           'RegionOrder/INTEGER'       => 0,
           'RegionStatus/INTEGER'      => 1,
           'RegionLastUpdate/DATETIME' => 'RegionStructLastUpdate',
           'IsCRegion/BOOLEAN'         => 0,
           'IsVRegion/BOOLEAN'         => 0
          );

1;

