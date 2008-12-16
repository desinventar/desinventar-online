#!/bin/sh
# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2007-01-19 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
# Drop tables for a specific Region
DB=di8db
PREFIX=$1
[ -f ./region.conf ] || exit 0
if [ -n "$DB" ]; then
	# Load table List
	. ./region.conf
	# Invert Table List
	INVTABLES=`perl -e'for($i=$#ARGV; $i>=0; $i--) { print "$ARGV[$i] " }' $TABLES`
	# First drop tables in reverse order, due to foreign keys
	for name in $INVTABLES ; do
		echo "Dropping table $name"
		echo "DROP TABLE IF EXISTS ${PREFIX}_${name};" | mysql $DB -u di8db --password=di8db
	done
else
	echo "Usage : $0 <dbname>"
fi

