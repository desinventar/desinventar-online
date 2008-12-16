/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.BufferedWriter;
import java.util.Hashtable;
import java.util.Set;
import java.util.Iterator;
import java.sql.*;
import com.Ostermiller.util.ExcelCSVParser;
import com.Ostermiller.util.ExcelCSVPrinter;
import java.util.logging.*;

public class UserSessionList extends java.util.Hashtable {
	private static final long serialVersionUID = 1; // Needed for Serialization

	//private static Hashtable<String,Object> DBConnList;
	
	public UserSessionList() {
		//DBConnList  = new Hashtable<String,Object>();
	}
	
	public String openUserSession(String sUserName, String sUserPasswd) {
		String sReturn = "";
		UserSession s = null;
		s = UserSession.createUserSession(sUserName, sUserPasswd);
		if (s != null) {
			s.awakeSession();
			sReturn = s.sSessionUUID;
			put(s.sSessionUUID, s);
			DIServer.logger.fine("openUserSession : " + s.sSessionUUID + " (" + sUserName + "/" + sUserPasswd + ") + Total : " + size());
		} else {
			sReturn = "";
		}
		return sReturn;
	}

	public int closeUserSession(String sSessionUUID) {
		if (containsKey(sSessionUUID)) {
			remove(sSessionUUID);
			DIServer.logger.fine("closeUserSession : " + sSessionUUID + " Total : " + size());
		}
		return 0;
	}

	public int awakeUserSession(String sSessionUUID) {
		int iReturn = Constants.ERR_NO_ERROR;
		UserSession s = (UserSession)get(sSessionUUID);
		if (s != null) {
			iReturn = s.awakeSession();
		}
		return iReturn;
	}

	public int validateUserPasswd(String sSessionUUID, String sUserPasswd) {
		int iReturn = Constants.ERR_NO_ERROR;
		UserSession s = (UserSession)get(sSessionUUID);
		if (s != null) {
			iReturn = s.validateUserPasswd(sUserPasswd);
		}
		return iReturn;
	}

	public Hashtable getUsersList(String sSessionUUID, String sUserName) {
		UserSession s = (UserSession)get(sSessionUUID);
		if (s != null) {
			return s.getUsersList(sUserName);
		}
		return null;
	}

	public Hashtable getUsersInfo(String sSessionUUID, String sUserName) {
		UserSession s = (UserSession)get(sSessionUUID);
		if (s != null) {
			return s.getUsersInfo(sUserName);
		}
		return null;
	}

	public int openRegion(String sSessionUUID, String sRegionUUID) {
		int iReturn = Constants.ERR_UNKNOWN_ERROR;
		UserSession s = null;
		// Validate if sSessionUUID exists...
		if (containsKey(sSessionUUID)) {
			s = (UserSession)DIServer.SessionList.get(sSessionUUID);
			s.sRegionUUID = sRegionUUID;
			DIServer.logger.fine("openRegion : " + sSessionUUID + " Region : " + sRegionUUID);
			iReturn = Constants.ERR_NO_ERROR;
		}
		return iReturn;
	}

	public int closeRegion(String sSessionUUID, String sRegionUUID) {
		DIServer.logger.fine("closeRegion : " + sSessionUUID + " Region : " + sRegionUUID);
		return 0;
	}
	
	public Connection getDbConnection(String sSessionUUID) {
		return DIServer.dicoreconn;
	}
	
	public int saveSessionList(String sFileName) {
		try {
			// Create the printer
			FileOutputStream F = new FileOutputStream(sFileName);
			BufferedWriter output = new BufferedWriter(new OutputStreamWriter(F, "UTF-8"));
			ExcelCSVPrinter ecsvp = new ExcelCSVPrinter(output);
			Set set = keySet();
			Iterator iter = set.iterator();
			String sKey;
			UserSession s;
			while (iter.hasNext()) {
				sKey = (String)iter.next();
				s = (UserSession)get(sKey);
				ecsvp.writeln(new String[]{s.sSessionUUID, 
				                           s.sRegionUUID,
				                           s.sUserName,
				                           s.bValid.toString(),
				                           new Long(s.iStart).toString(),
				                           new Long(s.iUpdate).toString()
				                          });
			}
			ecsvp.close();
		} catch (java.io.IOException e) {
			DIServer.logger.log(Level.SEVERE, "Error while saving session list file", e);
		}
		return Constants.ERR_NO_ERROR;
	}
	
	public int loadSessionList(String sFileName) {
		try {
			UserSession s;
			String[] arrValues;
			FileInputStream fis = new FileInputStream(sFileName);
			ExcelCSVParser parser = new ExcelCSVParser(new InputStreamReader(fis, "UTF-8"));
			while ((arrValues = parser.getLine()) != null) {
				DIServer.logger.finest("Session : " + arrValues[0] + " Region : " + arrValues[1]);
				s = new UserSession();
				s.sSessionUUID = arrValues[0];
				s.sRegionUUID = arrValues[1];
				s.sUserName = arrValues[2];
				s.bValid = new Boolean(true);
				s.iStart = Long.valueOf(arrValues[4]).longValue();
				s.iUpdate = Long.valueOf(arrValues[5]).longValue();
				put(s.sSessionUUID, s);
				if (s.sRegionUUID.length() > 0) {
					openRegion(s.sSessionUUID, s.sRegionUUID);
				}
			}
		} catch (java.io.IOException e) {
			DIServer.logger.log(Level.SEVERE, "Error while saving session list file", e);
		}
		return Constants.ERR_NO_ERROR;
	}
	
	public int awakeConnections() {
		// Do some query on all connections so the mysql timeout
		// is reset (the default timeout of mysql connections is 8 hours
		String sMsg = "";
		
		try {
			// Awake DICORE
			Statement stmt = DIServer.dicoreconn.createStatement();
			ResultSet rs = stmt.executeQuery("SELECT * FROM Users WHERE UserName='XXXXXXXXXX'");
			rs.close();
			stmt.close();
			sMsg = sMsg + "DICORE ";
		} catch (SQLException e) {
			DIServer.logger.log(Level.SEVERE, "Error while awakening database connections", e);
		}

		DIServer.logger.finest("awakeConnections : " + sMsg);
		return 0;
	}

	public int removeUnusedSessions(long iDatacardTimeOut, long iSessionTimeOut) {
		long iCurrentTime;
		long iUnusedTime;
		iCurrentTime = (new java.util.Date()).getTime();
		
		Hashtable ht = new Hashtable();
		
		Set set = keySet();
		Iterator iter = set.iterator();
		String sKey;
		UserSession s;
		/* Search for Unused Sessions and build a list of them */
		while (iter.hasNext()) {
			sKey = (String)iter.next();
			s = (UserSession)get(sKey);
			iUnusedTime = (iCurrentTime - s.iUpdate)/1000;
			// Java times are epoch milliseconds, while iTimeOut are seconds
			if ( iUnusedTime > iDatacardTimeOut) {
				ht.put(sKey, new Long(iUnusedTime));
			}
		}
		set = ht.keySet();
		iter = set.iterator();
		while (iter.hasNext()) {
			sKey = (String)iter.next();
			iUnusedTime = ((Long)ht.get(sKey)).longValue();
			/* Remove locked datacards for this session */
			DIServer.myDatacardLockList.removeDatacardLockBySession(sKey);
			/* Process list by removing sessions from server */
			if (iUnusedTime > iSessionTimeOut) {
				remove(sKey);
			}
		}
		return Constants.ERR_NO_ERROR;
	}
} // UserSessionList
