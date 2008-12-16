# dicore - Sample Data for Database
SET NAMES 'latin1';

# Clean Previous Data
DELETE FROM Users WHERE UserName != "root";
DELETE FROM Country WHERE CountryIsoCode != "";

# Insert Sample Data
INSERT INTO Country VALUES ('BOL', 'Bolivia, Republic of', 'Bolivia', '', '');
INSERT INTO Country VALUES ('COL', 'Rep�blica de Colombia', 'Colombia', '', '');
INSERT INTO Country VALUES ('ECU', 'Ecuador, Republic of', 'Ecuador', '', '');
INSERT INTO Country VALUES ('PAN', 'Panama, Republic of', 'Panam�', '', '');
INSERT INTO Country VALUES ('PER', 'Peru, Republic of', 'Per�', '', '');
INSERT INTO Country VALUES ('VEN', 'Venezuela, Bolivarian Republic of', 'Venezuela', '', '');

INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('demo','demo','Demo User','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('rvargas','rvargas','Ruben Vargas','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('acampos','acampos','Ana Campos','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('crosales','crosales','Cristina I. Rosales','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('nayjimpe','nayjimpe','Nayibe Jimenez P�rez','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('aveosso','aveosso','Andr�s Vel�squez','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('gebedoya','gebedoya','Geovanny Bedoya','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('aaguila','aaguila','Ana Mar�a Aguilar','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('ereyes','ereyes','Erick Reyes','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('lespino','lespino','Luis Espino','',true);
INSERT INTO Users (UserName,UserPasswd,UserFullName,UserCountry,UserActive) VALUES ('jvergara','jvergara','Jos� Vergara','',true);

INSERT INTO RegionAuth VALUES ('demo', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('rvargas', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('acampos', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('crosales', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('nayjimpe', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('aveosso', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('gebedoya', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('aaguila', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('ereyes', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('lespino', '', 'USER', 2, '');
INSERT INTO RegionAuth VALUES ('jvergara', '', 'USER', 2, '');
