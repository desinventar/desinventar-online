# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-05-29 Mario A. Yandar <mayandar@inticol.com>

DROP TABLE IF EXISTS empty_Cause;
CREATE TABLE empty_Cause (
	CauseId              VARCHAR(50) UNIQUE NOT NULL,
		PRIMARY KEY(CauseId),
	CauseLocalName       VARCHAR(50),
	CauseLocalDesc       TEXT,
	CauseActive          BOOL NOT NULL DEFAULT True,
	CausePreDefined      BOOL NOT NULL DEFAULT False,
	CauseCreationDate    DATETIME
) Type InnoDB CHARSET=utf8;
