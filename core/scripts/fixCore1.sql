# Fixes for dicore database, updates the current database
# structure.
#
# 2007-04-27 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

ALTER TABLE Country ADD COLUMN CountryFlagIcon VARCHAR(30);
ALTER TABLE Event   ADD COLUMN EventCreationDate DATETIME;
UPDATE Event SET EventCreationDate='2007-01-01';
ALTER TABLE Cause   ADD COLUMN CauseCreationDate DATETIME;
UPDATE Cause SET CauseCreationDate='2007-01-01';
ALTER TABLE RegionAuth ADD COLUMN AuthAuxValue VARCHAR(1024);
ALTER TABLE RegionAuth DROP COLUMN AuthValue;
ALTER TABLE RegionAuth ADD COLUMN AuthValue INT;


