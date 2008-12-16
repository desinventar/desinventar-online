# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2008-05-16 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2008-05-24 Mario A. Yandar <mayandar@desinventar.org>
DROP TABLE IF EXISTS DIEvent;
CREATE TABLE DIEvent (
  EventId VARCHAR(50) NOT NULL,
  EventLocalName      VARCHAR(50),
  EventLocalDesc      TEXT,
  EventLangCode       VARCHAR(10) NOT NULL,
  EventCreationDate   DATETIME,
  EventLastUpdate     DATETIME,
  EventDI6Name        VARCHAR(30)
) Type InnoDB CHARSET=utf8;
