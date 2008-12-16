# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-12-03 Mario A. Yandar <mayandar@inticol.com>

DROP TABLE IF EXISTS empty_EEField;
CREATE TABLE empty_EEField (
	EEFieldId     VARCHAR(50) NOT NULL,
		PRIMARY KEY(EEFieldId),
	EEGroupId     VARCHAR(30) NULL,
	EEFieldLabel  VARCHAR(50) NULL,
	EEFieldDesc   TEXT NULL,
	EEFieldType   VARCHAR(20) NULL,
	EEFieldSize   INT NULL,
	EEFieldOrder  INT NULL,
	EEFieldActive BOOL NOT NULL DEFAULT True,
	EEFieldPublic BOOL NOT NULL DEFAULT False
) Type InnoDB CHARSET=utf8;
