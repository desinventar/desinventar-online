# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-11-26 Mario A. Yandar <mayandar@inticol.com>

DROP TABLE IF EXISTS Region;
CREATE TABLE Region (
	RegionUUID        VARCHAR(20) UNIQUE NOT NULL,
		PRIMARY KEY(RegionUUID),
	RegionLabel            VARCHAR(50) NOT NULL,
	RegionDesc             TEXT,
	RegionDescEN           TEXT,
	RegionActive           BOOL DEFAULT TRUE,
	RegionPublic           BOOL DEFAULT FALSE,
	RegionLangCode         VARCHAR(10),
	RegionStructLastUpdate DATETIME,
	PredefEventLastUpdate  DATETIME,
	PredefCauseLastUpdate  DATETIME,
	CountryIsoCode         VARCHAR(10) NOT NULL,
		FOREIGN KEY(CountryISOCode) REFERENCES Country(CountryISOCode)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	PeriodBeginDate        DATETIME,
	PeriodEndDate          DATETIME,
	OptionAdminURL         VARCHAR(100),
	OptionOutOfPeriod      INT,
	GeoLimitMinX           DOUBLE NULL,
	GeoLimitMinY           DOUBLE NULL,
	GeoLimitMaxX           DOUBLE NULL,
	GeoLimitMaxY           DOUBLE NULL,
	RegionLayerFile        VARCHAR(50) NULL,
	RegionLayerCode        VARCHAR(50) NULL,
	RegionLayerName        VARCHAR(50) NULL
) Type InnoDB CHARSET=utf8;
