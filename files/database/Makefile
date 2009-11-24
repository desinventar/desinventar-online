SQLITE=sqlite3


all : base.db core.db desinventar.db

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
	
clean :
	/bin/rm -f *.db
#