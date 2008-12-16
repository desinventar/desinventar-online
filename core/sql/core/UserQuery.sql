# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
DROP TABLE IF EXISTS UserQuery;
CREATE TABLE UserQuery (
	UserName        VARCHAR(20) NOT NULL,
	UQName          VARCHAR(50),
	RegionUUID      VARCHAR(20),
	VirtualRegUUID  VARCHAR(20),
	UQDate          DATETIME,
	UQInput         TEXT,
	UQActive        BOOLEAN
) Type InnoDB CHARSET=utf8;
