# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
DROP TABLE IF EXISTS VirtualRegion;
CREATE TABLE VirtualRegion (
	VirtualRegUUID        VARCHAR(20) UNIQUE NOT NULL,
		PRIMARY KEY(VirtualRegUUID),
	VirtualRegLabel       VARCHAR(50) NOT NULL,
	VirtualRegDesc        TEXT,
	VirtualRegActive      BOOL DEFAULT TRUE,
	VirtualRegPublic      BOOL DEFAULT FALSE,
	VirtualRegLayerFile   VARCHAR(50),
	VirtualRegLayerCode   VARCHAR(50),
	VirtualRegLayerName   VARCHAR(50)
) Type InnoDB CHARSET=utf8;
