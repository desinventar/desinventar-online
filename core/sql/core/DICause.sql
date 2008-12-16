# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2008-05-16 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2008-05-24 Mario A. Yandar <mayandar@desinventar.org>
DROP TABLE IF EXISTS DICause;
CREATE TABLE DICause (
	CauseId            VARCHAR(50) NOT NULL,
	CauseLocalName     VARCHAR(50),
	CauseLocalDesc     TEXT,
	CauseLangCode      VARCHAR(10) NOT NULL,
	CauseCreationDate  DATETIME,
	CauseLastUpdate    DATETIME,
	CauseDI6Name       VARCHAR(30)
) Type InnoDB CHARSET=utf8;
