#!/bin/bash
# DesInventar
# Extract release files from a local copy of the repositories
#
#BINDIR=`dirname $0`
BINDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
SRCDIR=${BINDIR}/..
DSTDIR=${HOME}/rpmbuild/SOURCES
DSTFILE1=${DSTDIR}/desinventar-online.tar.gz
EXCLUDE=${BINDIR}/exclude.txt
cd ${SRCDIR}
tar -zcf ${DSTFILE1} api composer.* config files src web \
  --exclude-from=${EXCLUDE} \
  --exclude lib \
  --exclude vendor \
  --exclude portal \
  --exclude config/config_local.php \
  --exclude ./scripts
