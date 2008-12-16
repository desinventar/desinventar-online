# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2008-03-05 Mario A. Yandar <mayandar@desinventar.org>
DROP TABLE IF EXISTS empty_Geography;
CREATE TABLE empty_Geography (
	GeographyId				VARCHAR(60) UNIQUE NOT NULL,
		PRIMARY KEY(GeographyId),
	GeographyCode			VARCHAR(60) UNIQUE NOT NULL DEFAULT '',
	GeographyName			VARCHAR(100) NOT NULL DEFAULT '',
	GeographyLevel			INTEGER DEFAULT -1,
		FOREIGN KEY(GeographyLevel) REFERENCES empty_GeoLevel(GeoLevelId)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	GeographyActive			BOOL NOT NULL DEFAULT TRUE
) Type InnoDB CHARSET=utf8;

