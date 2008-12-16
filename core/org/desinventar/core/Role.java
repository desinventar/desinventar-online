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

public class Role {
	Connection conn;
	String sUserName;
	String sRegionUUID;
	String sRoleId;
	
	public Role() {	
		conn = null;
		sRoleId = "";
	}
	public Role(Connection oMyConn) {
		this();
		conn = oMyConn;
	}
	
	public String getUserRole(String sMyUserName, String sMyRegionUUID) {
		String sQuery;
		sUserName = sMyUserName;
		sRegionUUID = sMyRegionUUID;
		sRoleId = "UNKNOWN";
		sQuery = "SELECT * FROM RegionAuth WHERE " +
		                "((UserName='"   + sUserName   + "') OR (UserName='')) AND " +
		                "((RegionUUID='" + sRegionUUID + "') OR (RegionUUID='')) AND " +
		                " AuthKey='ROLE'" + 
		                " ORDER BY UserName,RegionUUID";
		DIServer.logger.finest("Role.getUserRole : " + sQuery);
		try {
			Statement st;
			st = conn.createStatement();
			ResultSet rs;
			rs = st.executeQuery(sQuery);
			while (rs.next()) {
				sRoleId = Util.getStringValueFromRecordSet(rs,"AuthAuxValue");
			}	
			rs.close();
			st.close();
			DIServer.logger.finest("Role.getUserRole : sUserName = " + sUserName + " Role : " + sRoleId);
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : Role.getUserRole : " + sUserName + " Region: " + sRegionUUID);
			e.printStackTrace();			
		}
		return sRoleId;
	}

	public Hashtable getUserRoleByRegion(String sMyRegionUUID) {
		Hashtable ht;
		String sQuery;		
		sUserName = "any";
		sRegionUUID = sMyRegionUUID;
		sRoleId = "UNKNOWN";
		sQuery = "SELECT * FROM RegionAuth WHERE " +
		                "((RegionUUID='"   + sRegionUUID   + "') OR (RegionUUID='')) AND " +
		                " AuthKey='ROLE'" + 
		                " ORDER BY UserName,RegionUUID";
		DIServer.logger.finest("Role.getUserRoleByRegion : " + sQuery);
		ht = new Hashtable();
		try {
			Statement st;
			st = conn.createStatement();
			ResultSet rs;
			rs = st.executeQuery(sQuery);
			while (rs.next()) {
				sUserName = Util.getStringValueFromRecordSet(rs, "UserName");
				sRoleId = Util.getStringValueFromRecordSet(rs,"AuthAuxValue");
				ht.put(sUserName, sRoleId);
			}	
			rs.close();
			st.close();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : Role.getUserRoleByRegion : " + sUserName + " Region: " + sRegionUUID);
			e.printStackTrace();		
		}
		return ht;
	}
	
	public int setUserRole(String sMyUserName, String sMyRegionUUID, String sMyRoleId) {
		Statement st;
		String sQuery;
		
		sUserName = sMyUserName;
		sRegionUUID = sMyRegionUUID;
		sRoleId = sMyRoleId;
		
		try {
			st = conn.createStatement();
			// Delete all permissions for this user in this region
			sQuery = "DELETE FROM RegionAuth WHERE " +
			         " UserName='" + sUserName + "' AND " +
			         " RegionUUID='" + sRegionUUID + "'";
			         //" AND " +
			         //" AuthKey='ROLE'";
			DIServer.logger.finest("Role.setUserRole : " + sQuery);
			st.executeUpdate(sQuery);
			// Create all permissions for this role in this session
			sQuery = "INSERT INTO RegionAuth VALUES (" +
			         "'" + sUserName + "'," +
			         "'" + sRegionUUID + "'," +
			         "'ROLE', 0," +
			         "'" + sMyRoleId + "'" +
			         ")";
			DIServer.logger.finest("Role.setUserRole : " + sQuery);
			st.executeUpdate(sQuery);
			// Add Permissions			
			if (sRoleId.equals("OBSERVER")) {
				setPerm("DISASTER" , 1, "STATUS=ACTIVE");
				setPerm("EVENT"    , 1, "STATUS=ACTIVE");
				setPerm("CAUSE"    , 1, "STATUS=ACTIVE");
				setPerm("GEOGRAPHY", 1, "STATUS=ACTIVE");
				setPerm("GEOLEVEL" , 1, "STATUS=ACTIVE");
				setPerm("DBINFO"   , 1, "");
				setPerm("DBLOG"    , 1, "");
				setPerm("EEFIELD"  , 1, "STATUS=ACTIVE");
			}
			if (sRoleId.equals("USER")) {
				setPerm("DISASTER" , 3, "STATUS=DRAFT,STATUS=READY");
				setPerm("EVENT"    , 1, "STATUS=ACTIVE");
				setPerm("CAUSE"    , 1, "STATUS=ACTIVE");
				setPerm("GEOGRAPHY", 1, "STATUS=ACTIVE");
				setPerm("GEOLEVEL" , 1, "STATUS=ACTIVE");
				setPerm("EEFIELD"  , 1, "STATUS=ACTIVE");
				setPerm("DBINFO"   , 1, "");
				setPerm("DBLOG"    , 3, "");
			}
			if (sRoleId.equals("SUPERVISOR")) {
				setPerm("DISASTER" , 4, "STATUS=DRAFT,STATUS=READY");
				setPerm("EVENT"    , 1, "STATUS=ACTIVE");
				setPerm("CAUSE"    , 1, "STATUS=ACTIVE");
				setPerm("GEOGRAPHY", 1, "STATUS=ACTIVE");
				setPerm("GEOLEVEL" , 1, "STATUS=ACTIVE");
				setPerm("EEFIELD"  , 1, "STATUS=ACTIVE");
				setPerm("DBINFO"   , 1, "");
				setPerm("DBLOG"    , 3, "");
			}
			if (sRoleId.equals("ADMINREGION")) {
				setPerm("DISASTER" , 5, "");
				setPerm("EVENT"    , 5, "");
				setPerm("CAUSE"    , 5, "");
				setPerm("GEOGRAPHY", 5, "");
				setPerm("GEOLEVEL" , 5, "");
				setPerm("EEFIELD"  , 5, "");
				setPerm("DBINFO"   , 2, "");
				setPerm("AUTH"     , 2, "");
				setPerm("DBPUBLIC" , 2, "");
				setPerm("DBACTIVE" , 2, "");
				setPerm("DBLOG"    , 5, "");
			}
			if (sRoleId.equals("MINIMAL")) {
				setPerm("USER" 		 , 2, "");
			}
			st.close();
			DIServer.logger.finest("Role.setUserRole : sUserName = " + sUserName + "Region : " + sRegionUUID + " Role : " + sRoleId);
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : Role.setUserRole : " + sUserName + " Region: " + sRegionUUID);
			e.printStackTrace();			
		}
		return 0;
	}
	public void setPerm(String sAuthKey, int iValue, String sAuxValue) {
		Statement st;
		String sQuery;
		try {
			st = conn.createStatement();
			sQuery = "INSERT INTO RegionAuth VALUES (" +
				"'" + sUserName + "','" + sRegionUUID + "'," +
				"'" + sAuthKey + "'," + new Integer(iValue).toString() + ",'" + sAuxValue + "')";
			DIServer.logger.finest("Role.setPerm : " + sQuery);
			st.executeUpdate(sQuery);
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : Role.setPerm : " + sUserName + " Region: " + sRegionUUID);
			e.printStackTrace();			
		}
	}
} // Role
