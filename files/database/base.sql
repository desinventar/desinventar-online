/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2009 Corporacion OSSO
  BASE.DB
*/

DROP TABLE IF EXISTS Country;
CREATE TABLE Country (
	CountryIso       VARCHAR(3),
	CountryLangCode  VARCHAR(3),
	CountryName      VARCHAR(100),
	CountryContinent VARCHAR(20),
	CountryMinX      DOUBLE,
	CountryMinY      DOUBLE,
	CountryMaxX      DOUBLE,
	CountryMaxY      DOUBLE,
	PRIMARY KEY(CountryIso)
);

DROP TABLE IF EXISTS Language;

CREATE TABLE Language (
	LangIsoCode   VARCHAR(3),
	LangIsoName   VARCHAR(50),
	LangLocalName VARCHAR(50),
	LangStatus    INT,
	PRIMARY KEY(LangIsoCode)
);

DROP TABLE IF EXISTS DI_Event;
CREATE TABLE DI_Event (
	EventId VARCHAR(50) NOT NULL,
	EventLangCode VARCHAR(3) NOT NULL,
	EvenlName VARCHAR(50),
	EventDesc TEXT,
	EventActive BOOLEAN,
	EventRGBColor VARCHAR(10),
	EventKeyWords TEXT,
	EventCreationDate DATETIME,
	EventLastUpdate DATETIME,
	PRIMARY KEY(EventId,EventLangCode)
);

DROP TABLE IF EXISTS DI_Cause;
CREATE TABLE DI_Cause (
	CauseId VARCHAR(50) NOT NULL,
	CauseLangCode VARCHAR(3) NOT NULL,
	CauseName VARCHAR(50),
	CauseDesc TEXT,
	CauseActive BOOLEAN,
	CauseRGBColor VARCHAR(10),
	CauseKeyWords TEXT,
	CauseCreationDate DATETIME,
	CauseLastUpdate DATETIME,
	PRIMARY KEY(CauseId,CauseLangCode)
);

DROP TABLE IF EXISTS Dictionary;
CREATE TABLE Dictionary (
	DictLabelId INTEGER,
	LangIsoCode VARCHAR(3),
	DictTranslation VARCHAR(30),
	DictTechHelp VARCHAR(50),
	DictBasDesc TEXT,
	DictFullDesc TEXT,
	PRIMARY KEY(DictLabelId,LangIsoCode)
);

DROP TABLE IF EXISTS LabelGroup;
CREATE TABLE LabelGroup (
	DictLabelId INTEGER,
	LGName VARCHAR(50),
	LabelName VARCHAR(30),
	LGOrder INTEGER,
	PRIMARY KEY(DictLabelId)
);

