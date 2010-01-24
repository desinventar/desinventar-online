DROP TABLE IF EXISTS GeoCarto;
CREATE TABLE 'GeoCarto' ( 
GeographyId VARCHAR(100), 
GeoLevelId INTEGER NOT NULL, 
RegionId VARCHAR(50), 
SyncRecord DATETIME, 
GeoLevelLayerFile VARCHAR(50) NULL, 
GeoLevelLayerName VARCHAR(50) NULL, 
GeoLevelLayerCode VARCHAR(50) NULL, 
PRIMARY KEY('GeographyId','GeoLevelId')
);
insert into GeoCarto select '', GeoLevelId, '', SyncRecord, GeoLevelLayerFile, GeoLevelLayerName, GeoLevelLayerCode from GeoLevel ;