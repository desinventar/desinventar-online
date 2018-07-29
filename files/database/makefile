# Makefile (must be TAB indented)
#
SQLITE=sqlite3
STRINGS=interface_strings.xml

.PHONY : all .FORCE

all : database lang

database: base.db core.db desinventar.db

# Rebuild base.db database
base.db : create-base update-base

create-base: base.yaml
	/bin/rm -f base.db && touch base.db
	sh liquibase.sh -l base.yaml -d sqlite -f base.db -c update

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

core.db : core.yaml
	/bin/rm -f core.db && touch core.db
	sh liquibase.sh -l core.yaml -d sqlite -f core.db -c update

desinventar.db : desinventar.yaml
	/bin/rm -f desinventar.db && touch desinventar.db
	sh liquibase.sh -l desinventar.yaml -d sqlite -f desinventar.db -c update

lang : spa.conf eng.conf por.conf fre.conf

spa.conf : interface_strings.xml
	php ../../scripts/xml2conf.php interface_strings.xml spa > spa.conf && cp spa.conf ../../web/conf/spa.conf

eng.conf : interface_strings.xml
	php ../../scripts/xml2conf.php interface_strings.xml eng > eng.conf && cp eng.conf ../../web/conf/eng.conf

por.conf : interface_strings.xml
	php ../../scripts/xml2conf.php interface_strings.xml por > por.conf && cp por.conf ../../web/conf/por.conf

fre.conf : interface_strings.xml
	php ../../scripts/xml2conf.php interface_strings.xml fre > fre.conf && cp fre.conf ../../web/conf/fre.conf

clean :
	/bin/rm -f *.db *.conf
#
