SQLITE=sqlite3


all : base.db core.db desinventar.db

# Rebuild base.db database
base.db : base.sql doc-db.sql eve_cau.sql
	/bin/rm -f base.db; \
	sqlite3 base.db < base.sql ; \
	sqlite3 base.db < eve_cau.sql ; \
	sqlite3 base.db < doc-db-clean.sql ; \
	cat doc-db.sql | sed -e 's/\/\*.*\*\///g' | sqlite3 base.db ; \
	sqlite3 base.db < doc-db-fix.sql
	./iso3166tosql.pl | sqlite3 base.db

core.db : core.sql
	/bin/rm -f core.db; \
	sqlite3 core.db < core.sql

desinventar.db : region.sql
	/bin/rm -f desinventar.db; \
	sqlite3 desinventar.db < region.sql
	
clean :
	/bin/rm -f *.db
#