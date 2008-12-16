#!/bin/bash
# This script takes a Mysql Script and removes special keywords
# so the same script can be used to create tables in a SQLITE database
cat - | sed \
	-e '/#/d' \
	-e '/PRIMARY KEY/d' \
	-e '/REFERENCES/d' \
	-e '/FOREIGN/d' \
	-e '/ON UPDATE/d' \
	-e '/ON DELETE/d' \
	-e 's/) TYPE InnoDB.*$/);/gi'

#