/* REGION.DB - DesInventar8.2
2009-07-28
*/

DROP TABLE IF EXISTS Info;
CREATE TABLE 'Info' ( 
InfoKey VARCHAR(50), 
LangIsoCode VARCHAR(3), 
SyncRecord DATETIME, 
InfoValue VARCHAR(1024), 
InfoAuxValue VARCHAR(1024), 
PRIMARY KEY('InfoKey')
);

DROP TABLE IF EXISTS Event;
CREATE TABLE 'Event' ( 
EventId VARCHAR(50), 
LangIsoCode VARCHAR(3), 
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
CauseId VARCHAR(50), 
LangIsoCode VARCHAR(3), 
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
GeoLevelId INTEGER, 
LangIsoCode VARCHAR(3), 
SyncRecord DATETIME, 
GeoLevelName VARCHAR(50) DEFAULT '---', 
GeoLevelDesc TEXT NULL, 
GeoLevelActive INTEGER DEFAULT 0, 
PRIMARY KEY('GeoLevelId','LangIsoCode')
);

DROP TABLE IF EXISTS GeoCarto;
CREATE TABLE 'GeoCarto' ( 
GeographyId VARCHAR(100), 
GeoLevelId INTEGER, 
LangIsoCode VARCHAR(3), 
RegionId VARCHAR(50), 
SyncRecord DATETIME, 
GeoLevelLayerFile VARCHAR(50), 
GeoLevelLayerName VARCHAR(50), 
GeoLevelLayerCode VARCHAR(50), 
PRIMARY KEY('GeographyId','GeoLevelId')
);

DROP TABLE IF EXISTS Geography;
CREATE TABLE 'Geography' ( 
GeographyId VARCHAR(100), 
LangIsoCode VARCHAR(3), 
SyncRecord DATETIME, 
GeographyCode VARCHAR(100) DEFAULT '---', 
GeographyName VARCHAR(200) DEFAULT '---', 
GeographyLevel INTEGER DEFAULT -1, 
GeographyActive INTEGER DEFAULT 1, 
PRIMARY KEY('GeographyId','LangIsoCode')
);

DROP TABLE IF EXISTS Disaster;
CREATE TABLE 'Disaster' ( 
DisasterId VARCHAR(50), 
SyncRecord DATETIME, 
DisasterSerial VARCHAR(50), 
DisasterBeginTime VARCHAR(30), 
DisasterGeographyId VARCHAR(100), 
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
EEFieldId VARCHAR(30), 
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
DisasterId VARCHAR(50), 
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
