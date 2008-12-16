#!/bin/sh
# DesInventar8
# http://www.desinventar.org
# (c) 1999-2007 Corporacion OSSO
#
# 2007-01-08 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
# Build an Empty Region Database
DB=di8db
[ -f ./region.conf ] || exit 0
if [ -n "$DB" ]; then
	# Load table List
	. ./region.conf
	# Invert Table List
	INVTABLES=`perl -e'for($i=$#ARGV; $i>=0; $i--) { print "$ARGV[$i] " }' $TABLES`
	# First drop tables in reverse order, due to foreign keys
	for name in $INVTABLES ; do
		echo "Dropping table $name"
		echo "DROP TABLE IF EXISTS ${PREFIX}_$name;" | mysql $DB -u di8db --password=di8db
	done
	# Create tables in order which allow the creation of the foreign keys	
	for name in $TABLES ; do
		echo "Creating table $name"
		mysql $DB -u di8db --password=di8db < $name.sql
	done
else
	echo "Usage : $0 <dbname>"
fi

