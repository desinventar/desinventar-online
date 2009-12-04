/* CORE.DB - DesInventar8.2
2009-08-19
Schema Version:1.01
*/

DROP TABLE IF EXISTS Region;
CREATE TABLE 'Region' ( 
RegionId VARCHAR(50), 
RegionLabel VARCHAR(200), 
LangIsoCode VARCHAR(3), 
CountryIso VARCHAR(3), 
RegionOrder INTEGER, 
RegionStatus INTEGER, 
RegionLastUpdate DATETIME, 
IsCRegion INTEGER, 
IsVRegion INTEGER, 
PRIMARY KEY('RegionId')
);

DROP TABLE IF EXISTS RegionItem;
CREATE TABLE 'RegionItem' ( 
RegionId VARCHAR(50), 
RegionItem VARCHAR(50), 
RegionQuery VARCHAR(50), 
PRIMARY KEY('RegionId','RegionItem')
);

DROP TABLE IF EXISTS RegionAuth;
CREATE TABLE 'RegionAuth' ( 
UserId VARCHAR(20), 
RegionId VARCHAR(50), 
AuthKey VARCHAR(50), 
AuthValue INTEGER, 
AuthAuxValue VARCHAR(1024), 
PRIMARY KEY('UserId','RegionId','AuthKey')
);

DROP TABLE IF EXISTS Queries;
CREATE TABLE 'Queries' ( 
QueryId VARCHAR(50), 
RegionId VARCHAR(50), 
SessionId VARCHAR(50), 
UserId VARCHAR(20), 
QueryStatus VARCHAR(20), 
QueryDate DATETIME, 
QueryName VARCHAR(100), 
QueryContent TEXT, 
PRIMARY KEY('QueryId')
);

DROP TABLE IF EXISTS User;
CREATE TABLE 'User' ( 
UserId VARCHAR(20), 
UserEMail VARCHAR(100), 
UserPasswd VARCHAR(100), 
UserFullName VARCHAR(100), 
Organization VARCHAR(100), 
CountryIso VARCHAR(3), 
UserCity VARCHAR(50), 
UserCreationDate  DATETIME, 
UserNotes TEXT, 
UserActive INTEGER, 
PRIMARY KEY('UserId')
);

DROP TABLE IF EXISTS UserOption;
CREATE TABLE 'UserOption' ( 
UserId VARCHAR(20), 
OptionKey VARCHAR(50), 
OptionValue VARCHAR(1024), 
OptionAuxValue VARCHAR(1024), 
PRIMARY KEY('UserId','OptionKey')
);

DROP TABLE IF EXISTS UserSession;
CREATE TABLE 'UserSession' ( 
SessionId VARCHAR(50), 
RegionId VARCHAR(50), 
UserId VARCHAR(20), 
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
RecordUpdate DATETIME, 
PRIMARY KEY('SessionId','RecordId')
);

DROP TABLE IF EXISTS SessionValue;
CREATE TABLE 'SessionValue' ( 
SessionId VARCHAR(50), 
Key VARCHAR(50), 
Value NUMERIC(12), 
AuxValue VARCHAR(100), 
PRIMARY KEY('SessionId','Key')
);

/* Set initial values */
INSERT INTO User VALUES ('root', 'root@localhost', '7af39c74ac6d9e68a4323440385cc1ff', 'Portal Administrator', '', '', '', '2008-01-01', '', 1);
INSERT INTO RegionAuth VALUES ('root', '', 'REGION', 5, '');
INSERT INTO RegionAuth VALUES ('root', '', 'USER', 5, '');
INSERT INTO RegionAuth VALUES ('root', '', 'ROLE', 0, 'ADMINPORTAL');

