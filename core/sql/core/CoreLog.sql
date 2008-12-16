# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
DROP TABLE IF EXISTS CoreLog;
CREATE TABLE CoreLog (
	LogDate           DATETIME,
	LogUserName       VARCHAR(20),
	LogAction         VARCHAR(30),
	LogMessage        VARCHAR(200)
) Type InnoDB CHARSET=utf8;
