# Fixes for region database, updates the current database
# structure.
#
# (c) 2007 Corporacion OSSO http://www.osso.org.co
# 2007-04-27 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
ALTER TABLE DatabaseInfo ADD COLUMN PredefEventLastUpdate DATETIME;
ALTER TABLE DatabaseInfo ADD COLUMN PredefCauseLastUpdate DATETIME;
ALTER TABLE Event        ADD COLUMN EventCreationDate     DATETIME;
ALTER TABLE Cause        ADD COLUMN CauseCreationDate     DATETIME;
