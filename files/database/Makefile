SQLITE=sqlite3

all : base.db core.db desinventar.db lang langportal

# Rebuild base.db database
base.db : base.sql doc-db.csv event.csv cause.csv
	/bin/rm -f base.db
	sqlite3 base.db < base.sql
	php insertEveCau.php event.csv spa EVENT | sqlite3 base.db
	php insertEveCau.php event.csv eng EVENT | sqlite3 base.db
	php insertEveCau.php event.csv fre EVENT | sqlite3 base.db
	php insertEveCau.php event.csv por EVENT | sqlite3 base.db
	php insertEveCau.php cause.csv spa CAUSE | sqlite3 base.db
	php insertEveCau.php cause.csv eng CAUSE | sqlite3 base.db
	php insertEveCau.php cause.csv fre CAUSE | sqlite3 base.db
	php insertEveCau.php cause.csv por CAUSE | sqlite3 base.db
	./createDictionary.php | sqlite3 base.db
	./iso3166tosql.pl | sqlite3 base.db
	./square2sql.pl | sqlite3 base.db

core.db : core.sql
	/bin/rm -f core.db; \
	sqlite3 core.db < core.sql

desinventar.db : region.sql
	/bin/rm -f desinventar.db; \
	sqlite3 desinventar.db < region.sql

lang : lang_spa lang_eng lang_por lang_fre

lang_spa : interface_strings.xml
	./xml2conf.php interface_strings.xml spa > ../../web/conf/spa.conf
lang_eng : interface_strings.xml
	./xml2conf.php interface_strings.xml eng > ../../web/conf/eng.conf
lang_por : interface_strings.xml
	./xml2conf.php interface_strings.xml por > ../../web/conf/por.conf
lang_fre : interface_strings.xml
	./xml2conf.php interface_strings.xml fre > ../../web/conf/fre.conf

clean :
	/bin/rm -f *.db
#