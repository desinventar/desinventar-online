# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-01-08 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-03-04 Mario A. Yandar <mayandar@desinventar.org>
# 	DBLogDate			DATETIME,
DROP TABLE IF EXISTS empty_DatabaseLog;
CREATE TABLE empty_DatabaseLog (
	DBLogDate			VARCHAR(30),
	DBLogType			VARCHAR(20),
	DBLogNotes			TEXT,
	DBLogUserName		VARCHAR(20),
	DBLogDisasterIdList	TEXT
) Type InnoDB CHARSET=utf8;
