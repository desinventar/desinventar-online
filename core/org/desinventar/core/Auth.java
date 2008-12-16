/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Hashtable;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.Statement;
import org.desinventar.core.Util;
import java.util.logging.Level;

public class Auth {
	Connection conn;
	public Auth() {	
		conn = null;
	}
	public Auth(Connection oMyConn) {
		this();
		conn = oMyConn;
	}
	
	public static String getUserPerm(String sRegionUUID, 
	                                 String sUserName, 
	                                 String sAuthKey) {
		String sAuthValue = ""; 
		String sQuery;		
		Connection conn = DIServer.dicoreconn;
		sQuery = "SELECT * FROM RegionAuth WHERE " + 
		                "((UserName='"   + sUserName   + "') OR (UserName='')) AND " +
		                "((RegionUUID='" + sRegionUUID + "') OR (RegionUUID='')) AND " +
		                " AuthKey='"    + sAuthKey      + "'" + 
		                " ORDER BY AuthValue";
		DIServer.logger.finest(sQuery);
		try {
			Statement st;
			st = conn.createStatement();
			ResultSet rs;
			rs = st.executeQuery(sQuery);
			while (rs.next()) {
				sAuthValue = Util.getIntValueFromRecordSet(rs, "AuthValue") + "/" +
				             Util.getStringValueFromRecordSet(rs,"AuthAuxValue");
			}	
			rs.close();
			st.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : Auth.getUserPerm : " + sRegionUUID + " User: " + sUserName + " Key: " + sAuthKey, e);
			e.printStackTrace();			
		}
		return sAuthValue;
	}
	public static String getPerm(String sSessionUUID, String sAuthKey) {
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		return getUserPerm(s.sRegionUUID, s.sUserName, sAuthKey);
	}

	/*	
	public static Boolean getBooleanPerm(String sSessionUUID, String sAuthKey) {
		String sPerm = getPerm(sSessionUUID, sAuthKey);
		return (sPerm.length() > 0);
	}
	*/

	public static Hashtable getAllPermsByUser(String sSessionUUID) {
		String sUserName = "";
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		if (s != null) {
			sUserName = s.sUserName;
		} else {
		}
		return getAllPermsGeneric(sUserName, "");
	}
		
	public static Hashtable getAllPermsByRegion(String sSessionUUID) {
		Hashtable ht;
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		if (s.sRegionUUID.length() > 0) {
			ht = getAllPermsGeneric(s.sUserName, s.sRegionUUID);
		} else {
			ht = null;
		}
		return ht;
	}
	
	public static Hashtable getAllPermsGeneric(String sUserName, String sRegionUUID) {
		String sAuthKey;
		String sAuthValue;
		Hashtable ht = new Hashtable();
		Connection conn = DIServer.dicoreconn;
		String sQuery = "SELECT * FROM RegionAuth WHERE " + 
		                "((UserName='"   + sUserName + "') OR (UserName='')) ";
		if (sRegionUUID.length() > 0) {
			if (sRegionUUID.equals("any")) {
			} else {
				sQuery = sQuery + 
					" AND " + 
			        "((RegionUUID='" + sRegionUUID + "') OR (RegionUUID='')) ";
			}
		} //if
		sQuery = sQuery + " ORDER BY AuthKey,AuthValue";
		DIServer.logger.finest(sQuery);
		try {
			Statement st = conn.createStatement();
			ResultSet rs = st.executeQuery(sQuery);
			while (rs.next()) {
				if (sRegionUUID.length() > 0) {
					sAuthKey   = Util.getStringValueFromRecordSet(rs,"AuthKey");
				} else {
					sAuthKey   = Util.getStringValueFromRecordSet(rs,"AuthKey") + "/" +
					             Util.getStringValueFromRecordSet(rs,"RegionUUID");
				}
				sAuthValue = Util.getIntValueFromRecordSet(rs, "AuthValue") + "/" +
				             Util.getStringValueFromRecordSet(rs,"AuthAuxValue");
				if (sAuthValue.length() > 0) {
					ht.put(sAuthKey, sAuthValue);
				}
			}
			rs.close();
			st.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : Auth.getPermsGeneric : " + sUserName + " DB: " + sRegionUUID, e);
		}
		return ht;
	}
} // Auth
