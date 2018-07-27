#!/bin/bash
#
LIQUIBASE="CLASSPATH=\"/usr/local/liquibase/*.jar\" && /usr/local/liquibase/liquibase"
while getopts ":c:d:f:l:" opt; do
  case $opt in
    c)
      CMD=$OPTARG
      ;;
    d)
      DATABASE=$OPTARG
      ;;
    f)
      FILE=$OPTARG
      ;;
    l)
      CHANGELOG=$OPTARG
      ;;
  esac
done

OPTS="--logLevel=warning --changeLogFile=${CHANGELOG} --url=jdbc:${DATABASE}:${FILE} ${CMD}"

#EXEC="docker run -it -v `pwd`:/opt/app desinventar/liquibase bash -c \"liquibase ${OPTS}\""
EXEC="liquibase ${OPTS}"
bash -c "${EXEC}"
