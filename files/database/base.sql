/* BASE.DB - DesInventar8.2
2009-07-30

DROP TABLE IF EXISTS 
CREATE TABLE 
PRIMARY KEY
);
*/

DROP TABLE IF EXISTS Country;
CREATE TABLE 'Country' (
CountryIso VARCHAR(3), 
CountryIsoName VARCHAR(100), 
CountryName VARCHAR(100), 
CountryContinent VARCHAR(20), 
CountryMinX DOUBLE, 
CountryMinY DOUBLE, 
CountryMaxX DOUBLE, 
CountryMaxY DOUBLE, 
RecordCreation DATETIME, 
RecordSync DATETIME, 
RecordUpdate DATETIME, 
PRIMARY KEY('CountryIso')
);

DROP TABLE IF EXISTS Language;
CREATE TABLE 'Language' ( 
LangIsoCode VARCHAR(3), 
LangIsoName VARCHAR(50), 
LangLocalName VARCHAR(50), 
LangStatus INTEGER, 
RecordCreation DATETIME, 
RecordSync DATETIME, 
RecordUpdate DATETIME, 
PRIMARY KEY('LangIsoCode')
);

DROP TABLE IF EXISTS Event;
CREATE TABLE 'Event' ( 
EventId VARCHAR(50), 
LangIsoCode VARCHAR(3), 
EventName VARCHAR(50), 
EventDesc TEXT, 
EventActive INTEGER, 
EventPredefined INTEGER, 
EventRGBColor VARCHAR(10), 
EventKeyWords TEXT, 
RecordCreation DATETIME, 
RecordSync DATETIME, 
RecordUpdate DATETIME, 
PRIMARY KEY('EventId','LangIsoCode')
);

DROP TABLE IF EXISTS Cause;
CREATE TABLE 'Cause' ( 
CauseId VARCHAR(50), 
LangIsoCode VARCHAR(3), 
CauseName VARCHAR(50), 
CauseDesc TEXT, 
CauseActive INTEGER, 
CausePredefined INTEGER, 
CauseRGBColor VARCHAR(10), 
CauseKeyWords TEXT, 
RecordCreation DATETIME, 
RecordSync DATETIME, 
RecordUpdate DATETIME, 
PRIMARY KEY('CauseId','LangIsoCode')
);

DROP TABLE IF EXISTS Dictionary;
CREATE TABLE 'Dictionary' ( 
DictLabelId INTEGER, 
LangIsoCode VARCHAR(3), 
DictTranslation VARCHAR(30), 
DictTechHelp VARCHAR(50), 
DictBasDesc TEXT, 
DictFullDesc TEXT, 
RecordCreation DATETIME, 
RecordSync DATETIME, 
RecordUpdate DATETIME, 
PRIMARY KEY('DictLabelId','LangIsoCode')
);

DROP TABLE IF EXISTS LabelGroup;
CREATE TABLE 'LabelGroup' ( 
DictLabelId INTEGER, 
LGName VARCHAR(50), 
LabelName VARCHAR(30), 
LGOrder INTEGER, 
RecordCreation DATETIME, 
RecordSync DATETIME, 
RecordUpdate DATETIME, 
PRIMARY KEY('DictLabelId')
);

DROP TABLE IF EXISTS Info;
CREATE TABLE 'Info' ( 
InfoKey VARCHAR(50), 
InfoValue VARCHAR(1024), 
InfoAuxValue VARCHAR(1024), 
RecordCreation DATETIME, 
RecordSync DATETIME, 
RecordUpdate DATETIME, 
PRIMARY KEY('InfoKey')
);

DROP TABLE IF EXISTS Sync;
CREATE TABLE 'Sync' ( 
SyncId VARCHAR(50), 
SyncTable VARCHAR(100), 
SyncUpload DATETIME, 
SyncDownload DATETIME, 
SyncURL VARCHAR(1024), 
SyncSpec VARCHAR(1024), 
PRIMARY KEY('SyncId')
);

/* Set initial values */
insert into Language values ('spa', 'Spanish', 'Español', 1,'','','');
insert into Language values ('eng', 'English', 'English', 1,'','','');
insert into Language values ('fre', 'French', 'Français', 1,'','','');
insert into Language values ('por', 'Portuguese', 'Portugais', 1,'','','');

insert into Info values ('DBVersion','1.0','','','','');
