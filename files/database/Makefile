SQLITE=sqlite3


all : base.db core.db desinventar.db

# Rebuild base.db database
base.db : base.sql doc-db.csv events.csv causes.csv
	/bin/rm -f base.db; \
	sqlite3 base.db < base.sql ; \
	iconv --from-code=UTF-8 --to-code=WINDOWS-1252 events.csv > eve.csv
	iconv --from-code=UTF-8 --to-code=WINDOWS-1252 causes.csv > cau.csv
	./insertEveCau.pl --file=eve.csv --table=Event --lang=spa | sqlite3 base.db
	./insertEveCau.pl --file=eve.csv --table=Event --lang=eng | sqlite3 base.db
	./insertEveCau.pl --file=cau.csv --table=Cause --lang=spa | sqlite3 base.db
	./insertEveCau.pl --file=cau.csv --table=Cause --lang=eng | sqlite3 base.db
	/bin/rm eve.csv cau.csv
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