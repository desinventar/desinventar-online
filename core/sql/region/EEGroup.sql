# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-11-26 Mario A. Yandar <mayandar@inticol.com>

DROP TABLE IF EXISTS empty_EEGroup;
CREATE TABLE empty_EEGroup (
	EEGroupId    VARCHAR(30) NOT NULL,
		PRIMARY KEY(EEGroupId),
	EEGroupDesc  TEXT NULL,
	EEGroupActive  BOOL NOT NULL DEFAULT True,
	EEGroupPublic  BOOL NOT NULL DEFAULT True
) Type InnoDB CHARSET=utf8;
