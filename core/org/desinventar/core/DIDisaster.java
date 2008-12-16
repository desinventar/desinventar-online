/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.UUID;
import java.util.Hashtable;

public class DIDisaster extends DIObject {
	public DIDisaster() {
		sTableName = "Disaster";
		sPermPrefix = "DISASTER";
		
		sFieldKeyDef = "DisasterId/STRING";
		sFieldDef    = "DisasterSerial/STRING," +
		               "DisasterBeginTime/DATETIME," +
		               "DisasterGeographyId/STRING," +
		               "DisasterSiteNotes/STRING," +
		               "DisasterLatitude/DOUBLE," +
		               "DisasterLongitude/DOUBLE," +
		               "DisasterSource/STRING," +
		               //
		               "RecordStatus/STRING," +
		               "RecordAuthor/STRING," +
		               "RecordCreation/DATETIME," +
		               "RecordLastUpdate/DATETIME," +
		               //
		               "EventId/STRING," +
		               "EventNotes/STRING," +
		               "EventDuration/INTEGER," +
		               "EventMagnitude/STRING," +
		               // Cause Fields
		               "CauseId/STRING," +
		               "CauseNotes/STRING," +
		               //Effects
		               "EffectPeopleDead/INTEGER," +
		               "EffectPeopleMissing/INTEGER," +
		               "EffectPeopleInjured/INTEGER," +
		               "EffectPeopleHarmed/INTEGER," +
		               "EffectPeopleAffected/INTEGER," +
		               "EffectPeopleEvacuated/INTEGER," +
		               "EffectPeopleRelocated/INTEGER," +
		               "EffectHousesDestroyed/INTEGER," +
		               "EffectHousesAffected/INTEGER," +
		               // Stat Fields
		               "EffectPeopleDeadStat/INTEGER," +
		               "EffectPeopleMissingStat/INTEGER," +
		               "EffectPeopleInjuredStat/INTEGER," +
		               "EffectPeopleHarmedStat/INTEGER," +
		               "EffectPeopleAffectedStat/INTEGER," +
		               "EffectPeopleEvacuatedStat/INTEGER," +
		               "EffectPeopleRelocatedStat/INTEGER," +
		               "EffectHousesDestroyedStat/INTEGER," +
		               "EffectHousesAffectedStat/INTEGER," +
		               // Numeric Fields
		               "EffectLossesValueLocal/DOUBLE," +
		               "EffectLossesValueUSD/DOUBLE," +
		               "EffectRoads/DOUBLE," +
		               "EffectFarmingAndForest/DOUBLE," +
		               "EffectLiveStock/INTEGER," +
		               "EffectEducationCenters/INTEGER," +
		               "EffectMedicalCenters/INTEGER," +
		               // Other Effects
		               "EffectOtherLosses/STRING," +
		               "EffectNotes/STRING," +
		               // Sectors Affected
		               "SectorTransport/INTEGER," +
		               "SectorCommunications/INTEGER," +
		               "SectorRelief/INTEGER," +
		               "SectorAgricultural/INTEGER," +
		               "SectorWaterSupply/INTEGER," +
		               "SectorSewerage/INTEGER," +
		               "SectorEducation/INTEGER," +
		               "SectorPower/INTEGER," +
		               "SectorIndustry/INTEGER," +
		               "SectorHealth/INTEGER," +
		               "SectorOther/INTEGER";

		createFields(sFieldKeyDef,sFieldDef);
		// Create a UUID for each Record
		set("DisasterId", UUID.randomUUID().toString());
		set("RecordCreation", Util.getNowDateTimeString());
		set("RecordLastUpdate", getString("RecordCreation"));
		set("EventId", "OTHER");
		set("EventDuration", new Integer(0));
		set("CauseId", "UNKNOWN");
		
	}
	
	public DIDisaster(String sMySessionUUID) {
		this();
		setSession(sMySessionUUID, false);
	}
	
	public DIDisaster(String sMySessionUUID, String sMyId) {
		this(sMySessionUUID);
		set("DisasterId", sMyId);
	}
	
	public String getDeleteQuery() {
		return "DELETE FROM " + sRegionUUID + "_" + sTableName + " WHERE DisasterId='" + getString("DisasterId") + "'";
	}
	
	public static DIDisaster fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIDisaster o;
		o = new DIDisaster(sMySessionUUID);
		o.copyFromHashtable(oMyData);
		
		/*
		o.iEffectPeopleDeadStat      = o.iEffectPeopleDead;
		if (o.iEffectPeopleDeadStat < 0) { o.iEffectPeopleDeadStat = 0; };
		o.iEffectPeopleMissingStat   = o.iEffectPeopleMissing;
		if (o.iEffectPeopleMissingStat < 0) { o.iEffectPeopleMissingStat = 0;};
		o.iEffectPeopleInjuredStat   = o.iEffectPeopleInjured;
		if (o.iEffectPeopleInjuredStat < 0) { o.iEffectPeopleInjuredStat = 0;};
		o.iEffectPeopleHarmedStat    = o.iEffectPeopleHarmed;
		if (o.iEffectPeopleHarmedStat < 0) { o.iEffectPeopleHarmedStat = 0;};
		o.iEffectPeopleAffectedStat  = o.iEffectPeopleAffected;
		if (o.iEffectPeopleAffectedStat < 0) { o.iEffectPeopleAffectedStat = 0;};
		o.iEffectPeopleEvacuatedStat = o.iEffectPeopleEvacuated;
		if (o.iEffectPeopleEvacuatedStat < 0) { o.iEffectPeopleEvacuatedStat = 0;};
		o.iEffectPeopleRelocatedStat = o.iEffectPeopleRelocated;
		if (o.iEffectPeopleRelocatedStat < 0) { o.iEffectPeopleRelocatedStat = 0;};
		o.iEffectHousesDestroyedStat = o.iEffectHousesDestroyed;
		if (o.iEffectHousesDestroyedStat < 0) { o.iEffectHousesDestroyedStat = 0;};
		o.iEffectHousesAffectedStat  = o.iEffectHousesAffected;
		if (o.iEffectHousesAffectedStat < 0) { o.iEffectHousesAffectedStat = 0;};
		*/
		return o;
	}
	
	// Validate Function, should verify that a record is complete
	// and sound before any attempt to save it into the database
	public int validate() {
		int iReturn = Constants.ERR_NO_ERROR;		
		
		if ( (getString("DisasterId") == null) || (getString("DisasterId") == "") ) {
			iReturn = Constants.ERR_NULL_ID;
		}
		return iReturn;
	}
	
	public boolean validateUniqueId() {
		int iReturn = Constants.ERR_NO_ERROR;
		iReturn = validateUniqueStr(iReturn, Constants.ERR_DISASTER_DUPLICATED_SERIAL, "DisasterSerial", getString("DisasterSerial"));
		return (iReturn > 0);
	}
	
	public int validateEffects() {
		int iReturn = Constants.ERR_NO_ERROR;
		
		if (! ( 
		  ( (getInteger("EffectPeopleDead")      > 0) || (getInteger("EffectPeopleDead")     == -1) ) ||
		  ( (getInteger("EffectPeopleInjured")   > 0) || (getInteger("EffectPeopleInjured")  == -1) ) ||
		  ( (getInteger("EffectPeopleHarmed")    > 0) || (getInteger("EffectPeopleHarmed")   == -1) ) ||
		  ( (getInteger("EffectPeopleAffected")  > 0) || (getInteger("EffectPeopleAffected") == -1) ) ||
		  ( (getInteger("EffectPeopleEvacuated") > 0) || (getInteger("EffectPeopleEvacuated")== -1) ) ||
		  ( (getInteger("EffectPeopleRelocated") > 0) || (getInteger("EffectPeopleRelocated")== -1) ) ||
		  ( (getInteger("EffectHousesDestroyed") > 0) || (getInteger("EffectHousesDestroyed")== -1) ) ||
		  ( (getInteger("EffectHousesAffected")  > 0) || (getInteger("EffectHousesAffected") == -1) ) ||
		  ( getDouble("EffectLossesValueLocal") > 0) ||
		  ( getDouble("EffectLossesValueUSD")   > 0) ||
		  ( getDouble("EffectRoads")            > 0) ||
		  ( getDouble("EffectFarmingAndForest") > 0) ||
		  ( getInteger("EffectLiveStock")        > 0) ||
		  ( getInteger("EffectEducationCenters") > 0) ||
		  ( getInteger("EffectMedicalCenters")   > 0) ||
		  ( getString("EffectOtherLosses").length() > 0) ||
		  ( getString("EffectNotes").length()       > 0) ||
		  ( getInteger("SectorTransport")     == -1) ||
		  ( getInteger("SectorCommunications")== -1) ||
		  ( getInteger("SectorRelief")        == -1) ||
		  ( getInteger("SectorAgricultural")  == -1) ||
		  ( getInteger("SectorWaterSupply")   == -1) ||
		  ( getInteger("SectorSewerage")      == -1) ||
		  ( getInteger("SectorEducation")     == -1) ||
		  ( getInteger("SectorPower")         == -1) ||
		  ( getInteger("SectorIndustry")      == -1) ||
		  ( getInteger("SectorHealth")        == -1) ||
		  ( getInteger("SectorOther")         == -1)
			) ) {
				iReturn = Constants.ERR_DISASTER_NO_EFFECTS;
		}
		return iReturn;
	}
	
	public int validateInsert() {
		int iReturn = Constants.ERR_NO_ERROR;
		iReturn = validateNotNullStr(iReturn, Constants.ERR_DISASTER_NULL_ID, getString("DisasterId"));
		iReturn = validateUniqueStr(iReturn, Constants.ERR_DISASTER_DUPLICATED_ID, "DisasterId", getString("DisasterId"));
		iReturn = validateNotNullStr(iReturn, Constants.ERR_DISASTER_NULL_SERIAL, getString("DisasterSerial"));
		//iReturn = validateUniqueStr(iReturn, Constants.ERR_DISASTER_DUPLICATED_SERIAL, "DisasterSerial", sDisasterSerial);
		iReturn = validateNotNullStr(iReturn, Constants.ERR_DISASTER_NULL_TIME, getString("DisasterBeginTime"));
		  // validar que es minimo un año correcto sDisasterBeginTime
		iReturn = validateNotNullStr(iReturn, Constants.ERR_DISASTER_NULL_SOURCE, getString("DisasterSource"));
		iReturn = validateNotNullStr(iReturn, Constants.ERR_DISASTER_NULL_STATUS, getString("RecordStatus"));
		//iReturn = validateNotNullStr(iReturn, Contants.ERR_DISASTER_NULL_CREATION, sRecordCreation);
		///iReturn = validateNotNullStr(iReturn, Constants.ERR_DISASTER_NULL_LASTUPDATE, sRecordLastUpdate);
		iReturn = validateRefStr(iReturn, Constants.ERR_DISASTER_NO_GEOGRAPHY, "Geography", "GeographyId", getString("DisasterGeographyId"));
		iReturn = validateRefStr(iReturn, Constants.ERR_DISASTER_NO_EVENT, "Event"    , "EventId"    , getString("EventId"));
		iReturn = validateRefStr(iReturn, Constants.ERR_DISASTER_NO_CAUSE, "Cause"    , "CauseId"    , getString("CauseId"));
		if (iReturn == Constants.ERR_NO_ERROR) {
			iReturn = validateEffects();
		}
		return iReturn;
	}
	public int validateDelete() {
		return Constants.ERR_NO_ERROR;
	} //validateDelete
}

