#!/bin/sh
# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2008-02-22 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
# Clear All Records from Region Database
DB=$1
[ -f ./region.conf ] || exit 0
if [ -n "$DB" ]; then
	# Load table List
	. ./region.conf
	# Invert Table List
	INVTABLES=`perl -e'for($i=$#ARGV; $i>=0; $i--) { print "$ARGV[$i] " }' $DATATABLES`
	# First drop tables in reverse order, due to foreign keys
	for name in $INVTABLES ; do
		echo "DELETE FROM ${DB}_${name};"
	done
	# Create tables in order which allow the creation of the foreign keys	
	#for name in $TABLES ; do
	#	mysqldump --add-locks -c $DB $name
	#done
else
	echo "Usage : $0 <dbname>"
fi

