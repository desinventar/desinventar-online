/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Hashtable;
import java.util.UUID;
import java.sql.*;
import java.util.logging.*;

public class UserSession {
	public String  sSessionUUID;// UUID of this session
	public String  sRegionUUID; // UUID of database connection
	public String  sUserName;   // Username of this session
	public Boolean bValid;      // Is this session valid ?
	public long    iStart;      // Timestamp : start of session
	public long    iUpdate;     // Timestamp : last operation on this session
	
	public UserSession() {
		sSessionUUID = "";
		sRegionUUID  = "";
		sUserName    = "";
		iStart       = (new java.util.Date()).getTime();
		iUpdate      = 0;
		bValid       = new Boolean(false);
	}
	
	
	public static UserSession createUserSession(String sMyUserName, String sMyUserPasswd) {
		UserSession session = null;
		session = new UserSession();
		session.sUserName = sMyUserName;
		session.sSessionUUID = UUID.randomUUID().toString();
		if (session.validateSession(DIServer.dicoreconn, sMyUserPasswd) < 0) {
			session = null;
		}
		return session;
	}
	
	// Refresh Session while still active
	public int awakeSession() {
		iUpdate = (new java.util.Date()).getTime();
		return Constants.ERR_NO_ERROR;
	}
	
	// Search for User/Passwd on User table
	public int validateSession(Connection conn, String sMyPasswd) {
		int iReturn = Constants.ERR_NO_ERROR;
		String sPasswd = "";
		try {
			if (sUserName.length() > 0) {
				Statement stmt = conn.createStatement();
				ResultSet rs = stmt.executeQuery("SELECT * FROM Users WHERE UserName='" + sUserName + "'");
				while (rs.next()) {
					sPasswd = Util.getStringValueFromRecordSet(rs,"UserPasswd");
					bValid = new Boolean(false);
					if (sPasswd.equals(sMyPasswd)) {
						bValid = new Boolean(true);
					}
				} 
				rs.close();
				stmt.close();
			} else {
				// Anonymous Session
				bValid = new Boolean(true);
			}
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "UserSession : Can't validate User Session => " + sUserName, e);
			iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
		}
		if (bValid.booleanValue() == false) {
			iReturn = Constants.ERR_INVALID_PASSWD;
		}
		return iReturn;
	}
	
	// Validate User Passwd
	public int validateUserPasswd(String sUserPasswd) {
		int iReturn = Constants.ERR_UNKNOWN_ERROR;
		if (sUserName.length() > 0) {
			Connection conn = DIServer.dicoreconn;
			if (validateSession(conn, sUserPasswd) > 0) {
				iReturn = Constants.ERR_NO_ERROR;
			}
			else {
				iReturn = Constants.ERR_INVALID_PASSWD;
			}
		}
		else {
			iReturn = Constants.ERR_OBJECT_NOT_FOUND;
		}
		return iReturn;
	}

	// Update RegionStructLastUpdate
	public int updateRegionStructLastUpdate() {
		String sRegionStructLastUpdate;
		int iReturn = Constants.ERR_NO_ERROR;
		try {
			Connection conn = DIServer.SessionList.getDbConnection(sSessionUUID);
			sRegionStructLastUpdate = Util.getNowDateTimeString();
			Statement st = conn.createStatement();
			String sQuery = "UPDATE Region SET RegionStructLastUpdate='" + sRegionStructLastUpdate + "' WHERE RegionUUID='" + sRegionUUID  + "'";
			st.executeUpdate(sQuery);
			st.close();
			conn = null;
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : updateRegionStructLastUpdate : " + sSessionUUID + " Region : " + sRegionUUID);
			e.printStackTrace();
		}
		return iReturn;
	}

	// Get list of users. (fix UserActive to Users..)
	public Hashtable getUsersList(String sUserName) {
		String sQuery = "SELECT * FROM Users WHERE UserActive = True";
		if (sUserName.length() > 0)
			sQuery = sQuery + " and UserName='" + sUserName + "'";
		sQuery = sQuery + " ORDER BY UserFullName";
		Hashtable ht = new Hashtable();
		try {
			Connection conn = DIServer.dicoreconn;
			Statement stmt = conn.createStatement();
			ResultSet rs = stmt.executeQuery(sQuery);
			while (rs.next()) {
				ht.put(Util.getStringValueFromRecordSet(rs,"UserName"), Util.getStringValueFromRecordSet(rs,"UserFullName"));
			}
			rs.close();
			stmt.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR: Can't Get User's List", e);
			e.printStackTrace();
		}
		return ht;
	}

	// Get Full list of user's fields. 
	public Hashtable getUsersInfo(String sUserName) {
		String sQuery = "SELECT * FROM Users";
		if (sUserName.length() > 0)
			sQuery = sQuery + " WHERE UserName='" + sUserName + "'";
		sQuery = sQuery + " ORDER BY UserFullName";
		Hashtable ht = new Hashtable();
		try {
			Connection conn = DIServer.dicoreconn;
			Statement stmt = conn.createStatement();
			ResultSet rs = stmt.executeQuery(sQuery);
			String[] val;
			while (rs.next()) {
				val = new String[10];
				val[0] = Util.getStringValueFromRecordSet(rs,"UserEMail");
				val[1] = Util.getStringValueFromRecordSet(rs,"UserPasswd");
				val[2] = Util.getStringValueFromRecordSet(rs,"UserFullName");
				val[3] = Util.getStringValueFromRecordSet(rs,"UserLangCode");
				val[4] = Util.getStringValueFromRecordSet(rs,"UserCountry");
				val[5] = Util.getStringValueFromRecordSet(rs,"UserCity");
				val[6] = Util.getStringValueFromRecordSet(rs,"UserCreationDate");
				val[7] = Util.getStringValueFromRecordSet(rs,"UserAllowedIPList");
				val[8] = Util.getStringValueFromRecordSet(rs,"UserNotes");
				val[9] = Util.getStringValueFromRecordSet(rs,"UserActive");
				ht.put(Util.getStringValueFromRecordSet(rs,"UserName"), val);
			}
			rs.close();
			stmt.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR: Can't Get User's List", e);
			e.printStackTrace();
		}
		return ht;
	}

}
