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

lang : lang_spa langportal_spa lang_eng langportal_eng lang_por langportal_por lang_fre langportal_fre

lang_spa : interface_strings.csv
	./createInterfaceStrings.pl --lang=spa > ../../web/conf/spa.conf
lang_eng : interface_strings.csv
	./createInterfaceStrings.pl --lang=eng > ../../web/conf/eng.conf
lang_por : interface_strings.csv
	./createInterfaceStrings.pl --lang=por > ../../web/conf/por.conf
lang_fre : interface_strings.csv
	./createInterfaceStrings.pl --lang=fre > ../../web/conf/fre.conf

langportal : langportal_spa langportal_eng langportal_por langportal_fre

langportal_spa: portal_strings.csv
	./createInterfaceStrings.pl --portal --lang=spa >> ../../web/conf/spa.conf
langportal_eng: portal_strings.csv
	./createInterfaceStrings.pl --portal --lang=eng >> ../../web/conf/eng.conf
langportal_por: portal_strings.csv
	./createInterfaceStrings.pl --portal --lang=por >> ../../web/conf/por.conf
langportal_fre: portal_strings.csv
	./createInterfaceStrings.pl --portal --lang=fre >> ../../web/conf/fre.conf

clean :
	/bin/rm -f *.db
#