# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
DROP TABLE IF EXISTS empty_Event;
CREATE TABLE empty_Event (
	EventId                    VARCHAR(50)        UNIQUE NOT NULL,
		PRIMARY KEY(EventId),
	EventLocalName             VARCHAR(50),
	EventLocalDesc             TEXT,
	EventActive                BOOL NOT NULL DEFAULT True,
	EventPreDefined            BOOL NOT NULL DEFAULT False,
	EventCreationDate          DATETIME
) Type InnoDB CHARSET=utf8;
