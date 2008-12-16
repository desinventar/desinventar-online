/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Hashtable;

public class DIGeoLevel extends DIObject {
	public DIGeoLevel() {
		sTableName = "GeoLevel";
		sPermPrefix = "GEOLEVEL";
		sFieldKeyDef = "GeoLevelId/INTEGER";
		sFieldDef    = "GeoLevelName/STRING," +
		               "GeoLevelDesc/STRING," +
		               "GeoLevelLayerFile/STRING," +
		               "GeoLevelLayerCode/STRING," +
		               "GeoLevelLayerName/STRING";
		createFields(sFieldKeyDef,sFieldDef);
	}
	
	public DIGeoLevel(String sMySessionUUID) {
		this();
		setSession(sMySessionUUID, false);
	}
	
	public DIGeoLevel(String sSessionUUID, int iMyId) {
		this(sSessionUUID);
		set("GeoLevelId", new Integer(iMyId));
	}

	public DIGeoLevel(String sSessionUUID, int iMyId, String sMyName) {
		this(sSessionUUID, iMyId);
		set("GeoLevelName", sMyName);
	}

	public String getDeleteQuery() {
		return "DELETE FROM " + sRegionUUID + "_" + sTableName + 
		  " WHERE GeoLevelId=" + getInteger("GeoLevelId") + "";
	}

	public static DIGeoLevel fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIGeoLevel o;
		o = new DIGeoLevel(sMySessionUUID);
		o.copyFromHashtable(oMyData);
		return o;
	}
	
	public int validateInsert() {
		int iReturn = Constants.ERR_NO_ERROR;
		iReturn = validateNotNullInt(iReturn, Constants.ERR_GEOLEVEL_NULL_ID, getInteger("GeoLevelId"));
		iReturn = validateUniqueInt(iReturn, Constants.ERR_GEOLEVEL_DUPLICATED_LEVEL, "GeoLevelId", getInteger("GeoLevelId"));
		iReturn = validateNotNullStr(iReturn, Constants.ERR_GEOLEVEL_NULL_NAME, getString("GeoLevelName"));
		iReturn = validateUniqueStr(iReturn, Constants.ERR_GEOLEVEL_DUPLICATED_NAME, "GeoLevelName", getString("GeoLevelName"));
		return iReturn;
	}

	public int validateDelete() {
		return Constants.ERR_NO_ERROR;
	} //validateDelete
} // DIGeoLevel

