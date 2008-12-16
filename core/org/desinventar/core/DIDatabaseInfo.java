/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Hashtable;
import java.sql.*;
import java.util.logging.Level;

public class DIDatabaseInfo extends DIObject {
	public String sRegionUUID;
	public String sRegionLabel;
	public String sRegionDesc;
	public String sRegionDescEN;
	public String sRegionLangCode;
	public String sRegionStructLastUpdate;
	public String sPredefEventLastUpdate;
	public String sPredefCauseLastUpdate;
	public String sPeriodBeginDate;
	public String sPeriodEndDate;
	public String sOptionAdminURL;
	public boolean bOptionOutOfPeriod;
	public Double dGeoLimitMinX;
	public Double dGeoLimitMinY;
	public Double dGeoLimitMaxX;
	public Double dGeoLimitMaxY;
	
	public DIDatabaseInfo(String sMySessionUUID) {
		setSession(sMySessionUUID, false);
		sRegionUUID = "";
		sRegionLabel = "";
		sRegionDesc = "";
		sRegionDescEN = "";
		sRegionLangCode = "es";
		sRegionStructLastUpdate = "";
		sPredefEventLastUpdate = Util.getNowDateString();
		sPredefCauseLastUpdate = Util.getNowDateString();
		sPeriodBeginDate = Util.getNowDateString();
		sPeriodEndDate = Util.getNowDateString();
		sOptionAdminURL = "admin@desinventar.org";
		bOptionOutOfPeriod = false;
		dGeoLimitMinX = null;
		dGeoLimitMinY = null;
		dGeoLimitMaxX = null;
		dGeoLimitMaxY = null;
	} // constructor
	
	public DIDatabaseInfo(String sMySessionUUID, String sMyId) {
		this(sMySessionUUID);
		sRegionUUID = sMyId;
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
		return "UPDATE Region SET " +
		       " RegionLabel='"            + sRegionLabel            + "', " +
		       " RegionDesc='"             + sRegionDesc             + "', " +
		       " RegionDescEN='"           + sRegionDescEN           + "', " +
		       " RegionLangCode='"         + sRegionLangCode         + "', " +
		       " RegionStructLastUpdate='" + sRegionStructLastUpdate + "', " +
		       " PredefEventLastUpdate='"  + sPredefEventLastUpdate  + "', " +
		       " PredefCauseLastUpdate='"  + sPredefCauseLastUpdate  + "', " +
		       " PeriodBeginDate='"        + sPeriodBeginDate        + "', " +
		       " PeriodEndDate='"          + sPeriodEndDate          + "', " +
		       " OptionAdminURL='"         + sOptionAdminURL         + "', " +
		       " OptionOutOfPeriod="       + bOptionOutOfPeriod      + ",  " +
		       " GeoLimitMinX="            + dGeoLimitMinX					 + ", " +
		       " GeoLimitMinY="            + dGeoLimitMinY					 + ", " +
		       " GeoLimitMaxX="            + dGeoLimitMaxX					 + ", " +
		       " GeoLimitMaxY="            + dGeoLimitMaxY					 +
		       " WHERE RegionUUID = '"     + sRegionUUID + "'";
	}
	
	public Hashtable toHashtable() {
		Hashtable result = new Hashtable();
		result.put("RegionUUID"            , sRegionUUID);
		result.put("RegionLabel"           , sRegionLabel);
		result.put("RegionDesc"            , sRegionDesc);
		result.put("RegionDescEN"          , sRegionDescEN);
		result.put("RegionLangCode"        , sRegionLangCode);
		result.put("RegionStructLastUpdate", sRegionStructLastUpdate);
		result.put("PredefEventLastUpdate" , sPredefEventLastUpdate);
		result.put("PredefCauseLastUpdate" , sPredefCauseLastUpdate);
		result.put("PeriodBeginDate"       , sPeriodBeginDate);
		result.put("PeriodEndDate"         , sPeriodEndDate);
		result.put("OptionAdminURL"        , sOptionAdminURL);
		result.put("OptionOutOfPeriod"     , new Boolean(bOptionOutOfPeriod));
		result.put("GeoLimitMinX"          , dGeoLimitMinX);
		result.put("GeoLimitMinY"          , dGeoLimitMinY);
		result.put("GeoLimitMaxX"          , dGeoLimitMaxX);
		result.put("GeoLimitMaxY"          , dGeoLimitMaxY);
		return result;		
	} // toHashtable
	
	public static DIDatabaseInfo fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIDatabaseInfo e;
		e = new DIDatabaseInfo(sMySessionUUID);
		e.sRegionUUID             = (String)oMyData.get("RegionUUID");
		e.sRegionLabel            = (String)oMyData.get("RegionLabel");
		e.sRegionDesc             = (String)oMyData.get("RegionDesc");
		e.sRegionDescEN           = (String)oMyData.get("RegionDescEN");
		e.sRegionLangCode         = (String)oMyData.get("RegionLangCode");
		e.sRegionStructLastUpdate = Util.getDateFromHashtable(oMyData,"RegionStructLastUpdate", Util.getNowDateString());
		e.sPredefEventLastUpdate  = Util.getDateFromHashtable(oMyData,"PredefEventLastUpdate",Util.getNowDateString());
		e.sPredefCauseLastUpdate  = Util.getDateFromHashtable(oMyData,"PredefCauseLastUpdate",Util.getNowDateString());
		e.sPeriodBeginDate        = Util.getDateFromHashtable(oMyData,"PeriodBeginDate",Util.getNowDateString());
		e.sPeriodEndDate          = Util.getDateFromHashtable(oMyData,"PeriodEndDate",Util.getNowDateString());
		e.sOptionAdminURL         = (String)oMyData.get("OptionAdminURL");
		e.bOptionOutOfPeriod      = ((Boolean)oMyData.get("OptionOutOfPeriod")).booleanValue();
		e.dGeoLimitMinX           = Util.getDoubleFromHashtable(oMyData, "GeoLimitMinX");
		e.dGeoLimitMinY           = Util.getDoubleFromHashtable(oMyData, "GeoLimitMinY");
		e.dGeoLimitMaxX           = Util.getDoubleFromHashtable(oMyData, "GeoLimitMaxX");
		e.dGeoLimitMaxY           = Util.getDoubleFromHashtable(oMyData, "GeoLimitMaxY");
		return e;
	}

	public int setFields(ResultSet rs) {
		int iReturn = Constants.ERR_NO_ERROR;
		try {
			sRegionUUID             = Util.getStringValueFromRecordSet(rs,"RegionUUID");
			sRegionLabel            = Util.getStringValueFromRecordSet(rs,"RegionLabel");
			sRegionDesc             = Util.getStringValueFromRecordSet(rs,"RegionDesc");
			sRegionDescEN           = Util.getStringValueFromRecordSet(rs,"RegionDescEN");
			sRegionLangCode         = Util.getStringValueFromRecordSet(rs,"RegionLangCode");
			sRegionStructLastUpdate = Util.getStringValueFromRecordSet(rs,"RegionStructLastUpdate");
			sPredefEventLastUpdate  = Util.getStringValueFromRecordSet(rs,"PredefEventLastUpdate");
			sPredefCauseLastUpdate  = Util.getStringValueFromRecordSet(rs,"PredefCauseLastUpdate");
			sPeriodBeginDate        = Util.getStringValueFromRecordSet(rs,"PeriodBeginDate");
			sPeriodEndDate          = Util.getStringValueFromRecordSet(rs,"PeriodEndDate");
			sOptionAdminURL         = Util.getStringValueFromRecordSet(rs,"OptionAdminURL");
			bOptionOutOfPeriod      = rs.getBoolean("OptionOufOfPeriod");
			dGeoLimitMinX           = Util.getDoubleValueFromRecordSet(rs,"GeoLimitMinX");
			dGeoLimitMinY           = Util.getDoubleValueFromRecordSet(rs,"GeoLimitMinY");
			dGeoLimitMaxX           = Util.getDoubleValueFromRecordSet(rs,"GeoLimitMaxX");
			dGeoLimitMaxY           = Util.getDoubleValueFromRecordSet(rs,"GeoLimitMaxY");
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "DIDatabaseInfo::setFields : ERROR : " + sRegionUUID, e);
			iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
		}
		return iReturn;
	} 

	public String getPermPrefix() {
		return "DBINFO";
	}
	
	public int validateDelete() {
		int iReturn = Constants.ERR_NO_ERROR;
		// Always return error, don't let anyone delete database info...
		iReturn = Constants.ERR_CONSTRAINT_FAIL;
		return iReturn;
	} // validateDelete()
}
