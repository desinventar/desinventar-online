#!/bin/bash
#
# Use the php linter to validate PHP syntax in source files
#
BINDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd ${BINDIR} && cd ..
FILES=`find src api config web -name "*.php"`
for FILE in ${FILES} ; do
    php -l $FILE 1>/dev/null
done
