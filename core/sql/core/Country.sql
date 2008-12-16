# DesInventar8
# http://www.desinventar.org
# (c) 1999-2007 Corporacion OSSO
#
# 2007-01-08 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
DROP TABLE IF EXISTS Country;
CREATE TABLE Country (
	CountryIsoCode    VARCHAR(10) UNIQUE NOT NULL,
		PRIMARY KEY(CountryIsoCode),
	CountryIsoName    VARCHAR(100),
	CountryName       VARCHAR(50),
	CountryContinent  VARCHAR(20),
	CountryFlagIcon   VARCHAR(30)	
) Type InnoDB CHARSET=utf8;

INSERT INTO Country VALUES ('', 'Unknown', 'Desconocido', '', '');
