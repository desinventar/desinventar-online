# DesInventar8
# http://www.desinventar.org
# (c) 1999-2009 Corporacion OSSO
#
# 2009-01-13 Jhon H. Caicedo <jhcaiced@desinventar.org>

DROP TABLE IF EXISTS UserSession;
CREATE TABLE UserSession (
	SessionId	      VARCHAR(50) UNIQUE NOT NULL,
	RegionId          VARCHAR(50) DEFAULT '',
	UserName          VARCHAR(50) DEFAULT '',
	Valid             BOOLEAN DEFAULT TRUE,
	Start             DATETIME,
	LastUpdate        DATETIME
) Type InnoDB CHARSET=utf8;
