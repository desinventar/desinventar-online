# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
DROP TABLE IF EXISTS empty_Disaster;
CREATE TABLE empty_Disaster (
	# Disaster Related Fields
	DisasterId              VARCHAR(50) UNIQUE NOT NULL,
		PRIMARY KEY(DisasterId),
	DisasterSerial          VARCHAR(50) NOT NULL,
	DisasterBeginTime       VARCHAR(30) NOT NULL,
	DisasterGeographyId     VARCHAR(60) NOT NULL,
		FOREIGN KEY(DisasterGeographyId) REFERENCES empty_Geography(GeographyId)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	DisasterSiteNotes       TEXT,
	DisasterLatitude        DOUBLE,
	DisasterLongitude       DOUBLE,
	DisasterSource          VARCHAR(200),

	# Record Related Fields
	RecordStatus            VARCHAR(20),
	RecordAuthor            VARCHAR(50),
	RecordCreation          DATETIME,
	RecordUpdate            DATETIME,

	# Event Related Fields
	EventId                 VARCHAR(50),
		FOREIGN KEY(EventId) REFERENCES empty_Event(EventId)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	EventNotes              TEXT,
	EventDuration           INT,
	EventMagnitude          VARCHAR(80),

	# Cause Related Fields
	CauseId                 VARCHAR(50),
		FOREIGN KEY(CauseId) REFERENCES empty_Cause(CauseId)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	CauseNotes              TEXT,

	# Effects Fields
	EffectPeopleDead        INT,
	EffectPeopleMissing     INT,
	EffectPeopleInjured     INT,
	EffectPeopleHarmed      INT,
	EffectPeopleAffected    INT,
	EffectPeopleEvacuated   INT,
	EffectPeopleRelocated   INT,
	EffectHousesDestroyed   INT,
	EffectHousesAffected    INT,
	EffectPeopleDeadStat    INT,
	EffectPeopleMissingStat   INT,
	EffectPeopleInjuredStat   INT,
	EffectPeopleHarmedStat    INT,
	EffectPeopleAffectedStat  INT,
	EffectPeopleEvacuatedStat INT,
	EffectPeopleRelocatedStat INT,
	EffectHousesDestroyedStat INT,
	EffectHousesAffectedStat  INT,

	EffectLossesValueLocal  NUMERIC(30,2),
	EffectLossesValueUSD    NUMERIC(30,2),
	EffectRoads             NUMERIC(30,2),
	EffectFarmingAndForest  NUMERIC(30,2),
	EffectLiveStock         NUMERIC(10),
	EffectEducationCenters  NUMERIC(10),
	EffectMedicalCenters    NUMERIC(10),

	EffectOtherLosses       TEXT,
	EffectNotes             TEXT,

	SectorTransport         INT,
	SectorCommunications    INT,
	SectorRelief            INT,
	SectorAgricultural      INT,
	SectorWaterSupply       INT,
	SectorSewerage          INT,
	SectorEducation         INT,
	SectorPower             INT,
	SectorIndustry          INT,
	SectorHealth            INT,
	SectorOther             INT
) TYPE InnoDB CHARSET=utf8;

