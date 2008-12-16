/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

import java.util.Hashtable;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.Statement;
import org.desinventar.core.Util;
import java.util.logging.*;

public class RpcRegionOperations {
	public int acquireDatacardLock(String sSessionUUID, String sDatacardUUID) {
		String sUUID;
		int iAnswer = 1;
		sUUID = DIServer.myDatacardLockList.addDatacardLock(sSessionUUID, sDatacardUUID);
		if (sUUID.equals("")) {
			iAnswer = -1;
		}
		return iAnswer;
	}
	
	public int releaseDatacardLock(String sSessionUUID, String sDatacardUUID) {
		DIServer.myDatacardLockList.removeDatacardLock(sSessionUUID, sDatacardUUID);
		return 0;
	}
	
	public boolean isDatacardLocked(String sSessionUUID, String sDatacardUUID) {
		boolean bAnswer = DIServer.myDatacardLockList.isDatacardLocked(sSessionUUID, sDatacardUUID);
		return bAnswer;
	}
	
	public int createRegion(String sSessionUUID, Hashtable oMyData) {
		DIRegionInfo o;
		int iReturn = Constants.ERR_NO_ERROR;
		o = DIRegionInfo.fromHashtable(sSessionUUID, oMyData);
		o.sRegionStructLastUpdate = Util.getNowDateTimeString();		
		iReturn = o.insertIntoDB();
		iReturn = Constants.ERR_NO_ERROR;
		if (iReturn > 0) {
			iReturn = o.saveToDB();
		} else {
			// Object already exists, cannot insert another
			iReturn = Constants.ERR_OBJECT_EXISTS;
		}
		if (iReturn > 0) {
			Region.createRegionStructure(sSessionUUID, o.sRegionUUID);
		}
		return iReturn;
	}
	public int dropRegion(String sSessionUUID, String sRegionUUID) {
		int iReturn = Constants.ERR_NO_ERROR;
		Region.dropRegionTables(sSessionUUID, sRegionUUID);
		return iReturn;
	}
	
	//public String queryDatacardLock(String sSessionUUID, String sDatacardUUID) {
	//	return DIServer.myDatacardLockList.isDatacardLocked(sDatacardUUID);
	//}
	
	public Hashtable getCountryList(String sSessionUUID) {
		Hashtable ht = null;
		if (sSessionUUID.length() > 0) {
			ht = new Hashtable();
			try {
				Connection conn = DIServer.dicoreconn;
				Statement stmt = conn.createStatement();
				ResultSet rs = stmt.executeQuery("SELECT * FROM Country WHERE CountryIsoCode!='' ORDER BY CountryName");
				while (rs.next()) {
					ht.put(Util.getStringValueFromRecordSet(rs,"CountryIsoCode"),Util.getStringValueFromRecordSet(rs,"CountryName"));
				} // while
				rs.close();
				stmt.close();
			} catch (Exception e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : getCountryList", e);
			}
		}
		return ht;
	}

	public Hashtable getCountryByCode(String sSessionUUID, String sCountryIsoCode) {
		Hashtable ht;
		ht = new Hashtable();
		try {
			Connection conn = DIServer.dicoreconn;
			Statement stmt = conn.createStatement();
			ResultSet rs = stmt.executeQuery("SELECT * FROM Country WHERE CountryIsoCode='" + sCountryIsoCode + "'");
			while (rs.next()) {
				ht.put(Util.getStringValueFromRecordSet(rs,"CountryName"),Util.getStringValueFromRecordSet(rs,"CountryIsoName"));
			} // while
			rs.close();
			stmt.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : getCountryByCode", e);
			e.printStackTrace();
		}		
		return ht;
	}

	public Hashtable getRegionList(String sSessionUUID, String sCountryISOCode) {
		String sQuery = "SELECT * FROM Region";
		Hashtable ht;
		ht = new Hashtable();
		try {
			Connection conn = DIServer.dicoreconn;
			if (sCountryISOCode.length() > 0) {
				sQuery = sQuery + " WHERE CountryIsoCode='" + sCountryISOCode + "'";
			}
			Statement stmt = conn.createStatement();
			ResultSet rs = stmt.executeQuery(sQuery);			
			while (rs.next()) {
				ht.put(Util.getStringValueFromRecordSet(rs,"RegionUUID"),Util.getStringValueFromRecordSet(rs,"RegionLabel"));
			} // while
			rs.close();
			stmt.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : getRegionList", e);
			e.printStackTrace();						
		}		
		return ht;
	}

	public Hashtable getRegionByID(String sSessionUUID, String sRegionUUID, String sRegionField) {
		if (!(sRegionField.equalsIgnoreCase("RegionLabel") || 
				sRegionField.equalsIgnoreCase("RegionActive") || 
				sRegionField.equalsIgnoreCase("RegionPublic"))) {
			sRegionField = "RegionLabel";
		}
		String sQuery = "SELECT RegionUUID, " + sRegionField + " as myField FROM Region";
		Hashtable ht;
		ht = new Hashtable();
		try {
			Connection conn = DIServer.dicoreconn;
			// Get only region
			if (sRegionUUID.length() > 0) {
				sQuery = sQuery + " WHERE RegionUUID='" + sRegionUUID + "'";
			}
			Statement stmt = conn.createStatement();
			ResultSet rs = stmt.executeQuery(sQuery);
			String sKey;
			String sVal;
			while (rs.next()) {
				sKey = Util.getStringValueFromRecordSet(rs,"RegionUUID");
				sVal = Util.getStringValueFromRecordSet(rs,"myField");
				ht.put(sKey, sVal);
			} // while
			rs.close();
			stmt.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : getRegionByID", e);
			e.printStackTrace();						
		}		
		return ht;
	}

	public int openRegion(String sSessionUUID, String sRegionUUID) {
		int iReturn = Constants.ERR_UNKNOWN_ERROR;
		iReturn = DIServer.SessionList.openRegion(sSessionUUID, sRegionUUID);
		return iReturn;
	}

	public int closeRegion(String sSessionUUID, String sRegionUUID) {
		return DIServer.SessionList.closeRegion(sSessionUUID, sRegionUUID);
	}
	
	public String getRegionInformation(String sSessionUUID) {
		String encLabel = "";
		String sFileName = "";		
		sFileName="/tmp/di8dbinfo_" + sSessionUUID + "_.db3";
		Region.getRegionInformation(sSessionUUID, sFileName);
		encLabel = Util.encodeFile(sFileName);
		return encLabel;
	}
	
	public String getRegionLastUpdate(String sSessionUUID) {
		Statement stmt = null;
		ResultSet rs = null;
		Connection conn = null;
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		try {
			conn = DIServer.getDbConnection(sSessionUUID);
			if (conn != null) {
				stmt = conn.createStatement();
				rs = stmt.executeQuery("SELECT RegionStructLastUpdate FROM Region WHERE RegionUUID='" + s.sRegionUUID + "'");
				while (rs.next()) {
					return Util.getStringValueFromRecordSet(rs,"RegionStructLastUpdate");
				}
			} // if conn != null
			rs.close();
			stmt.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "Error in Database", e);
			e.printStackTrace();
		}
		return null;
	}

	public Hashtable getLogList(String sSessionUUID) {
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		String sQuery = "";
		Hashtable ht;
		ht = new Hashtable();
		if ( (s != null) && 
		     (s.sRegionUUID.length() > 0) ) {
			try {
				Connection conn = DIServer.getDbConnection(sSessionUUID);
				Statement stmt = conn.createStatement();
				sQuery = "SELECT * FROM " + s.sRegionUUID + "_DatabaseLog ORDER BY DBLogDate DESC";
				ResultSet rs = stmt.executeQuery(sQuery);
				String sKey;
				String sVal;
				while (rs.next()) {
					sKey = Util.getStringValueFromRecordSet(rs,"DBLogDate");
					sVal = Util.getStringValueFromRecordSet(rs,"DBLogType") + "|" +
								 Util.getStringValueFromRecordSet(rs,"DBLogNotes");
					ht.put(sKey, sVal);
				} // while
				rs.close();
				stmt.close();
			} catch (Exception e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : getLogList", e);
				e.printStackTrace();
			}
		}
		return ht;
	}


	/* Validate import File, doesn't import anything, just return
	   lines with errors */
	public Hashtable validateFromCSV(String sSessionUUID, 
	                                 String sFileName, int iDataType) {
		return DIImport.validateFromCSV(sSessionUUID, sFileName, iDataType);
	}
	/* Import File, ignore lines with errors */
	public Hashtable importFromCSV(String sSessionUUID,
	                               String sFileName, int iDataType) {
		return DIImport.importFromCSV(sSessionUUID, sFileName, iDataType);
	}
} // RpcRegionOperations
