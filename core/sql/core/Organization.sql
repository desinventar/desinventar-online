# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-05-29 Mario A. Yandar <mayandar@inticol.com>

DROP TABLE IF EXISTS Organization;
CREATE TABLE Organization (
	OrgUUID         VARCHAR(50),
	OrgName         VARCHAR(100),
	OrgShortName    VARCHAR(20),
	OrgTelNumber    VARCHAR(20),
	OrgFaxNumber    VARCHAR(20),
	OrgURL          VARCHAR(80),
	OrgAddress      VARCHAR(60),
	OrgCountry      VARCHAR(10),
	OrgLogoIcon     VARCHAR(100),
	OrgActive       BOOLEAN
) Type InnoDB CHARSET=utf8;
