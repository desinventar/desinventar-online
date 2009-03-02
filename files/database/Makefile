SQLITE=sqlite3


all : base.db

# Rebuild base.db database
base.db : base.sql doc-db.sql eve_cau.sql
	touch base.db; \
	/bin/rm -f base.db; \
	sqlite3 base.db < base.sql ; \
	sqlite3 base.db < eve_cau.sql ; \
	sqlite3 base.db < doc-db-clean.sql ; \
	cat doc-db.sql | sed -e 's/\/\*.*\*\///g' | sqlite3 base.db ; \
	sqlite3 base.db < doc-db-fix.sql

clean :
	/bin/rm -f *.db
#