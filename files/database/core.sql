/* CORE.DB - DesInventar8.2
2009-03-01
*/

DROP TABLE IF EXISTS Region;
CREATE TABLE 'Region' ( 
RegionId VARCHAR(50), 
RegionLabel VARCHAR(200) NOT NULL, 
LangIsoCode VARCHAR(3), 
CountryIso VARCHAR(3), 
RegionOrder INTEGER, 
RegionStatus INTEGER, 
RegionLastUpdate DATETIME, 
IsCRegion INTEGER, 
IsVRegion INTEGER, 
PRIMARY KEY('RegionId')
);

DROP TABLE IF EXISTS CVRegionItem;
CREATE TABLE 'CVRegionItem' ( 
RegionId VARCHAR(50), 
RegionItem VARCHAR(50), 
RegionQuery VARCHAR(50), 
PRIMARY KEY('RegionId','RegionItem')
);

DROP TABLE IF EXISTS RegionAuth;
CREATE TABLE 'RegionAuth' ( 
UserName VARCHAR(20) NOT NULL, 
RegionId VARCHAR(50), 
AuthKey VARCHAR(50), 
AuthValue INTEGER, 
AuthAuxValue VARCHAR(1024), 
PRIMARY KEY('UserName','RegionId','AuthKey')
);

DROP TABLE IF EXISTS Queries;
CREATE TABLE 'Queries' ( 
QueryId VARCHAR(50), 
RegionId VARCHAR(50), 
QueryStatus VARCHAR(10), 
UserName VARCHAR(20), 
QueryDate DATETIME, 
QueryName VARCHAR(100), 
QueryContent TEXT, 
PRIMARY KEY('QueryId')
);

DROP TABLE IF EXISTS User;
CREATE TABLE 'User' ( 
UserName VARCHAR(20) NOT NULL, 
UserEMail VARCHAR(100) NOT NULL, 
UserPasswd VARCHAR(100) NOT NULL, 
UserFullName VARCHAR(100) NOT NULL, 
Organization VARCHAR(100), 
CountryIso VARCHAR(3), 
UserCity VARCHAR(50), 
UserCreationDate  DATETIME, 
UserNotes TEXT, 
UserActive INTEGER, 
PRIMARY KEY('UserName')
);

DROP TABLE IF EXISTS UserOption;
CREATE TABLE 'UserOption' ( 
UserName VARCHAR(20) NOT NULL, 
OptionKey VARCHAR(50), 
OptionValue VARCHAR(1024), 
OptionAuxValue VARCHAR(1024), 
PRIMARY KEY('UserName','OptionKey')
);

DROP TABLE IF EXISTS UserSession;
CREATE TABLE 'UserSession' ( 
SessionId VARCHAR(50), 
RegionId VARCHAR(50), 
UserName VARCHAR(50), 
Valid INTEGER, 
Start DATETIME, 
LastUpdate DATETIME, 
PRIMARY KEY('SessionId')
);

DROP TABLE IF EXISTS UserLockList;
CREATE TABLE 'UserLockList' ( 
SessionId VARCHAR(50), 
TableId VARCHAR(50), 
RecordId VARCHAR(50), 
PRIMARY KEY('SessionId')
);

/* Set initial values */
INSERT INTO User VALUES ('root', 'root@localhost', 'root', 'Portal Administrator', '', '', '', '2008-01-01', '', 1);
INSERT INTO RegionAuth VALUES ('root', '', 'REGION', 5, '');
INSERT INTO RegionAuth VALUES ('root', '', 'USER', 5, '');
