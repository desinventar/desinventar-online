#!/bin/sh
# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-12-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
# Dump a Region Database to STDOUT
DB=di8db
[ -f ./core.conf ] || exit 0
if [ -n "$DB" ]; then
	# Load table List
	. ./core.conf
	# Invert Table List
	INVTABLES=`perl -e'for($i=$#ARGV; $i>=0; $i--) { print "$ARGV[$i] " }' $TABLES`
	# First drop tables in reverse order, due to foreign keys
	for name in $INVTABLES ; do
		echo "DROP TABLE IF EXISTS $name;"
	done
	# Create tables in order which allow the creation of the foreign keys	
	for name in $TABLES ; do
		mysqldump --add-locks --complete-insert $DB $name
	done
else
	echo "Usage : $0 <dbname>"
fi

