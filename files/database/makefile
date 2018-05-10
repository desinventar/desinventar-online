# Makefile (must be TAB indented)
#
SQLITE=sqlite3
STRINGS=interface_strings.xml

all : databases lang

databases: base.db core.db desinventar.db

base.db : base.sql doc-db.csv event.csv cause.csv
	/bin/rm -f base.db
	$(SQLITE) base.db < base.sql
	php insertEveCau.php event.csv spa EVENT | sqlite3 base.db
	php insertEveCau.php event.csv eng EVENT | sqlite3 base.db
	php insertEveCau.php event.csv fre EVENT | sqlite3 base.db
	php insertEveCau.php event.csv por EVENT | sqlite3 base.db
	php insertEveCau.php cause.csv spa CAUSE | sqlite3 base.db
	php insertEveCau.php cause.csv eng CAUSE | sqlite3 base.db
	php insertEveCau.php cause.csv fre CAUSE | sqlite3 base.db
	php insertEveCau.php cause.csv por CAUSE | sqlite3 base.db
	php createDictionary.php | sqlite3 base.db
	./iso3166tosql.pl | sqlite3 base.db
	./square2sql.pl | sqlite3 base.db

core.db : core.sql
	/bin/rm -f core.db; \
	sqlite3 core.db < core.sql

desinventar.db : region.sql
	/bin/rm -f desinventar.db; \
	sqlite3 desinventar.db < region.sql

lang : spa.conf eng.conf por.conf fre.conf

spa.conf : interface_strings.xml
	php xml2conf.php interface_strings.xml spa > spa.conf && cp spa.conf ../../web/conf/spa.conf

eng.conf : interface_strings.xml
	php xml2conf.php interface_strings.xml eng > eng.conf && cp eng.conf ../../web/conf/eng.conf

por.conf : interface_strings.xml
	php xml2conf.php interface_strings.xml por > por.conf && cp por.conf ../../web/conf/por.conf

fre.conf : interface_strings.xml
	php xml2conf.php interface_strings.xml fre > fre.conf && cp fre.conf ../../web/conf/fre.conf

clean :
	/bin/rm -f *.db *.conf
#
