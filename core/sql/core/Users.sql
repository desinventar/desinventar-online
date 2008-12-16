# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
DROP TABLE IF EXISTS Users;
CREATE TABLE Users (
	UserName        VARCHAR(20) UNIQUE NOT NULL,
	UserEMail       VARCHAR(100) NOT NULL,
	UserPasswd      VARCHAR(100) NOT NULL,
	UserFullName    VARCHAR(100) NOT NULL,
	UserLangCode    VARCHAR(10) NOT NULL,
	UserCountry     VARCHAR(10),
		FOREIGN KEY(UserCountry) REFERENCES Country(CountryISOCode)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	UserCity        VARCHAR(50),
	UserCreationDate  DATETIME,
	UserAllowedIPList TEXT,
	UserNotes         TEXT,
	UserActive        BOOL,
	UserDisplayOrder  TEXT,
	UserImportOrder   TEXT
) Type InnoDB  CHARSET=utf8;

SET NAMES 'utf8';
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('root','root','Portal Administrator','',true);
