# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
DROP TABLE IF EXISTS VirtualRegionItem;
CREATE TABLE VirtualRegionItem (
	VirtualRegUUID        VARCHAR(20) NOT NULL,
	RegionUUID            VARCHAR(20) NOT NULL,
		PRIMARY KEY(VirtualRegUUID,RegionUUID)
) Type InnoDB CHARSET=utf8;
