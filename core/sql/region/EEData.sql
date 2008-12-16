# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-11-26 Mario A. Yandar <mayandar@inticol.com>

DROP TABLE IF EXISTS empty_EEData;
CREATE TABLE empty_EEData (
	DisasterId     VARCHAR(50) NOT NULL,
		PRIMARY KEY(DisasterId)
) Type InnoDB CHARSET=utf8;
