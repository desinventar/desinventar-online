#!/bin/sh
# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2008-02-23 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
# Make Backup of Region Database
DB=$1
[ -f ./region.conf ] || exit 0
if [ -n "$DB" ]; then
	# Load table List
	. ./region.conf
	# Invert Table List
	INVTABLES=`perl -e'for($i=$#ARGV; $i>=0; $i--) { print "$ARGV[$i] " }' $DATATABLES`
	for name in $INVTABLES ; do
		#echo "DELETE FROM ${DB}_${name};"
		mysqldump -t di8db ${DB}_${name}
	done
else
	echo "Usage : $0 <dbname>"
fi

