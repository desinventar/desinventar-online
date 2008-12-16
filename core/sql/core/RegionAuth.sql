# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2007-05-29 Mario A. Yandar <mayandar@inticol.com>

DROP TABLE IF EXISTS RegionAuth;
CREATE TABLE RegionAuth (
	UserName             VARCHAR(20) NOT NULL,
	RegionUUID           VARCHAR(20) NOT NULL,
	AuthKey              VARCHAR(50),
	AuthValue            INT,
	AuthAuxValue         VARCHAR(1024)
) Type InnoDB CHARSET=utf8;

SET NAMES 'utf8';
INSERT INTO RegionAuth VALUES ('root', '', 'REGION', 5, '');
INSERT INTO RegionAuth VALUES ('root', '', 'USER', 5, '');
