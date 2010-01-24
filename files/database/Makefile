SQLITE=sqlite3

all : base.db core.db desinventar.db lang langportal

# Rebuild base.db database
base.db : base.sql doc-db.csv events.csv causes.csv
	/bin/rm -f base.db
	sqlite3 base.db < base.sql
	php insertEveCau.php events.csv spa EVENT | sqlite3 base.db
	php insertEveCau.php events.csv eng EVENT | sqlite3 base.db
	php insertEveCau.php causes.csv spa CAUSE | sqlite3 base.db
	php insertEveCau.php causes.csv eng CAUSE | sqlite3 base.db
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

lang_spa : di8_interface_strings.csv
	./createInterfaceStrings.pl --lang=spa > ../../web/include/spa.conf
lang_eng : di8_interface_strings.csv
	./createInterfaceStrings.pl --lang=eng > ../../web/include/eng.conf
lang_por : di8_interface_strings.csv
	./createInterfaceStrings.pl --lang=por > ../../web/include/por.conf
lang_fre : di8_interface_strings.csv
	./createInterfaceStrings.pl --lang=fre > ../../web/include/fre.conf

langportal : langportal_spa langportal_eng langportal_por langportal_fre

langportal_spa: di8_portal_strings.csv
	./createInterfaceStrings.pl --portal --lang=spa > ../../portal/include/spa.conf
langportal_eng: di8_portal_strings.csv
	./createInterfaceStrings.pl --portal --lang=eng > ../../portal/include/eng.conf
langportal_por: di8_portal_strings.csv
	./createInterfaceStrings.pl --portal --lang=por > ../../portal/include/por.conf
langportal_fre: di8_portal_strings.csv
	./createInterfaceStrings.pl --portal --lang=fre > ../../portal/include/fre.conf

clean :
	/bin/rm -f *.db
#