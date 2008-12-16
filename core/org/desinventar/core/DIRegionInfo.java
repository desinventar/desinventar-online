/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Hashtable;
import java.sql.*;
import java.util.logging.Level;

public class DIRegionInfo extends DIObject {
	public String  sRegionUUID;
	public String  sRegionLabel;
	public String  sCountryIsoCode;
	public String  sRegionStructLastUpdate;
	public boolean bRegionActive;
	public boolean bRegionPublic;
	
	public DIRegionInfo(String sMySessionUUID) {
		setSession(sMySessionUUID, false);
		sRegionUUID = "";
		sRegionLabel = "";
		sCountryIsoCode = "";
		bRegionActive = true;
		bRegionPublic = true;
	} // constructor
	
	public DIRegionInfo(String sMySessionUUID, String sMyId) {
		this(sMySessionUUID);
		sRegionUUID = sMyId;
		sRegionStructLastUpdate = Util.getNowDateTimeString();
	}

	public String getSelectQuery() {
		return "SELECT * FROM Region WHERE RegionUUID='" + sRegionUUID + "'";
	}
	
	public String getInsertQuery() {
		return "INSERT INTO Region (RegionUUID) VALUES ('" + sRegionUUID + "')";
	}
	
	public String getDeleteQuery() {
		return "DELETE FROM Region WHERE RegionUUID='" + sRegionUUID + "'";
	}
	
	public String getUpdateQuery() {
		return "UPDATE Region SET "        +
		       " RegionLabel='"            + sRegionLabel            + "', " +
		       " CountryIsoCode='"         + sCountryIsoCode         + "', " +
		       " RegionStructLastUpdate='" + sRegionStructLastUpdate + "', " + 
		       " RegionActive="            + bRegionActive           + ",  " +
		       " RegionPublic="            + bRegionPublic           + "   " +
		       " WHERE RegionUUID = '"     + sRegionUUID             + "'";
	}
	
	public Hashtable toHashtable() {
		Hashtable result = new Hashtable();
		result.put("RegionUUID"            , sRegionUUID);
		result.put("RegionLabel"           , sRegionLabel);
		result.put("CountryIsoCode"        , sCountryIsoCode);
		result.put("RegionStructLastUpdate", sRegionStructLastUpdate);
		result.put("RegionActive"          , new Boolean(bRegionActive));
		result.put("RegionPublic"          , new Boolean(bRegionPublic));
		return result;		
	} // toHashtable
	
	public static DIRegionInfo fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIRegionInfo e;
		e = new DIRegionInfo(sMySessionUUID);
		e.sRegionUUID             = (String)oMyData.get("RegionUUID");
		e.sRegionLabel            = (String)oMyData.get("RegionLabel");
		e.sCountryIsoCode         = (String)oMyData.get("CountryIsoCode");
		e.sRegionStructLastUpdate = (String)oMyData.get("RegionStructLastUpdate");
		e.bRegionActive           = ((Boolean)oMyData.get("RegionActive")).booleanValue();
		e.bRegionPublic           = ((Boolean)oMyData.get("RegionPublic")).booleanValue();
		return e;
	}

	public int setFields(ResultSet rs) {
		int iReturn = Constants.ERR_NO_ERROR;
		try {
			sRegionUUID             = Util.getStringValueFromRecordSet(rs,"RegionUUID");
			sRegionLabel            = Util.getStringValueFromRecordSet(rs,"RegionLabel");
			sCountryIsoCode         = Util.getStringValueFromRecordSet(rs,"CountryIsoCode");
			sRegionStructLastUpdate = Util.getStringValueFromRecordSet(rs,"RegionStructLastUpdate");
			bRegionActive           = rs.getBoolean("RegionActive");
			bRegionPublic           = rs.getBoolean("RegionPublic");
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "DIRegionInfo::setFields : ERROR : " + sRegionUUID, e);
			iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
		}
		return iReturn;
	} 

	public String getPermPrefix() {
		return "REGION";
	}
	
	public int validateDelete() {
		int iReturn = Constants.ERR_NO_ERROR;
		// Always return error, don't let anyone delete database info...
		iReturn = Constants.ERR_CONSTRAINT_FAIL;
		return iReturn;
	} // validateDelete()
}
