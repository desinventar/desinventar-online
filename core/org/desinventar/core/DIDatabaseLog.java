/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Hashtable;
import java.sql.*;
import java.util.logging.Level;

public class DIDatabaseLog extends DIObject {
	public String sDBLogDate;
	public String sDBLogType;
	public String sDBLogNotes;
	public String sDBLogUserName;
	public String sDBLogDisasterIdList;
	
	public DIDatabaseLog(String sMySessionUUID) {
		setSession(sMySessionUUID, false);
		sDBLogDate = 
		sDBLogType = "";
		sDBLogNotes = "";
		sDBLogUserName = "";
		sDBLogDisasterIdList = "";
	} // constructor
	
	public DIDatabaseLog(String sMySessionUUID, String sMyId) {
		this(sMySessionUUID);
		sDBLogDate = sMyId;
	}

	public String getSelectQuery() {
		return "SELECT * FROM " + sRegionUUID + "_DatabaseLog WHERE DBLogDate='" + sDBLogDate + "'";
	}
	
	public String getInsertQuery() {
		return "INSERT INTO " + sRegionUUID + "_DatabaseLog (DBLogDate) VALUES ('" + sDBLogDate + "')";
	}
	
	public String getDeleteQuery() {
		return "DELETE FROM " + sRegionUUID + "_DatabaseLog WHERE DBLogDate='" + sDBLogDate + "'";
	}
	
	public String getUpdateQuery() {
		return "UPDATE " + sRegionUUID + "_DatabaseLog SET " +
		       " DBLogType='"            + sDBLogType            + "', " +
		       " DBLogNotes='"           + sDBLogNotes           + "', " +
		       " DBLogUserName='"        + sDBLogUserName        + "', " +
		       " DBLogDisasterIdList='"  + sDBLogDisasterIdList  + "' " +
		       " WHERE DBLogDate = '"    + sDBLogDate            + "'";
	}
	
	public Hashtable toHashtable() {
		Hashtable result = new Hashtable();
		result.put("DBLogDate"           , sDBLogDate);
		result.put("DBLogType"           , sDBLogType);
		result.put("DBLogNotes"          , sDBLogNotes);
		result.put("DBLogUserName"       , sDBLogUserName);
		result.put("DBLogDisasterIdList" , sDBLogDisasterIdList);
		return result;
	} // toHashtable
	
	public static DIDatabaseLog fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIDatabaseLog e;
		e = new DIDatabaseLog(sMySessionUUID);
		e.sDBLogDate            = (String)oMyData.get("DBLogDate");
		e.sDBLogType            = (String)oMyData.get("DBLogType");
		e.sDBLogNotes           = (String)oMyData.get("DBLogNotes");
		e.sDBLogUserName        = (String)oMyData.get("DBLogUserName");
		e.sDBLogDisasterIdList  = (String)oMyData.get("DBLogDisasterIdList");
		return e;
	}

	public int setFields(ResultSet rs) {
		int iReturn = Constants.ERR_NO_ERROR;
		try {
			sDBLogDate            = Util.getStringValueFromRecordSet(rs,"DBLogDate");
			sDBLogType            = Util.getStringValueFromRecordSet(rs,"DBLogType");
			sDBLogNotes           = Util.getStringValueFromRecordSet(rs,"DBLogNotes");
			sDBLogUserName        = Util.getStringValueFromRecordSet(rs,"DBLogUserName");
			sDBLogDisasterIdList  = Util.getStringValueFromRecordSet(rs,"DBLogDisasterIdList");
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "DIDatabaseLog::setFields : ERROR : " + sDBLogDate, e);
			iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
		}
		return iReturn;
	}

	public String getPermPrefix() {
		return "DBLOG";
	}
	
	public int validateDelete() {
		int iReturn = Constants.ERR_NO_ERROR;
		// Always return error, don't let anyone delete database log...
		iReturn = Constants.ERR_CONSTRAINT_FAIL;
		return iReturn;
	} // validateDelete()
}
