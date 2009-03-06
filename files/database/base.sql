/* BASE.DB - DesInventar8.2
2009-03-05
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
PRIMARY KEY('CountryIso')
);

DROP TABLE IF EXISTS Language;
CREATE TABLE 'Language' ( 
LangIsoCode VARCHAR(3), 
LangIsoName VARCHAR(50), 
LangLocalName VARCHAR(50), 
LangStatus INTEGER, 
PRIMARY KEY('LangIsoCode')
);

DROP TABLE IF EXISTS DI_Event;
CREATE TABLE 'DI_Event' ( 
EventId VARCHAR(50) NOT NULL, 
LangIsoCode VARCHAR(3) NOT NULL, 
EventName VARCHAR(50), 
EventDesc TEXT, 
EventActive INTEGER, 
EventRGBColor VARCHAR(10), 
EventKeyWords TEXT, 
EventCreationDate DATETIME, 
EventLastUpdate DATETIME, 
PRIMARY KEY('EventId','LangIsoCode')
);

DROP TABLE IF EXISTS DI_Cause;
CREATE TABLE 'DI_Cause' ( 
CauseId VARCHAR(50) NOT NULL, 
LangIsoCode VARCHAR(3) NOT NULL, 
CauseName VARCHAR(50), 
CauseDesc TEXT, 
CauseActive INTEGER, 
CauseRGBColor VARCHAR(10), 
CauseKeyWords TEXT, 
CauseCreationDate DATETIME, 
CauseLastUpdate DATETIME, 
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
PRIMARY KEY('DictLabelId','LangIsoCode')
);

DROP TABLE IF EXISTS LabelGroup;
CREATE TABLE 'LabelGroup' ( 
DictLabelId INTEGER, 
LGName VARCHAR(50), 
LabelName VARCHAR(30), 
LGOrder INTEGER, 
PRIMARY KEY('DictLabelId')
);

/* Set initial values */
insert into Language values ('spa', 'Spanish', 'Español', 1);
insert into Language values ('eng', 'English', 'English', 1);
insert into Language values ('fre', 'French', 'Français', 1);
insert into Language values ('por', 'Portuguese', 'Portugais', 1);
