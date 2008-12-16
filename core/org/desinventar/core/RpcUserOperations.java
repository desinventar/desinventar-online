/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

import java.util.Hashtable;
import java.sql.*;
import org.desinventar.core.Util;
import java.util.logging.*;

public class RpcUserOperations {

    public String openUserSession(String sUserName, String sUserPasswd) {
    	String sReturn = "";
    	sReturn = DIServer.SessionList.openUserSession(sUserName, sUserPasswd);
		return sReturn;
	}
	
    public int closeUserSession(String sSessionUUID) {
    	return DIServer.SessionList.closeUserSession(sSessionUUID);
    }
    
    public int awakeUserSession(String sSessionUUID) {
    	return DIServer.SessionList.awakeUserSession(sSessionUUID);
    }

	public String getPerm(String sSessionUUID, String sAuthKey) {
		return Auth.getPerm(sSessionUUID, sAuthKey);
	}
	
	    
	public Hashtable getAllPermsByUser(String sSessionUUID) {
		return Auth.getAllPermsByUser(sSessionUUID);
	}
		
    public Hashtable getAllPermsByRegion(String sSessionUUID) {
    	return Auth.getAllPermsByRegion(sSessionUUID);
    }
    
    // Role Management Functions
	public String getUserRole(String sSessionUUID, String sUserName, String sRegionUUID) {
		Role r = new Role(DIServer.dicoreconn);
		String sRoleId = r.getUserRole(sUserName, sRegionUUID);
		return sRoleId;
	}
	
	public String getSessionRole(String sSessionUUID) {
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		return getUserRole(sSessionUUID, s.sUserName, s.sRegionUUID);
	}
	
	public Hashtable getUserRoleByRegion(String sSessionUUID, String sRegionUUID) {
		Role r = new Role(DIServer.dicoreconn);
		return r.getUserRoleByRegion(sRegionUUID);
	}
	
	public int setUserRole(String sSessionUUID, 
	                       String sUserName, 
	                       String sRegionUUID,
	                       String sRoleId) {
		Role r = new Role(DIServer.dicoreconn);
		return r.setUserRole(sUserName, sRegionUUID, sRoleId);		
	}

	  // Standard User's Functions
	public Hashtable getUsersList(String sSessionUUID, String sUserName) {
		return DIServer.SessionList.getUsersList(sSessionUUID, sUserName);
	}

		// Get Full User's info
	public Hashtable getUsersInfo(String sSessionUUID, String sUserName) {
		return DIServer.SessionList.getUsersInfo(sSessionUUID, sUserName);
	}

  	// Validate User's Password
  public int validateUserPasswd(String sSessionUUID, String sUserPasswd) {
		return DIServer.SessionList.validateUserPasswd(sSessionUUID, sUserPasswd);
  }

	// OBSOLETE: Remember User's Passwd
	public String rememberUserPasswd(String sUserValue) {
		String sReturn = "";
		if (sUserValue.length() > 0) {
			Connection conn = DIServer.dicoreconn;
			try {
				Statement stmt = conn.createStatement();
				String sQuery = "SELECT * FROM Users WHERE " +
				                " UserName='" + sUserValue + "' OR" +
				                " UserEMail='" + sUserValue + "'";
					DIServer.logger.finer(sQuery);
					ResultSet rs = stmt.executeQuery(sQuery);
					while (rs.next()) {
						sReturn = Util.getStringValueFromRecordSet(rs,"UserPasswd");
					}
					rs.close();
					stmt.close();
			} catch (SQLException e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : rememberUserPasswd", e);
			}
		}
		return sReturn;
	}
} // RpcUserOperations
