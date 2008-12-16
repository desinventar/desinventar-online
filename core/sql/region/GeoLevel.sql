# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-11-26 Mario A. Yandar <mayandar@inticol.com>
#
DROP TABLE IF EXISTS empty_GeoLevel;
CREATE TABLE empty_GeoLevel (
	GeoLevelId			INTEGER UNIQUE NOT NULL,
		PRIMARY KEY(GeoLevelId),
	GeoLevelName		VARCHAR(50) UNIQUE NOT NULL DEFAULT '',
	GeoLevelDesc		TEXT NULL,
	GeoLevelLayerFile		VARCHAR(50) NULL,
	GeoLevelLayerCode		VARCHAR(50) NULL,
	GeoLevelLayerName		VARCHAR(50) NULL
) Type InnoDB CHARSET=utf8;
