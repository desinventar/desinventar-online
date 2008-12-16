#!/bin/sh
# 2008-05-16 Jhon H. Caicedo <jhcaiced@desinventar.org>
# Fixes some old database tables, create Stat fields in Disaster tables
#
DBPREFIX=$1
QUERY="alter table $1_Disaster add column EffectPeopleDeadStat numeric(11,0),
add column EffectPeopleMissingStat INT,
add column EffectPeopleInjuredStat INT,
add column EffectPeopleHarmedStat INT,
add column EffectPeopleAffectedStat INT,
add column EffectPeopleEvacuatedStat  INT,
add column EffectPeopleRelocatedStat INT,
add column EffectHousesDestroyedStat INT,
add column EffectHousesAffectedStat INT"
echo $QUERY
