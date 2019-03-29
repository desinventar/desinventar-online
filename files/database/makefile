# Makefile (must be TAB indented)
#
SQLITE=sqlite3
STRINGS=interface_strings.xml

.PHONY : all .FORCE

all: database lang

database: base.db core.db desinventar.db

# Rebuild base.db database
base.db: create-base update-base

create-base: .FORCE
	/bin/rm -f base.db && cp ../../migrations/base.sqlite3 base.db

update-base: doc-db.csv event.csv cause.csv
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

core.db: .FORCE
	/bin/rm -f core.db && cp ../../migrations/core.sqlite3 core.db

desinventar.db: .FORCE
	/bin/rm -f desinventar.db && cp ../../migrations/region.sqlite3 desinventar.db

lang: lang_spa lang_eng lang_por lang_fre

lang_spa: interface_strings.xml
	php ../../scripts/xml2conf.php --file=interface_strings.xml --language=spa > spa.conf && cp spa.conf ../../web/conf/spa.conf

lang_eng: interface_strings.xml
	php ../../scripts/xml2conf.php --file=interface_strings.xml --language=eng > eng.conf && cp eng.conf ../../web/conf/eng.conf

lang_por: interface_strings.xml
	php ../../scripts/xml2conf.php --file=interface_strings.xml --language=por > por.conf && cp por.conf ../../web/conf/por.conf

lang_fre: interface_strings.xml
	php ../../scripts/xml2conf.php --file=interface_strings.xml --language=fre > fre.conf && cp fre.conf ../../web/conf/fre.conf

clean:
	/bin/rm -f *.db *.conf
#
