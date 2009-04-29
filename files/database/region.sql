/* REGION.DB - DesInventar8.2
2009-04-28
*/

DROP TABLE IF EXISTS Info;
CREATE TABLE 'Info' ( 
InfoKey VARCHAR(50), 
SyncRecord DATETIME, 
InfoValue VARCHAR(1024), 
InfoAuxValue VARCHAR(1024), 
PRIMARY KEY('InfoKey')
);

DROP TABLE IF EXISTS Event;
CREATE TABLE 'Event' ( 
EventId VARCHAR(50) NOT NULL, 
LangIsoCode VARCHAR(3) NOT NULL, 
SyncRecord DATETIME, 
EventName VARCHAR(50), 
EventDesc TEXT, 
EventActive INTEGER DEFAULT 1, 
EventPredefined INTEGER DEFAULT 0, 
EventRGBColor VARCHAR(10), 
EventKeyWords TEXT, 
EventCreationDate DATETIME, 
EventLastUpdate DATETIME, 
PRIMARY KEY('EventId','LangIsoCode')
);

DROP TABLE IF EXISTS Cause;
CREATE TABLE 'Cause' ( 
CauseId VARCHAR(50) NOT NULL, 
LangIsoCode VARCHAR(3) NOT NULL, 
SyncRecord DATETIME, 
CauseName VARCHAR(50), 
CauseDesc TEXT, 
CauseActive INTEGER DEFAULT 1, 
CausePredefined INTEGER DEFAULT 0, 
CauseRGBColor VARCHAR(10), 
CauseKeyWords TEXT, 
CauseCreationDate DATETIME, 
CauseLastUpdate DATETIME, 
PRIMARY KEY('CauseId','LangIsoCode')
);

DROP TABLE IF EXISTS GeoLevel;
CREATE TABLE 'GeoLevel' ( 
GeoLevelId INTEGER NOT NULL, 
LangIsoCode VARCHAR(3) NOT NULL, 
SyncRecord DATETIME, 
GeoLevelName VARCHAR(50) NOT NULL DEFAULT '---', 
GeoLevelDesc TEXT NULL, 
GeoLevelActive INTEGER NOT NULL DEFAULT 0, 
PRIMARY KEY('GeoLevelId','LangIsoCode')
);

DROP TABLE IF EXISTS GeoCarto;
CREATE TABLE 'GeoCarto' ( 
GeographyId VARCHAR(100), 
GeoLevelId INTEGER NOT NULL, 
SyncRecord DATETIME, 
GeoLevelLayerFile VARCHAR(50) NULL, 
GeoLevelLayerName VARCHAR(50) NULL, 
GeoLevelLayerCode VARCHAR(50) NULL, 
PRIMARY KEY('GeographyId','GeoLevelId')

);

DROP TABLE IF EXISTS Geography;
CREATE TABLE 'Geography' ( 
GeographyId VARCHAR(100) NOT NULL, 
LangIsoCode VARCHAR(3) NOT NULL, 
SyncRecord DATETIME, 
GeographyCode VARCHAR(100) NOT NULL DEFAULT '---', 
GeographyName VARCHAR(200) NOT NULL DEFAULT '---', 
GeographyLevel INTEGER DEFAULT -1, 
GeographyActive INTEGER DEFAULT 1, 
PRIMARY KEY('GeographyId','LangIsoCode')
);

DROP TABLE IF EXISTS Disaster;
CREATE TABLE 'Disaster' ( 
DisasterId VARCHAR(50) NOT NULL, 
SyncRecord DATETIME, 
DisasterSerial VARCHAR(50) NOT NULL, 
DisasterBeginTime VARCHAR(30) NOT NULL, 
DisasterGeographyId VARCHAR(100) NOT NULL, 
DisasterSiteNotes TEXT, 
DisasterLatitude DOUBLE, 
DisasterLongitude DOUBLE, 
DisasterSource VARCHAR(200), 
RecordStatus VARCHAR(20), 
RecordAuthor VARCHAR(100), 
RecordCreation DATETIME, 
RecordLastUpdate DATETIME, 
EventId VARCHAR(50), 
EventNotes TEXT, 
EventDuration INTEGER, 
EventMagnitude VARCHAR(80), 
CauseId VARCHAR(50), 
CauseNotes TEXT, 
EffectPeopleDead INTEGER, 
EffectPeopleMissing INTEGER, 
EffectPeopleInjured INTEGER, 
EffectPeopleHarmed INTEGER, 
EffectPeopleAffected INTEGER, 
EffectPeopleEvacuated INTEGER, 
EffectPeopleRelocated INTEGER, 
EffectHousesDestroyed INTEGER, 
EffectHousesAffected INTEGER, 
EffectPeopleDeadQ INTEGER, 
EffectPeopleMissingQ INTEGER, 
EffectPeopleInjuredQ INTEGER, 
EffectPeopleHarmedQ INTEGER, 
EffectPeopleAffectedQ INTEGER, 
EffectPeopleEvacuatedQ INTEGER, 
EffectPeopleRelocatedQ INTEGER, 
EffectHousesDestroyedQ INTEGER, 
EffectHousesAffectedQ INTEGER, 
EffectLossesValueLocal NUMERIC(30,2), 
EffectLossesValueUSD NUMERIC(30,2), 
EffectRoads NUMERIC(30,2), 
EffectFarmingAndForest NUMERIC(30,2), 
EffectLiveStock NUMERIC(10), 
EffectEducationCenters NUMERIC(10), 
EffectMedicalCenters NUMERIC(10), 
EffectOtherLosses TEXT, 
EffectNotes TEXT, 
SectorTransport INTEGER, 
SectorCommunications INTEGER, 
SectorRelief INTEGER, 
SectorAgricultural INTEGER, 
SectorWaterSupply INTEGER, 
SectorSewerage INTEGER, 
SectorEducation INTEGER, 
SectorPower INTEGER, 
SectorIndustry INTEGER, 
SectorHealth INTEGER, 
SectorOther INTEGER, 
PRIMARY KEY('DisasterId')
);

DROP TABLE IF EXISTS EEGroup;
CREATE TABLE 'EEGroup' ( 
EEGroupId VARCHAR(30), 
SyncRecord DATETIME, 
EEGroupLabel VARCHAR(50), 
EEGroupDesc TEXT, 
EEGroupStatus INTEGER, 
PRIMARY KEY('EEGroupId')
);

DROP TABLE IF EXISTS EEField;
CREATE TABLE 'EEField' ( 
EEFieldId VARCHAR(30) NOT NULL, 
SyncRecord DATETIME, 
EEGroupId VARCHAR(30), 
EEFieldLabel VARCHAR(30), 
EEFieldDesc TEXT, 
EEFieldType VARCHAR(20), 
EEFieldSize INTEGER, 
EEFieldOrder INTEGER, 
EEFieldStatus INTEGER, 
PRIMARY KEY('EEFieldId')
);

DROP TABLE IF EXISTS EEData;
CREATE TABLE 'EEData' ( 
DisasterId VARCHAR(50) NOT NULL, 
SyncRecord DATETIME, 
PRIMARY KEY('DisasterId')
);

DROP TABLE IF EXISTS DatabaseLog;
CREATE TABLE 'DatabaseLog' ( 
DBLogDate DATETIME, 
SyncRecord DATETIME, 
DBLogType VARCHAR(20), 
DBLogNotes TEXT, 
DBLogUserName VARCHAR(20)
);

/* Set initial values */
INSERT INTO Info VALUES ('DBVersion','','1.0','');
INSERT INTO Info VALUES ('RegionId','','','');
INSERT INTO Info VALUES ('RegionLabel','','','');
INSERT INTO Info VALUES ('RegionDesc','','','');
INSERT INTO Info VALUES ('RegionDescEN','','','');
INSERT INTO Info VALUES ('CountryIso','','','');
INSERT INTO Info VALUES ('RegionLastUpdate','','','');
INSERT INTO Info VALUES ('I18NFirstLang','','eng','');
INSERT INTO Info VALUES ('I18NSecondLang','','','');
INSERT INTO Info VALUES ('I18NThirdLang','','','');
INSERT INTO Info VALUES ('PeriodBeginDate','','','');
INSERT INTO Info VALUES ('PeriodEndDate','','','');
INSERT INTO Info VALUES ('PeriodOutOfRange','','','');
INSERT INTO Info VALUES ('InfoCredits','','','');
INSERT INTO Info VALUES ('InfoGeneral','','','');
INSERT INTO Info VALUES ('InfoSources','','','');
INSERT INTO Info VALUES ('InfoSynopsis','','','');
INSERT INTO Info VALUES ('InfoObservation','','','');
INSERT INTO Info VALUES ('InfoGeography','','','');
INSERT INTO Info VALUES ('InfoCartography','','','');
INSERT INTO Info VALUES ('InfoAdminURL','','','');
INSERT INTO Info VALUES ('GeoLimitMinX','','','');
INSERT INTO Info VALUES ('GeoLimitMinY','','','');
INSERT INTO Info VALUES ('GeoLimitMaxX','','','');
INSERT INTO Info VALUES ('GeoLimitMaxY','','','');
INSERT INTO Info VALUES ('CartoLayerFile','','','');
INSERT INTO Info VALUES ('CartoLayerName','','','');
INSERT INTO Info VALUES ('CartoLayerCode','','','');
INSERT INTO Info VALUES ('Sync_Info','','','');
INSERT INTO Info VALUES ('Sync_Event','','','');
INSERT INTO Info VALUES ('Sync_Cause','','','');
INSERT INTO Info VALUES ('Sync_GeoLevel','','','');
INSERT INTO Info VALUES ('Sync_Geography','','','');
INSERT INTO Info VALUES ('Sync_Disaster','','','');
INSERT INTO Info VALUES ('Sync_EEField','','','');
INSERT INTO Info VALUES ('Sync_EEData','','','');
INSERT INTO Info VALUES ('Sync_EEGroup','','','');
INSERT INTO Info VALUES ('Sync_DatabaseLog','','','');
